<?php
session_start();
include('../database/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn,$_POST['username']);
    $password = mysqli_real_escape_string($conn,$_POST['password']);

    if(!empty($username) && !empty($password)){
        $sql = "SELECT * FROM admin WHERE user_name='$username' AND password='$password' LIMIT 1";
        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);
            $is_active = $row['is_active'];

            if ($is_active !== "1") {
                $error = "Account not yet activated";
            } else {
                // Create session for logged-in admin
                $_SESSION['admin_logged_in'] = true;        // flag for login
                $_SESSION['admin_id'] = $row['admin_id'];         // admin id
                $_SESSION['admin_name'] = $row['full_name'];     // admin name
                $_SESSION['admin_username'] = $row['user_name']; // optional username
                $_SESSION['device_id'] = $row['device_id'];    

                // Redirect to dashboard
                header("Location:../views/dashboard.php");
                exit;
            }
        } else {
            $_SESSION['error'] = "Invalid username or password"; 
            header("Location:../index.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Please enter username and password"; 
        header("Location:../index.php");
        exit;
    }
}
?>