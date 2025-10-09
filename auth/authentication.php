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

// You can access admin info like:
// $adminName = $_SESSION['admin_name'];
// $adminId = $_SESSION['admin_id'];
?>