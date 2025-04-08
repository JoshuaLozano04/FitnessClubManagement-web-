<?php
header('Content-Type: application/json');
include 'database.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the email from the POST request
    $email = $_POST['email'] ?? '';

    // Validate email
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Prepare the SQL query to check if the email exists
        $query = $conn->prepare("SELECT COUNT(*) AS count FROM users WHERE email = ?");
        $query->bind_param("s", $email);
        $query->execute();
        $result = $query->get_result();
        $row = $result->fetch_assoc();

        // Check if the email exists
        if ($row['count'] > 0) {
            echo json_encode(['exists' => true, 'message' => 'Email already exists.']);
        } else {
            echo json_encode(['exists' => false, 'message' => 'Email is available.']);
        }

        $query->close();
    } else {
        echo json_encode(['error' => true, 'message' => 'Invalid email format.']);
    }
} else {
    echo json_encode(['error' => true, 'message' => 'Invalid request method.']);
}

$conn->close();
?>