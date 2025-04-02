<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'database.php';

    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);

    if ($data) {
        // JSON payload
        $request_id = $data['request_id'] ?? null;
        $date_of_training = $data['date_of_training'] ?? null;
        $time_start = $data['time_start'] ?? null;
        $time_end = $data['time_end'] ?? null;
        $description = $data['description'] ?? null;
    } else {
        // Form-urlencoded payload
        $request_id = $_POST['request_id'] ?? null;
        $date_of_training = $_POST['date_of_training'] ?? null;
        $time_start = $_POST['time_start'] ?? null;
        $time_end = $_POST['time_end'] ?? null;
        $description = $_POST['description'] ?? null;
    }

    if ($request_id && $date_of_training && $time_start && $time_end && $description) {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_of_training)) {
            echo json_encode(["status" => "error", "message" => "Invalid date format. Use YYYY-MM-DD."]);
            exit;
        }

        
        if (!preg_match('/^(0[1-9]|1[0-2]):[0-5][0-9] (AM|PM)$/', $time_start)) {
            echo json_encode(["status" => "error", "message" => "Invalid time_start format. Use hh:mm AM/PM."]);
            exit;
        }
        if (!preg_match('/^(0[1-9]|1[0-2]):[0-5][0-9] (AM|PM)$/', $time_end)) {
            echo json_encode(["status" => "error", "message" => "Invalid time_end format. Use hh:mm AM/PM."]);
            exit;
        }

        // Convert time_start and time_end to 24-hour format for database storage
        $time_start_24 = date("H:i", strtotime($time_start));
        $time_end_24 = date("H:i", strtotime($time_end));

        // Check if the request exists and get trainer and user information
        $checkQuery = $conn->prepare("SELECT trainer_email, user_name FROM trainer_request WHERE request_id = ?");
        $checkQuery->bind_param("i", $request_id);
        $checkQuery->execute();
        $result = $checkQuery->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $trainer_email = $row['trainer_email'];
            $user_name = $row['user_name'];

            // Create notification for the trainer
            $notification_message = $user_name . " has rescheduled their appointment";
            $stmt = $conn->prepare("INSERT INTO notification (email, message) VALUES (?, ?)");
            $stmt->bind_param("ss", $trainer_email, $notification_message);

            if ($stmt->execute()) {
                $updateQuery = $conn->prepare("UPDATE trainer_request SET date_of_training = ?, time_start = ?, time_end = ?, description = ? WHERE request_id = ?");
                $updateQuery->bind_param("ssssi", $date_of_training, $time_start_24, $time_end_24, $description, $request_id);

                // Execute the update query
                if ($updateQuery->execute()) {
                    echo json_encode(["status" => "success", "message" => "Trainer request updated and notification sent successfully."]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Failed to update trainer request."]);
                }
                $updateQuery->close();
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to create notification."]);
            }
            $stmt->close();
        } else {
            echo json_encode(["status" => "error", "message" => "Request not found."]);
        }
        $checkQuery->close();
        $conn->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Missing required parameters."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>