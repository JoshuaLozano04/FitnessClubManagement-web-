<?php
require 'database.php'; // Ensure you have a connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if request_id is set
    if (isset($_POST['request_id'])) {
        $request_id = $_POST['request_id'];

        $stmt = $conn->prepare("SELECT trainer_email, user_name FROM trainer_request WHERE request_id = ?");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $trainer_email = $row['trainer_email'];
            $user_name = $row['user_name'];
            
            $notification_message = "Your training session has been cancelled by " . $user_name . ".";
            $stmt = $conn->prepare("INSERT INTO notification (email, message) VALUES (?, ?)");
            $stmt->bind_param("ss", $trainer_email, $notification_message);
            
            if ($stmt->execute()) {
                $stmt = $conn->prepare("DELETE FROM trainer_request WHERE request_id = ?");
                $stmt->bind_param("i", $request_id);

                if ($stmt->execute()) {
                    echo json_encode(["success" => true, "message" => "Appointment cancelled and notification sent successfully."]);
                } else {
                    echo json_encode(["success" => false, "message" => "Failed to delete appointment."]);
                }
            } else {
                echo json_encode(["success" => false, "message" => "Failed to create notification."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Request not found."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Invalid request."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

$conn->close();
?>
