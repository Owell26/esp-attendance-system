

<?php
$servername = getenv('DB_HOST');
$port = getenv('DB_PORT');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
$dbname = getenv('DB_NAME');

$conn = mysqli_init();
mysqli_real_connect($conn, $servername, $username, $password, $dbname, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>