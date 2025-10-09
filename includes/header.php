<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include db_connect.php relative sa header.php mismo
include __DIR__ . '/../database/db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Ctech Attendance System - Login</title>
		<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
	</head>
	<style>
		.bg-leafy-green {
			background-color: #43A047;
		}
		.text-cream {
			color: #FFFDE7;
			&:hover {
				color: #F5E6CC;
			}
		}
	</style>
<body style="
		font-family: 'Poppins', sans-serif;">

