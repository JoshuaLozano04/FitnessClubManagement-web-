<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'database.php';

    // Get email from form-urlencoded data
    $email = $_POST['email'] ?? null;

    if ($email) {
        // First check if user exists
        $checkStmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            // Update user role to member
            $updateStmt = $conn->prepare("UPDATE users SET role = 'member' WHERE email = ?");
            $updateStmt->bind_param("s", $email);

            if ($updateStmt->execute()) {
                echo json_encode([
                    "status" => "success",
                    "message" => "User role updated to member successfully."
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Failed to update user role."
                ]);
            }
            $updateStmt->close();
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "User not found."
            ]);
        }
        $checkStmt->close();
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Email is required."
        ]);
    }
    $conn->close();
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method."
    ]);
}
?> 