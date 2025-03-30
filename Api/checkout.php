<?php
require 'database.php'; // Include your database connection

header("Content-Type: application/json");

// Set the timezone to Philippine Time (Asia/Manila)
date_default_timezone_set("Asia/Manila");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $attendance_id = $_POST['id'] ?? '';
    $scanned_string = $_POST['scanned_string'] ?? '';

    if (empty($attendance_id)) {
        echo json_encode(["success" => false, "message" => "Missing check-in ID"]);
        exit();
    }

    if ($scanned_string !== 'PumpingIronGym') {
        echo json_encode(["success" => false, "message" => "Invalid QR Code"]);
        exit();
    }

    $checkout_time = date("Y-m-d H:i:s"); // Get current Philippine time

    // Update the check-out time in the `attendance` table
    $stmt = $conn->prepare("UPDATE attendance SET checkout_time = ? WHERE id = ?");
    $stmt->bind_param("si", $checkout_time, $attendance_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Check-out successful", "checkout_time" => $checkout_time]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to check out. ID not found or already checked out."]);
    }

    // Close connections
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>
