#include <ESP8266WiFi.h>
#include <ESP8266WebServer.h>
#include <ESP8266HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>
#include <Wire.h>
#include <RTClib.h>
#include <LittleFS.h>
#include <WiFiClientSecureBearSSL.h>

#define SS_PIN D4
#define RST_PIN D3
#define BUZZER_PIN D0

MFRC522 mfrc522(SS_PIN, RST_PIN);
RTC_DS3231 rtc;

uint32_t chip_id;  // unique numeric ID ng ESP8266
String device_id;

ESP8266WebServer server(80);

// --- SoftAP config ---
const char* ap_ssid = "ESP8266_Host";
const char* ap_password = "12345678";

// --- Server URL (HTTPS) ---
const String serverURL = "https://esp-attendance-system-riha.onrender.com/save_rfid.php";

// --- RFID tracking ---
const int MAX_TRACKED = 50;
String trackedUIDs[MAX_TRACKED];
unsigned long lastSeenSec[MAX_TRACKED];
int trackedCount = 0;
const unsigned long DUPLICATE_WINDOW_SEC = 60UL;

// --- Sync flags ---
String syncSSID = "";
String syncPassword = "";
bool doSync = false;

// --- Helpers ---
String urlencode(const String &str) {
  String encoded = "";
  char c;
  char buf[4];
  for (unsigned int i = 0; i < str.length(); i++) {
    c = str[i];
    if (('a' <= c && c <= 'z') ||
        ('A' <= c && c <= 'Z') ||
        ('0' <= c && c <= '9') ||
        c == '-' || c == '_' || c == '.' || c == '~') {
      encoded += c;
    } else {
      sprintf(buf, "%%%02X", (uint8_t)c);
      encoded += String(buf);
    }
  }
  return encoded;
}

bool saveOffline(String uid, String timestamp) {
  DateTime now = rtc.now();
  unsigned long nowSec = now.unixtime();

  // 🔎 1-minute rule check
  int idx = findUIDIndex(uid);
  if (idx >= 0 && (nowSec - lastSeenSec[idx]) < DUPLICATE_WINDOW_SEC) {
    unsigned long remaining = DUPLICATE_WINDOW_SEC - (nowSec - lastSeenSec[idx]);
    Serial.printf("⚠️ Duplicate punch detected for UID %s — wait %lu seconds.\n", uid.c_str(), remaining);
    return false;  // ❌ Not saved
  }

  // 💾 Save to file
  File file = LittleFS.open("/offline_logs.txt", "a");
  if (file) {
    file.println(uid + "," + timestamp + "," + device_id);
    file.close();
    Serial.println("📝 Saved offline: " + uid + " at " + timestamp);
  } else {
    Serial.println("❌ Failed to open offline log file!");
    return false;
  }

  // 🕒 Update cooldown time
  updateUIDTimestamp(uid, nowSec);
  return true;  // ✅ Saved successfully
}



void beep(int times, int duration_ms = 150, int pause_ms = 150) {
  for (int i = 0; i < times; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(duration_ms);
    digitalWrite(BUZZER_PIN, LOW);
    delay(pause_ms);
  }
}

int findUIDIndex(const String &uid) {
  for (int i = 0; i < trackedCount; i++) if (trackedUIDs[i] == uid) return i;
  return -1;
}

void updateUIDTimestamp(const String &uid, unsigned long nowSec) {
  int idx = findUIDIndex(uid);
  if (idx >= 0) lastSeenSec[idx] = nowSec;
  else if (trackedCount < MAX_TRACKED) {
    trackedUIDs[trackedCount] = uid;
    lastSeenSec[trackedCount] = nowSec;
    trackedCount++;
  }
}

