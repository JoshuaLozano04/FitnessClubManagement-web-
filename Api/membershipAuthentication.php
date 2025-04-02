<?php
include 'database.php';

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if email is provided
    if (isset($_GET['email'])) {
        $email = $_GET['email'];
        $current_date = date('Y-m-d');
        
        // Prepare the SQL statement to check membership status
        $stmt = $conn->prepare("SELECT membership_end FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $membership_end = $row['membership_end'];
            
            // Compare current date with membership end date
            if ($current_date <= $membership_end) {
                echo json_encode([
                    "success" => true,
                    "message" => "Membership is active",
                    "is_active" => true
                ]);
            } else {
                echo json_encode([
                    "success" => true,
                    "message" => "Membership has expired",
                    "is_active" => false
                ]);
            }
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No user found with the provided email"
            ]);
        }
        
        // Close the statement and the database connection
        $stmt->close();
        $conn->close();
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Email parameter is required"
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Use GET method."
    ]);
}
?> 