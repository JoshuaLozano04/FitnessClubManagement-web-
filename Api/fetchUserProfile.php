<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed'
    ]);
    exit();
}

// Get the email from either form data or JSON
$email = '';
if (isset($_POST['email'])) {
    $email = $_POST['email'];
} else {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['email'])) {
        $email = $data['email'];
    }
}

// Check if email is provided
if (empty($email)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Email is required'
    ]);
    exit();
}

$email = mysqli_real_escape_string($conn, $email);

// Query to fetch user data
$query = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $query);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . mysqli_error($conn)
    ]);
    exit();
}

if (mysqli_num_rows($result) === 0) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => 'User not found'
    ]);
    exit();
}

// Fetch user data
$user = mysqli_fetch_assoc($result);

// Format the response according to the specified structure
$response = [
    'status' => 'success',
    'message' => 'User profile retrieved successfully',
    'fullname' => $user['fullname'] ?? '',
    'email' => $user['email'] ?? '',
    'role' => $user['role'] ?? '',
    'profile_picture' => $user['profile_picture'] ?? ''
];

echo json_encode($response);

mysqli_close($conn);
?> 