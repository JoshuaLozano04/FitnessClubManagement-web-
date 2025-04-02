<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'database.php';

    // Check if the request has a JSON payload
    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);

    // Extract the request_id from the payload
    $request_id = $data['request_id'] ?? null;

    $status = 'rejected';

    if ($request_id) {
        // Check if the request exists and get user information
        $checkQuery = $conn->prepare("SELECT trainer_name, user_email FROM trainer_request WHERE request_id = ?");
        $checkQuery->bind_param("i", $request_id);
        $checkQuery->execute();
        $result = $checkQuery->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $trainer_name = $row['trainer_name'];
            $user_email = $row['user_email'];

            // Create notification for the user
            $notification_message = "Your training session has been rejected by " .  $trainer_name . ".";
            $stmt = $conn->prepare("INSERT INTO notification (email, message) VALUES (?, ?)");
            $stmt->bind_param("ss", $user_email, $notification_message);

            if ($stmt->execute()) {
                $updateQuery = $conn->prepare("UPDATE trainer_request SET status = ? WHERE request_id = ?");
                $updateQuery->bind_param("si", $status, $request_id);

                if ($updateQuery->execute()) {
                    echo json_encode([
                        "status" => "success",
                        "message" => "Trainer request status updated and notification sent successfully.",
                        "request_id" => $request_id,
                        "new_status" => $status
                    ]);
                } else {
                    echo json_encode([
                        "status" => "error",
                        "message" => "Failed to update trainer request status."
                    ]);
                }

                $updateQuery->close();
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Failed to create notification."
                ]);
            }

            $stmt->close();
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Trainer request not found."
            ]);
        }

        $checkQuery->close();
        $conn->close();
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Request ID is required."
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method."
    ]);
}
?>