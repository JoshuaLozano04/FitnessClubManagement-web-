<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include 'database.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if email and fullname are provided in form data
    if (isset($_POST['email']) && isset($_POST['fullname'])) {
        $email = $_POST['email'];
        $fullname = $_POST['fullname'];

        // Update the fullname in the users table
        $updateUserQuery = "UPDATE users SET fullname = ? WHERE email = ?";
        $stmt = $conn->prepare($updateUserQuery);
        $stmt->bind_param("ss", $fullname, $email);
        
        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success", 
                "message" => "User profile updated successfully."
            ]);
        } else {
            echo json_encode([
                "status" => "error", 
                "message" => "Failed to update user profile."
            ]);
        }
        $stmt->close();
    } else {
        echo json_encode([
            "status" => "error", 
            "message" => "Email and fullname are required."
        ]);
    }

    // Close the database connection
    $conn->close();
} else {
    echo json_encode([
        "status" => "error", 
        "message" => "Invalid request method."
    ]);
}
?> 