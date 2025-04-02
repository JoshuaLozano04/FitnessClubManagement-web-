<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'database.php';

    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);

    $request_id = $data['request_id'] ?? null;
    $status = 'approved';

    if ($request_id) {
        $checkQuery = $conn->prepare("SELECT * FROM trainer_request WHERE request_id = ?");
        $checkQuery->bind_param("i", $request_id);
        $checkQuery->execute();
        $result = $checkQuery->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            $user_email = $row['user_email'];
            $user_name = $row['user_name'];
            $trainer_email = $row['trainer_email'];
            $trainer_name = $row['trainer_name'];
            $assignment_date = $row['date_of_training'];
            $start_time = $row['time_start'];
            $end_time = $row['time_end'];

            // Create notification for the user
            $notification_message = "Your training request has been accepted by " . $trainer_name . ".";
            $stmt = $conn->prepare("INSERT INTO notification (email, message) VALUES (?, ?)");
            $stmt->bind_param("ss", $user_email, $notification_message);

            if ($stmt->execute()) {
                $updateQuery = $conn->prepare("UPDATE trainer_request SET status = ? WHERE request_id = ?");
                $updateQuery->bind_param("si", $status, $request_id);
                
                if ($updateQuery->execute()) {
                    $insertQuery = $conn->prepare("INSERT INTO trainer_assignments (request_id, user_email, user_name, trainer_email, trainer_name, assignment_date, status, start_time, end_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $insertQuery->bind_param("issssssss", $request_id, $user_email, $user_name, $trainer_email, $trainer_name, $assignment_date, $status, $start_time, $end_time);
                    
                    if ($insertQuery->execute()) {
                        echo json_encode([
                            "status" => "success",
                            "message" => "Trainer request approved, assigned, and notification sent successfully.",
                            "request_id" => $request_id,
                            "new_status" => $status
                        ]);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Failed to insert into trainer_assignment."]);
                    }
                    $insertQuery->close();
                } else {
                    echo json_encode(["status" => "error", "message" => "Failed to update trainer request status."]);
                }
                $updateQuery->close();
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to create notification."]);
            }
            $stmt->close();
        } else {
            echo json_encode(["status" => "error", "message" => "Trainer request not found."]);
        }
        $checkQuery->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Request ID is required."]);
    }
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>