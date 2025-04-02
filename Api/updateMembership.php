<?php
include 'database.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if email is provided in form data
    if (!isset($_POST['email']) || empty($_POST['email'])) {
        echo json_encode([
            "success" => false,
            "message" => "Email is required. Please provide an email address."
        ]);
        exit;
    }
    
    $email = $_POST['email'];
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid email format. Please provide a valid email address."
        ]);
        exit;
    }
    
    $payment_amount = 600;
    
    // Calculate membership dates
    $membership_start = date('Y-m-d'); // Current date
    $membership_end = date('Y-m-d', strtotime('+1 month')); // One month from now
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update membership dates and status
        $stmt = $conn->prepare("UPDATE users SET membership_start = ?, membership_end = ?, status = 'Active' WHERE email = ?");
        $stmt->bind_param("sss", $membership_start, $membership_end, $email);
        $stmt->execute();
        
        // Record the transaction
        $stmt = $conn->prepare("INSERT INTO membership_transaction (email, payment_amount) VALUES (?, ?)");
        $stmt->bind_param("sd", $email, $payment_amount);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            "success" => true,
            "message" => "Membership dates updated successfully"
        ]);
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo json_encode([
            "success" => false,
            "message" => "Error updating membership: " . $e->getMessage()
        ]);
    }
    
    // Close the statement and the database connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Use POST method."
    ]);
}
?> 