<?php
session_start();

// Unset all admin-related sessions
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_name']);
unset($_SESSION['admin_username']);

// Or destroy the whole session
session_destroy();

// Redirect back to login page
header("Location: ../index.php");
exit;
?>
