<?php
header("Content-Type: application/json");
include "database.php";

$response = array();

// Get JSON input
$raw_data = file_get_contents("php://input"); 
$data = json_decode($raw_data, true);

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($data)) {
    $user_name = isset($data['user_name']) ? trim($data['user_name']) : "";
    $user_email = isset($data['user_email']) ? trim($data['user_email']) : "";
    $trainer_name = isset($data['trainer_name']) ? trim($data['trainer_name']) : "";
    $trainer_email = isset($data['trainer_email']) ? trim($data['trainer_email']) : "";
    $rating = isset($data['rating']) ? floatval($data['rating']) : 0;
    $comment = isset($data['comment']) ? trim($data['comment']) : "";

    // Validate required fields
    if (empty($user_name) || empty($user_email) || empty($trainer_name) || empty($trainer_email) || $rating < 1 || $rating > 5) {
        error_log("Invalid Input - user_name: $user_name, user_email: $user_email, trainer_name: $trainer_name, trainer_email: $trainer_email, rating: $rating");
        $response["success"] = false;
        $response["message"] = "Invalid input. Please provide valid details.";
        echo json_encode($response);
        exit();
    }

    // Fetch user's profile picture
    $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $stmt->bind_result($profile_picture);
    $stmt->fetch();
    $stmt->close();

    if (empty($profile_picture)) {
        $profile_picture = "default.png";
    }

    // Insert review into trainer_review table
    $stmt = $conn->prepare("INSERT INTO trainer_review (user_name, user_email, trainer_name, trainer_email, rating, comment, user_picture) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssdss", $user_name, $user_email, $trainer_name, $trainer_email, $rating, $comment, $profile_picture);

    if ($stmt->execute()) {
        $response["success"] = true;
        $response["message"] = "Review submitted successfully!";
    } else {
        $response["success"] = false;
        $response["message"] = "Failed to submit review.";
    }

    $stmt->close();
    $conn->close();
} else {
    $response["success"] = false;
    $response["message"] = "Invalid request method.";
}

echo json_encode($response);
?>