// --- Send record via POST (HTTPS) ---
void sendRecordPOST(const String &uid, const String &timestamp, unsigned long nowSec) {
  std::unique_ptr<BearSSL::WiFiClientSecure> client(new BearSSL::WiFiClientSecure);
  client->setInsecure(); // bypass SSL verification

  HTTPClient http;
  http.begin(*client, serverURL);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  String postData = "uid=" + urlencode(uid) + "&timestamp=" + urlencode(timestamp) + "&device_id=" + urlencode(device_id);

  int code = http.POST(postData);

  Serial.print("📡 HTTP Response code: ");
  Serial.println(code);
  
  if (code > 0) {
    String response = http.getString();
    Serial.println("🖥️ Server response:");
    Serial.println(response);

    if (response.indexOf("✅") >= 0) {
      Serial.println("✅ Synced successfully!");
      beep(1);
    } else {
      Serial.println("⚠️ Server returned error; saving offline.");
      if (saveOffline(uid, timestamp)) {  // ✅ Only show message if truly saved
        Serial.println("📡 Attendance Saved Offline. Sync Later when there is available internet.");
        beep(1);
      } else {
        beep(3);  // duplicate or failed
      }
    }
  } else {
    bool saved = saveOffline(uid, timestamp);
    if (saved) {
      Serial.println("📡 Attendance Saved Offline. Sync Later when there is available internet.");
      beep(1);
    } else {
      beep(3);  // duplicate or failed
    }
  }


  http.end();
}

// --- Sync offline logs via POST (HTTPS) ---
void syncOfflineLogsToServerPOST() {
  if (!LittleFS.exists("/offline_logs.txt")) return;

  File file = LittleFS.open("/offline_logs.txt", "r");
  if (!file) return;

  File temp = LittleFS.open("/temp_logs.txt", "w");
  if (!temp) return;

  while (file.available()) {
    String line = file.readStringUntil('\n');
    line.trim();
    if (line.length() == 0) continue;

    int firstComma = line.indexOf(',');
    int secondComma = line.indexOf(',', firstComma + 1);

    if (firstComma < 0) continue;

    String uid = line.substring(0, firstComma);
    String timestamp;
    String devID = device_id; // default kung wala

    if (secondComma > 0) {
      timestamp = line.substring(firstComma + 1, secondComma);
      devID = line.substring(secondComma + 1);
    } else {
      timestamp = line.substring(firstComma + 1);
    }

    // ✅ Use a single postData variable
    String postData = "uid=" + urlencode(uid) +
                      "&timestamp=" + urlencode(timestamp) +
                      "&device_id=" + urlencode(devID);

    // Create HTTPS client
    std::unique_ptr<BearSSL::WiFiClientSecure> client(new BearSSL::WiFiClientSecure);
    client->setInsecure();
    HTTPClient http;

    http.begin(*client, serverURL);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    int code = http.POST(postData);  // ✅ Use same postData here

    String response = "";
    if (code > 0) response = http.getString();
    http.end();

    Serial.print("📡 Code: ");
    Serial.print(code);
    Serial.print(" | Resp: ");
    Serial.println(response);

    if (code > 0 && response.indexOf("✅") >= 0) {
      // synced successfully
    } else {
      temp.println(line); // keep line if failed
    }

    delay(200);
  }

  file.close();
  temp.close();

  LittleFS.remove("/offline_logs.txt");
  if (LittleFS.exists("/temp_logs.txt")) LittleFS.rename("/temp_logs.txt", "/offline_logs.txt");

  Serial.println("✅ Offline sync done!");
}

