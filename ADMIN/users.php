
<?php
session_start();
require 'database.php'; // Include database connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect if not logged in
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$sql = "SELECT username, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$host = "localhost";  // Change if needed
$user = "root";       // Change if needed
$pass = "";           // Change if needed
$dbname = "fitnessclubmanagement_db"; // Your database name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <p>Welcome to the user account page</p>

</body>
</html>