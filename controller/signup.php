<?php
session_start();
include('../database/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $device_id = mysqli_real_escape_string($conn, $_POST['device_id']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);

    if (!empty($username) && !empty($password) && !empty($full_name) && !empty($device_id)) {
        // Check if username already exists
        $check = "SELECT * FROM admin WHERE device_id='$device_id' LIMIT 1";
        $check_result = mysqli_query($conn, $check);

        if (mysqli_num_rows($check_result) > 0) {
            $_SESSION['error'] = "Device already exists.";
            header("Location: ../signup.php");
            exit;
        }

        // Insert new admin (no password hashing)
        $sql = "INSERT INTO admin (device_id, full_name, user_name, password, is_active)
                VALUES ('$device_id', '$full_name', '$username', '$password', '1')";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $_SESSION['success'] = "Account created successfully!";
            header("Location: ../index.php");
            exit;
        } else {
            $_SESSION['error'] = "Error creating account. Please try again.";
            header("Location: ../signup.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../signup.php");
        exit;
    }
}
?>
