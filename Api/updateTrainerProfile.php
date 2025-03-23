<?php
include 'database.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if email, fullname, and about_text are provided
    if (isset($_POST['email']) && isset($_POST['fullname']) && isset($_POST['about_text'])) {
        $email = $_POST['email'];
        $fullname = $_POST['fullname'];
        $about_text = $_POST['about_text'];

        // Update the fullname in the users table
        $updateUserQuery = "UPDATE users SET fullname = ? WHERE email = ?";
        $stmt = $conn->prepare($updateUserQuery);
        $stmt->bind_param("ss", $fullname, $email);
        if ($stmt->execute()) {
            $userUpdateStatus = true;
        } else {
            $userUpdateStatus = false;
        }
        $stmt->close();

        // Check if the trainer exists in the trainers_about table
        $checkQuery = "SELECT COUNT(*) AS count FROM trainers_about WHERE email = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row['count'] > 0) {
            // Update the existing record in trainers_about
            $updateQuery = "UPDATE trainers_about SET about = ? WHERE email = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("ss", $about_text, $email);
            if ($stmt->execute()) {
                $aboutUpdateStatus = true;
            } else {
                $aboutUpdateStatus = false;
            }
            $stmt->close();
        } else {
            // Insert a new record into trainers_about
            $insertQuery = "INSERT INTO trainers_about (email, about) VALUES (?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ss", $email, $about_text);
            if ($stmt->execute()) {
                $aboutUpdateStatus = true;
            } else {
                $aboutUpdateStatus = false;
            }
            $stmt->close();
        }

        // Return the response
        if ($userUpdateStatus && $aboutUpdateStatus) {
            echo json_encode(["status" => "success", "message" => "Trainer profile updated successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update trainer profile."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Email, fullname, and about_text are required."]);
    }

    // Close the database connection
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>