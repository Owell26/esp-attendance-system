<?php
include 'auth/authentication.php';
include 'database/db_connect.php'; // Adjust if your DB connection path is different

header('Content-Type: application/json');

// Query attendance with assigned users
$sql = "
    SELECT attendance.*, users.*
    FROM attendance
    INNER JOIN users ON attendance.card_uid = users.card_uid
    WHERE device_id = '$device_id'
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
