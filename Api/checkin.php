<?php
require 'database.php'; // Include your database connection

header("Content-Type: application/json");

// Set the timezone to Philippine Time (Asia/Manila)
date_default_timezone_set("Asia/Manila");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $scanned_string = $_POST['scanned_string'] ?? '';
    $user_email = $_POST['user_email'] ?? '';

    if ($scanned_string !== 'PumpingIronGym') {
        echo json_encode(["success" => false, "message" => "Invalid QR Code"]);
        exit();
    }

    // Fetch user details from the database
    $stmt = $conn->prepare("SELECT fullname, profile_picture FROM users WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "User not found"]);
        exit();
    }

    $user = $result->fetch_assoc();
    $trainee_name = $user['fullname'];
    $trainee_image = $user['profile_picture'];
    
    $checkin_date = date("Y-m-d"); // Get current date (Philippine time)
    $checkin_time = date("H:i:s"); // Get current time (Philippine time)

    // Insert check-in data into `attendance` table
    $insert_stmt = $conn->prepare("INSERT INTO attendance (Trainee_name, Trainee_image, checkin_date, checkin_time) VALUES (?, ?, ?, ?)");
    $insert_stmt->bind_param("ssss", $trainee_name, $trainee_image, $checkin_date, $checkin_time);

    if ($insert_stmt->execute()) {
        $inserted_id = $insert_stmt->insert_id; // Get the generated ID
        echo json_encode([
            "success" => true,
            "message" => "Check-in successful",
            "id" => $inserted_id,
            "checkin_date" => $checkin_date,
            "checkin_time" => $checkin_time
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to check in"]);
    }

    // Close connections
    $stmt->close();
    $insert_stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>
