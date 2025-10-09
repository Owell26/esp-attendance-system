#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>
#include <Wire.h>
#include <RTClib.h>
#include <LittleFS.h>

#define SS_PIN D4
#define RST_PIN D3
#define BUZZER_PIN D0

MFRC522 mfrc522(SS_PIN, RST_PIN);
RTC_DS3231 rtc;

// --- SoftAP config ---
const char* ap_ssid = "ESP8266_Host";
const char* ap_password = "12345678";
IPAddress serverIP(192,168,4,2); // IP na ibibigay sa computer

// --- RFID tracking ---
const int MAX_TRACKED = 50;
String trackedUIDs[MAX_TRACKED];
unsigned long lastSeenSec[MAX_TRACKED];
int trackedCount = 0;
const unsigned long DUPLICATE_WINDOW_SEC = 60UL;

// --- Offline log ---
void saveOffline(const String &uid, const String &timestamp){
  File file = LittleFS.open("/offline_logs.txt", "a");
  if(file){
    file.println(uid + "," + timestamp);
    file.close();
    Serial.println("Saved offline: " + uid + " | " + timestamp);
  } else Serial.println("Failed to open offline log file.");
}

// --- Buzzer ---
void beep(int times, int duration_ms=150, int pause_ms=150){
  for(int i=0;i<times;i++){
    digitalWrite(BUZZER_PIN,HIGH);
    delay(duration_ms);
    digitalWrite(BUZZER_PIN,LOW);
    delay(pause_ms);
  }
}

// --- Duplicate helper ---
int findUIDIndex(const String &uid){
  for(int i=0;i<trackedCount;i++) if(trackedUIDs[i]==uid) return i;
  return -1;
}
void updateUIDTimestamp(const String &uid, unsigned long nowSec){
  int idx = findUIDIndex(uid);
  if(idx>=0) lastSeenSec[idx]=nowSec;
  else if(trackedCount<MAX_TRACKED){
    trackedUIDs[trackedCount]=uid;
    lastSeenSec[trackedCount]=nowSec;
    trackedCount++;
  }
}

void syncOfflineLogs() {
  if (!LittleFS.exists("/offline_logs.txt")) return;

  File file = LittleFS.open("/offline_logs.txt", "r");
  if (!file) return;

  Serial.println("🔁 Syncing offline logs...");
  File temp = LittleFS.open("/temp_logs.txt", "w");
  
  WiFiClient client;
  HTTPClient http;

  while (file.available()) {
    String line = file.readStringUntil('\n');
    line.trim();
    if (line.length() == 0) continue;

    int commaIndex = line.indexOf(',');
    if (commaIndex < 0) continue;

    String uid = line.substring(0, commaIndex);
    String timestamp = line.substring(commaIndex + 1);

    String url = "http://192.168.4.2/AttendanceSystem/save_rfid.php"; // static server
    String postData = "uid=" + uid + "&timestamp=" + timestamp;

    http.begin(client, url);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    int code = http.POST(postData);
    http.end();

    if (code > 0) {
      Serial.println("Synced: " + uid + " | " + timestamp);
    } else {
      Serial.println("Failed to sync: " + uid);
      temp.println(line); // keep the line if failed
    }
  }

  file.close();
  temp.close();

  // Replace old offline file with remaining unsynced lines
  LittleFS.remove("/offline_logs.txt");
  if (LittleFS.exists("/temp_logs.txt")) LittleFS.rename("/temp_logs.txt", "/offline_logs.txt");

  Serial.println("✅ Offline sync done!");
}

void setup(){
  Serial.begin(115200);
  SPI.begin();
  mfrc522.PCD_Init();
  Wire.begin(D2,D1);
  pinMode(BUZZER_PIN,OUTPUT);

  if(!LittleFS.begin()) Serial.println("LittleFS failed!");
  if(!rtc.begin()) Serial.println("RTC not found!");
  else if(rtc.lostPower()) rtc.adjust(DateTime(F(__DATE__),F(__TIME__)));

  // --- Start SoftAP ---
  WiFi.softAP(ap_ssid, ap_password);
  Serial.println("ESP8266 SoftAP started!");
  Serial.print("ESP AP IP: "); Serial.println(WiFi.softAPIP());
  Serial.print("Please set computer IP to "); Serial.println(serverIP);

  Serial.println("Waiting for RFID...");
}

bool wasClientConnected = false;   // tracks if a client is connected
bool wasServerAvailable = false;   // tracks if server was reachable

void loop(){
  // --- RFID SCAN ---
  if(mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()){
    String uid="";
    for(byte i=0;i<mfrc522.uid.size;i++){
      if(mfrc522.uid.uidByte[i]<0x10) uid+="0";
      uid+=String(mfrc522.uid.uidByte[i],HEX);
    }
    uid.toUpperCase();

    unsigned long nowSec=millis()/1000UL;
    int idx=findUIDIndex(uid);
    if(idx>=0 && (nowSec-lastSeenSec[idx])<DUPLICATE_WINDOW_SEC){
      Serial.println("Duplicate detected.");
      beep(3);
      return;
    }

    DateTime now=rtc.now();
    char buf[25];
    sprintf(buf,"%04d-%02d-%02d %02d:%02d:%02d",
            now.year(),now.month(),now.day(),
            now.hour(),now.minute(),now.second());
    String timestamp=String(buf);
    Serial.println("UID: "+uid+" | Time: "+timestamp);

    WiFiClient client;
    HTTPClient http;
    http.begin(client, "http://192.168.4.2/AttendanceSystem/save_rfid.php");
    http.addHeader("Content-Type","application/x-www-form-urlencoded");
    String data = "uid="+uid+"&timestamp="+timestamp;
    int code = http.POST(data);
    http.end();
    if(code>0){
        Serial.println("Sent to server successfully!");
        updateUIDTimestamp(uid, nowSec);
        beep(1);
    } else {
        Serial.println("Server unreachable, saving offline...");
        saveOffline(uid, timestamp);
        beep(3);
    }

    delay(2000); // debounce
  }

  int connectedClients = WiFi.softAPgetStationNum();
  if (connectedClients > 0) {
      // Client is connected
      WiFiClient client;
      HTTPClient http;
      http.begin(client, "http://192.168.4.2/AttendanceSystem/save_rfid.php");
      int code = http.GET();
      http.end();

      if (code > 0) {
          // Server is reachable
          if (!wasServerAvailable) {
              Serial.println("✅ Server is now reachable! Syncing offline logs...");
              syncOfflineLogs();
              wasServerAvailable = true;
          }
      } else {
          // Server not reachable
          if (wasServerAvailable) {
              Serial.println("❌ Server became unreachable.");
              wasServerAvailable = false;
          }
      }
  } else {
      // No client connected
      if (wasClientConnected) {
          Serial.println("❌ Client disconnected.");
          wasClientConnected = false;
          wasServerAvailable = false;
      }
  }

}
