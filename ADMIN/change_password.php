<?php
session_start();
include 'db_connection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $currentPassword = $_POST["currentPassword"];
    $newPassword = $_POST["newPassword"];
    $confirmPassword = $_POST["confirmPassword"];
    $userId = $_SESSION["user_id"];

    // Check if passwords match
    if ($newPassword !== $confirmPassword) {
        echo "<script>alert('New passwords do not match!'); window.history.back();</script>";
        exit();
    }
    
    // Fetch user from DB
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();

        // Verify current password
        if (password_verify($currentPassword, $hashedPassword)) {
            $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update password in database
            $updateSql = "UPDATE users SET password = ? WHERE id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("si", $newHashedPassword, $userId);
            if ($updateStmt->execute()) {
                echo "<script>alert('Password changed successfully!'); window.location.href = 'dashboard.php';</script>";
            } else {
                echo "<script>alert('Error updating password!'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Incorrect current password!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('User not found!'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
