<?php
include 'auth/authentication.php';
include 'database/db_connect.php'; // Adjust if your DB connection path is different

header('Content-Type: application/json');

// Query attendance with assigned users
$sql = "
    SELECT attendance.card_uid, attendance.scan_time, attendance.session_type, users.first_name, users.middle_name, users.last_name, users.suffix, users.user_type
    FROM attendance
    INNER JOIN users ON attendance.card_uid = users.card_uid
    ORDER BY attendance.scan_time DESC
";

$result = mysqli_query($conn, $sql);
$data = [];

if($result && mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
        $data[] = $row;
    }
}

echo json_encode($data);
