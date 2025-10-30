<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    $_SESSION['error'] = "Please Login First to Access this Page"; 
    header("Location:../index.php"); //
    exit();
}

$device_id = $_SESSION['device_id'];

?>