<?php
require 'database.php'; // Include your database connection

if (!isset($_GET['email'])) {
    echo json_encode(["success" => false, "message" => "Email is required"]);
    exit;
}

$email = $_GET['email'];

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

// Query for total days in the gym
$totalDaysQuery = "SELECT COUNT(DISTINCT checkin_date) AS total_days FROM attendance WHERE trainee_name = ?";
$stmt = $conn->prepare($totalDaysQuery);
$stmt->bind_param("s", $fullname);
$stmt->execute();
$totalDaysResult = $stmt->get_result();
$totalDaysRow = $totalDaysResult->fetch_assoc();
$totalDays = $totalDaysRow['total_days'] ?? 0;

// Query for total workout hours
$totalHoursQuery = "
    SELECT SUM(TIMESTAMPDIFF(MINUTE, checkin_time, checkout_time)) AS total_minutes 
    FROM attendance 
    WHERE trainee_name = ? AND checkout_time IS NOT NULL";
$stmt = $conn->prepare($totalHoursQuery);
$stmt->bind_param("s", $fullname);
$stmt->execute();
$totalHoursResult = $stmt->get_result();
$totalHoursRow = $totalHoursResult->fetch_assoc();
$totalMinutes = $totalHoursRow['total_minutes'] ?? 0;
$totalWorkoutHours = round($totalMinutes / 60, 2); // Convert minutes to hours

// Query for this week's attendance count
$thisWeekQuery = "
    SELECT COUNT(DISTINCT checkin_date) AS this_week_days 
    FROM attendance 
    WHERE trainee_name = ? AND YEARWEEK(checkin_date, 1) = YEARWEEK(NOW(), 1)";
$stmt = $conn->prepare($thisWeekQuery);
$stmt->bind_param("s", $fullname);
$stmt->execute();
$thisWeekResult = $stmt->get_result();
$thisWeekRow = $thisWeekResult->fetch_assoc();
$thisWeekDays = $thisWeekRow['this_week_days'] ?? 0;

// Query for this month's attendance count
$thisMonthQuery = "
    SELECT COUNT(DISTINCT checkin_date) AS this_month_days 
    FROM attendance 
    WHERE trainee_name = ? AND MONTH(checkin_date) = MONTH(NOW()) AND YEAR(checkin_date) = YEAR(NOW())";
$stmt = $conn->prepare($thisMonthQuery);
$stmt->bind_param("s", $fullname);
$stmt->execute();
$thisMonthResult = $stmt->get_result();
$thisMonthRow = $thisMonthResult->fetch_assoc();
$thisMonthDays = $thisMonthRow['this_month_days'] ?? 0;

$response = [
    "success" => true,
    "fullname" => $fullname,
    "total_days" => (int)$totalDays,
    "total_workout_hours" => (float)$totalWorkoutHours,
    "this_week_days" => (int)$thisWeekDays,
    "this_month_days" => (int)$thisMonthDays
];

echo json_encode($response);
?>