// --- Web Server Handlers ---
void handleRoot() {
  String html = R"rawliteral(
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>ESP8266 Attendance Portal</title>
      <style>
        body {
          font-family: 'Segoe UI', Tahoma, sans-serif;
          background: linear-gradient(135deg, #00c853, #b2ff59);
          color: #333;
          text-align: center;
          margin: 0;
          padding: 0;
          min-height: 100vh;
          display: flex;
          flex-direction: column;
          justify-content: center;
          align-items: center;
        }
        h2 {
          color: #000;
          font-size: 2em;
          margin-bottom: 20px;
          text-shadow: 1px 1px 3px rgba(0,0,0,0.2);
        }
        .card {
          background: #fff;
          padding: 25px;
          border-radius: 15px;
          box-shadow: 0 4px 10px rgba(0,0,0,0.2);
          width: 90%;
          max-width: 400px;
        }
        button {
          display: block;
          width: 100%;
          margin: 10px 0;
          padding: 12px;
          font-size: 1em;
          background-color: #00c853;
          color: white;
          border: none;
          border-radius: 8px;
          cursor: pointer;
          transition: background 0.3s ease;
        }
        button:hover {
          background-color: #009624;
        }
        footer {
          margin-top: 20px;
          color: #fff;
          font-size: 0.9em;
        }
      </style>
    </head>
    <body>
      <div class="card">
        <h2>ESP8266 Attendance Portal</h2>
        <form action='/view' method='GET'>
          <button> View Offline Logs</button>
        </form>
        <form action='/sync' method='GET'>
          <button> Sync Attendance Logs</button>
        </form>
      </div>
      <footer>Powered by ESP8266</footer>
    </body>
    </html>
  )rawliteral";

  server.send(200, "text/html", html);
}


void handleViewLogs() {
  String logs = "";
  if (LittleFS.exists("/offline_logs.txt")) {
    File file = LittleFS.open("/offline_logs.txt", "r");
    while (file.available()) {
      logs += file.readStringUntil('\n') + "\n";
    }
    file.close();
  } else {
    logs = "No offline logs found.";
  }

  String html = R"rawliteral(
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Offline Attendance Logs</title>
      <style>
        body {
          font-family: 'Segoe UI', Tahoma, sans-serif;
          background: linear-gradient(135deg, #00c853, #b2ff59);
          color: #333;
          text-align: center;
          margin: 0;
          padding: 20px;
          min-height: 100vh;
          display: flex;
          flex-direction: column;
          align-items: center;
        }
        h2 {
          color: #fff;
          font-size: 2em;
          margin-bottom: 15px;
          text-shadow: 1px 1px 3px rgba(0,0,0,0.2);
        }
        .card {
          background: #fff;
          padding: 20px;
          border-radius: 15px;
          box-shadow: 0 4px 10px rgba(0,0,0,0.2);
          width: 90%;
          max-width: 500px;
          text-align: left;
          overflow-y: auto;
          max-height: 400px;
        }
        pre {
          white-space: pre-wrap;
          word-wrap: break-word;
          font-family: 'Courier New', monospace;
          font-size: 0.95em;
          color: #222;
        }
        a.button {
          display: inline-block;
          margin-top: 20px;
          padding: 10px 20px;
          background-color: #00c853;
          color: white;
          text-decoration: none;
          border-radius: 8px;
          transition: background 0.3s ease;
        }
        a.button:hover {
          background-color: #009624;
        }
        footer {
          margin-top: 25px;
          color: #fff;
          font-size: 0.9em;
        }
      </style>
    </head>
    <body>
      <h2>Offline Attendance Logs</h2>
      <div class="card">
        <pre>)rawliteral";
  
  html += logs;
  
  html += R"rawliteral(</pre>
      </div>
      <a class='button' href='/'> Back to Home</a>
      <footer>Powered by ESP8266</footer>
    </body>
    </html>
  )rawliteral";

  server.send(200, "text/html", html);
}


