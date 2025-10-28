<?php
include('database/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get and sanitize POST data
    $uid = isset($_POST['uid']) ? mysqli_real_escape_string($conn, $_POST['uid']) : '';
    $timestamp = isset($_POST['timestamp']) ? mysqli_real_escape_string($conn, $_POST['timestamp']) : '';
    $device_id = isset($_POST['device_id']) ? mysqli_real_escape_string($conn, $_POST['device_id']) : '';

    if (empty($uid) || empty($timestamp) || empty($device_id)) {
        echo "Error: UID, timestamp, or device_id missing!";
        exit;
    }

    // Validate timestamp format
    $dt = DateTime::createFromFormat('Y-m-d H:i:s', $timestamp);
    if (!$dt) {
        echo "Error: Invalid timestamp format: $timestamp";
        exit;
    }

    // Determine session_type based on last scan today
    date_default_timezone_set('Asia/Manila');
    $today = $dt->format('Y-m-d'); // Use ESP timestamp date

    $lastScanQuery = "SELECT session_type FROM attendance 
                      WHERE card_uid='$uid' 
                      AND DATE(scan_time)='$today' 
                      ORDER BY scan_time DESC LIMIT 1";
    $lastScan = mysqli_query($conn, $lastScanQuery);

    $session = "";
    if ($lastScan && mysqli_num_rows($lastScan) > 0) {
        $row = mysqli_fetch_assoc($lastScan);
        $lastSession = $row['session_type'];

        switch ($lastSession) {
            case 'AM_IN': $session = 'AM_OUT'; break;
            case 'AM_OUT': $session = 'PM_IN'; break;
            case 'PM_IN': $session = 'PM_OUT'; break;
            case 'PM_OUT': $session = 'AM_IN'; break;
            default: $session = 'AM_IN';
        }
    } else {
        // Determine session based on ESP timestamp time
        $currentTime = $dt->format('H:i:s');
        if ($currentTime >= "06:00:00" && $currentTime <= "11:59:59") $session = 'AM_IN';
        elseif ($currentTime >= "12:00:00" && $currentTime <= "17:59:59") $session = 'PM_IN';
        else $session = 'AM_IN';
    }

    // Insert into database (with device_id)
    $insertQuery = "INSERT INTO attendance (card_uid, session_type, scan_time, device_id) 
                    VALUES ('$uid', '$session', '$timestamp', '$device_id')";
    if (mysqli_query($conn, $insertQuery)) {
        echo "✅ Attendance saved: $uid - $session at $timestamp (Device: $device_id)";
    } else {
        echo "❌ Database error: " . mysqli_error($conn);
    }

} else {
    echo "Error: Invalid request method.";
}

mysqli_close($conn);
?>
