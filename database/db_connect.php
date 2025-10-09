<?php
$servername = "gateway01.ap-southeast-1.prod.aws.tidbcloud.com";
$port = 4000;
$username = "QAjABtC55ERu3ho.root";
$password = "vVEQLZU2Q8paqQKA";
$dbname = "attendance_system";

// Initialize MySQLi
$conn = mysqli_init();

// Enable SSL
mysqli_ssl_set($conn, NULL, NULL, __DIR__ . '/ca.pem', NULL, NULL);

// Real connect with SSL
mysqli_real_connect($conn, $servername, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>
