<?php
include('database/db_connect.php');

if(isset($_POST['uid']) && isset($_POST['name'])) {
    $uid = $_POST['uid'];
    $name = $_POST['name'];

    // Check if UID already exists in users table
    $stmt = $conn->prepare("SELECT * FROM users WHERE card_uid=?");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        // Update name
        $stmtUpdate = $conn->prepare("UPDATE users SET name=? WHERE card_uid=?");
        $stmtUpdate->bind_param("ss", $name, $uid);
        if($stmtUpdate->execute()){
            echo "Name updated successfully!";
        } else {
            echo "Error updating name.";
        }
        $stmtUpdate->close();
    } else {
        // Insert new user
        $stmtInsert = $conn->prepare("INSERT INTO users (card_uid, name) VALUES (?, ?)");
        $stmtInsert->bind_param("ss", $uid, $name);
        if($stmtInsert->execute()){
            echo "Name saved successfully!";
        } else {
            echo "Error saving name.";
        }
        $stmtInsert->close();
    }
    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