void handleSyncForm() {
  int n = WiFi.scanNetworks();

  String html = R"rawliteral(
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Sync Attendance Logs</title>
      <style>
        body {
          font-family: 'Segoe UI', Tahoma, sans-serif;
          background: linear-gradient(135deg, #00c853, #b2ff59);
          color: #333;
          text-align: center;
          margin: 0;
          padding: 20px;
          min-height: 100vh;
          display: flex;
          flex-direction: column;
          align-items: center;
        }
        h2 {
          color: #fff;
          font-size: 2em;
          margin-bottom: 20px;
          text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
        }
        .card {
          background: #fff;
          padding: 25px;
          border-radius: 15px;
          box-shadow: 0 4px 10px rgba(0,0,0,0.25);
          width: 90%;
          max-width: 400px;
        }
        select, input[type='password'] {
          width: 100%;
          padding: 10px;
          margin: 10px 0;
          border-radius: 8px;
          border: 1px solid #ccc;
          font-size: 1em;
        }
        input[type='submit'] {
          width: 100%;
          padding: 12px;
          background-color: #00c853;
          color: #fff;
          border: none;
          border-radius: 8px;
          font-size: 1.1em;
          cursor: pointer;
          transition: background 0.3s ease;
        }
        input[type='submit']:hover {
          background-color: #009624;
        }
        a.button {
          display: inline-block;
          margin-top: 20px;
          padding: 10px 20px;
          background-color: #00c853;
          color: white;
          text-decoration: none;
          border-radius: 8px;
          transition: background 0.3s ease;
        }
        a.button:hover {
          background-color: #009624;
        }
        footer {
          margin-top: 25px;
          color: #fff;
          font-size: 0.9em;
        }
      </style>
    </head>
    <body>
      <h2>Sync Attendance Logs</h2>
      <div class="card">
        <form method="POST" action="/sync_submit">
          <label for="ssid"><b>Select Wi-Fi:</b></label>
          <select name="ssid" id="ssid">
  )rawliteral";

  // Append available Wi-Fi networks
  for (int i = 0; i < n; i++) {
    html += "<option value='" + WiFi.SSID(i) + "'>" + WiFi.SSID(i) +
            " (" + String(WiFi.RSSI(i)) + " dBm)</option>";
  }

  html += R"rawliteral(
          </select>
          <label for="password"><b>Password:</b></label>
          <input type="password" id="password" name="password" placeholder="Enter Wi-Fi Password" required>
          <input type="submit" value="Connect and Sync">
        </form>
      </div>
      <a class="button" href="/"> Back to Home</a>
      <footer>Powered by ESP8266</footer>
    </body>
    </html>
  )rawliteral";

  server.send(200, "text/html", html);
}

