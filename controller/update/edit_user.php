<?php  
include"../../database/db_connect.php";
include"../../auth/authentication.php";


if (isset($_POST['editUser'])) {
	$card_uid = mysqli_real_escape_string($conn,$_POST['card_uid']);
	$firstname = mysqli_real_escape_string($conn,$_POST['firstname']);
	$middlename = mysqli_real_escape_string($conn,$_POST['middlename']);
	$lastname = mysqli_real_escape_string($conn,$_POST['lastname']);
	$suffix = mysqli_real_escape_string($conn,$_POST['suffix']);
	$user_type = mysqli_real_escape_string($conn,$_POST['user_type']);

	$sql = "UPDATE users 
			SET first_name = '$firstname',
				middle_name = '$middlename',
				last_name = '$lastname',
				suffix = '$suffix',
				user_type = '$user_type'
			WHERE card_uid = '$card_uid'";
	$sql_run = mysqli_query($conn, $sql);

	if ($sql_run) {
		header("Location:../../views/manage_users.php");
	}else{
		echo "Error";
	}
}
?>