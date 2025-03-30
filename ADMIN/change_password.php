<?php
session_start();
include 'database.php';

$response = ["success" => false, "message" => ""];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user_id"]; // Assuming user is logged in
    $currentPassword = $_POST["currentPassword"];
    $newPassword = $_POST["newPassword"];
    $confirmPassword = $_POST["confirmPassword"];
    $logoutAfterChange = isset($_POST["logoutAfterChange"]) ? $_POST["logoutAfterChange"] : "false";

    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $response["message"] = "All fields are required.";
        echo json_encode($response);
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        $response["message"] = "New passwords do not match.";
        echo json_encode($response);
        exit;
    }

    if (strlen($newPassword) < 8) {
        $response["message"] = "Password must be at least 8 characters.";
        echo json_encode($response);
        exit;
    }

    // Check current password
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($currentPassword, $hashedPassword)) {
        $response["message"] = "Current password is incorrect.";
        echo json_encode($response);
        exit;
    }

    // Hash new password
    $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update password
    $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $updateStmt->bind_param("si", $newHashedPassword, $user_id);
    
    if ($updateStmt->execute()) {
        $response["success"] = true;
        $response["message"] = "Password updated successfully.";

        // Check if user wants to log out
        if ($logoutAfterChange === "true") {
            session_destroy();
            $response["redirect"] = "login.php"; // Change this to your actual login page
        }
    } else {
        $response["message"] = "Error updating password.";
    }

    $updateStmt->close();
}

echo json_encode($response);
?>