void handleSyncSubmit() {
  // Check kung may laman SSID at Password
  if (!server.hasArg("ssid") || !server.hasArg("password")) {
    String errorHtml = R"rawliteral(
      <html>
      <head>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <style>
          body {font-family: Poppins, sans-serif; text-align: center; background: #000; color: #fff;}
          .card {max-width: 400px; margin: 50px auto; padding: 25px; background: #111; border-radius: 10px; box-shadow: 0 0 10px rgba(255,0,0,0.4);}
          h2 {color: #ff4c4c;}
          p {color: #ddd;}
          a {text-decoration: none; display: inline-block; margin-top: 20px; color: #00ff88;}
        </style>
      </head>
      <body>
        <div class='card'>
          <h2>❌ Missing SSID or Password</h2>
          <p>Please go back and fill in both fields.</p>
          <a href='/'>← Back to Home</a>
        </div>
      </body>
      </html>
    )rawliteral";

    server.send(400, "text/html", errorHtml);
    return;
  }

  // Get the SSID and password from the form
  syncSSID = server.arg("ssid");
  syncPassword = server.arg("password");

  Serial.println("sync_submit POST received: SSID=" + syncSSID + " Password=" + syncPassword);

  // Try to connect to the Wi-Fi network
  WiFi.begin(syncSSID.c_str(), syncPassword.c_str());
  int retryCount = 0;
  bool connected = false;

  while (WiFi.status() != WL_CONNECTED && retryCount < 20) {
    delay(500);
    Serial.print(".");
    retryCount++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\n✅ Wi-Fi Connected! Syncing now...");
    doSync = true;

    // After successful sync, redirect to /view
    server.sendHeader("Location", "/view");
    server.send(303); // 303 See Other = redirect after POST
  } else {
    Serial.println("\n❌ Failed to connect to Wi-Fi!");

    String errorHtml = R"rawliteral(
      <html>
      <head>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <style>
          body {font-family: Poppins, sans-serif; text-align: center; background: #000; color: #fff;}
          .card {max-width: 450px; margin: 60px auto; padding: 25px; background: #111; border-radius: 12px; box-shadow: 0 0 15px rgba(255,0,0,0.3);}
          h2 {color: #ff4c4c;}
          p {color: #ccc;}
          a {text-decoration: none; display: inline-block; margin-top: 25px; color: #00ff88;}
        </style>
      </head>
      <body>
        <div class='card'>
          <h2>❌ Failed to Connect to Wi-Fi</h2>
          <p>Please check your SSID or password and try again.</p>
          <a href='/sync'>← Back to Sync Form</a>
        </div>
      </body>
      </html>
    )rawliteral";

    server.send(200, "text/html", errorHtml);
  }
}


// --- Setup ---
void setup() {
  Serial.begin(115200);
  SPI.begin();
  mfrc522.PCD_Init();
  Wire.begin(D2, D1);
  pinMode(BUZZER_PIN, OUTPUT);

  chip_id = ESP.getChipId();           // kunin unique ID
  device_id = String(chip_id);         // convert to string
  Serial.println("📟 Device ID: " + device_id);

  if (!LittleFS.begin()) Serial.println("LittleFS failed!");
  if (!rtc.begin()) Serial.println("RTC not found!");
  else if (rtc.lostPower()) rtc.adjust(DateTime(F(__DATE__), F(__TIME__)));

  WiFi.softAP(ap_ssid, ap_password);
  Serial.println("ESP8266 SoftAP started!");
  Serial.print("ESP AP IP: "); Serial.println(WiFi.softAPIP());

  server.on("/", handleRoot);
  server.on("/view", handleViewLogs);
  server.on("/sync", handleSyncForm);
  server.on("/sync_submit", HTTP_POST, handleSyncSubmit);
  server.on("/sync_submit", HTTP_GET, []() {
    server.send(200, "text/plain", "Use POST to submit Wi-Fi credentials.");
  });
  server.begin();
  Serial.println("Web server started. Connect and open portal!");
}

// --- Loop ---
void loop() {
  server.handleClient();

  if (doSync) {
    doSync = false;
    Serial.println("🔌 Connecting to Wi-Fi: " + syncSSID);
    WiFi.mode(WIFI_STA);
    WiFi.begin(syncSSID.c_str(), syncPassword.c_str());

    int timeout = 0;
    while (WiFi.status() != WL_CONNECTED && timeout < 40) {
      delay(500);
      Serial.print(".");
      timeout++;
    }

    if (WiFi.status() == WL_CONNECTED) {
      Serial.println("\n✅ Connected! IP: " + WiFi.localIP().toString());
      syncOfflineLogsToServerPOST();
    } else Serial.println("\n❌ Failed to connect.");

    WiFi.disconnect(true);
    delay(2000);
    WiFi.softAP(ap_ssid, ap_password);
    Serial.println("SoftAP restarted.");
  }

  if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {
    String uid = "";
    for (byte i = 0; i < mfrc522.uid.size; i++) {
      if (mfrc522.uid.uidByte[i] < 0x10) uid += "0";
      uid += String(mfrc522.uid.uidByte[i], HEX);
    }
    uid.toUpperCase();

    unsigned long nowSec = millis() / 1000UL;
    int idx = findUIDIndex(uid);
    if (idx >= 0 && (nowSec - lastSeenSec[idx]) < DUPLICATE_WINDOW_SEC) {
      Serial.println("Duplicate detected.");
      beep(3);
      return;
    }

    DateTime now = rtc.now();
    char buf[25];
    sprintf(buf, "%04d-%02d-%02d %02d:%02d:%02d",
            now.year(), now.month(), now.day(),
            now.hour(), now.minute(), now.second());
    String timestamp = String(buf);
    Serial.println("UID: " + uid + " | Time: " + timestamp);

    sendRecordPOST(uid, timestamp, nowSec);
    delay(2000);
  }
}
