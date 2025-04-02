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

// Read the form-urlencoded data directly from $_POST
if (empty($_POST["email"])) {
    respond(["message" => "Email is required"], 400);
}

$email = $_POST["email"];

// Use prepared statements to prevent SQL injection
$sql = "UPDATE users SET token=NULL WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);

if ($stmt->execute()) {
    respond(["message" => "Logout successful"]);
} else {
    respond(["message" => "Error: " . $stmt->error], 500);
}

$stmt->close();
$conn->close();
?>
