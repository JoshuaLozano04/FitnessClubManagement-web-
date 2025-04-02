<?php
include "database.php";

function respond($message, $status = 200) {
    http_response_code($status);
    echo json_encode($message);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(["message" => "Invalid request method"], 405);
}


if (empty($_POST["email"])) {
    respond(["message" => "Email is required"], 400);
}

$email = $_POST["email"];

$sql = "SELECT membership_start, membership_end, status FROM users WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $next_payment_date = date('Y-m-d', strtotime($row['membership_end'] . ' +1 day'));
    
    respond([
        "success" => true,
        "message" => "Membership dates retrieved successfully",
        "membership_start" => $row['membership_start'],
        "membership_end" => $row['membership_end'],
        "next_payment_date" => $next_payment_date,
        "status" => $row['status']
    ]);
} else {
    respond([
        "success" => false,
        "message" => "No user found with this email"
    ], 404);
}

$stmt->close();
$conn->close();
?> 