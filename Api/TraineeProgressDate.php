<?php
require 'database.php'; // Include your database connection

if (!isset($_GET['email']) || !isset($_GET['date'])) {
    echo json_encode(["success" => false, "message" => "Email and date are required"]);
    exit;
}

$email = $_GET['email'];
$date = $_GET['date'];

// Query to get the full name of the user based on email
$userQuery = "SELECT fullname FROM users WHERE email = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("s", $email);
$stmt->execute();
$userResult = $stmt->get_result();
$userRow = $userResult->fetch_assoc();

if (!$userRow) {
    echo json_encode(["success" => false, "message" => "User not found"]);
    exit;
}

$fullname = $userRow['fullname'];

// Query to get check-in and check-out time based on the date
$attendanceQuery = "
    SELECT checkin_time, checkout_time 
    FROM attendance 
    WHERE trainee_name = ? AND checkin_date = ?";
$stmt = $conn->prepare($attendanceQuery);
$stmt->bind_param("ss", $fullname, $date);
$stmt->execute();
$attendanceResult = $stmt->get_result();
$attendanceRow = $attendanceResult->fetch_assoc();

if (!$attendanceRow) {
    echo json_encode(["success" => false, "message" => "No attendance record found for this date"]);
    exit;
}

$response = [
    "success" => true,
    "checkin_time" => $attendanceRow['checkin_time'] ?? null,
    "checkout_time" => $attendanceRow['checkout_time'] ?? null
];

echo json_encode($response);
?>
