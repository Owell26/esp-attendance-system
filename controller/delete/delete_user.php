<?php  
include "../../database/db_connect.php";
include "../../auth/authentication.php";

if (isset($_POST['deleteUser'])) {
    $card_uid = mysqli_real_escape_string($conn, $_POST['card_uid']);

    // Delete attendance records first
    $sql_attendance = "DELETE FROM attendance WHERE card_uid = '$card_uid'";
    mysqli_query($conn, $sql_attendance);

    // Delete user after
    $sql_user = "DELETE FROM users WHERE card_uid = '$card_uid'";
    $sql_run = mysqli_query($conn, $sql_user);

    if ($sql_run) {
        header("Location: ../../views/manage_users.php");
        exit();
    } else {
        echo "Error deleting user: " . mysqli_error($conn);
    }
}
?>  
