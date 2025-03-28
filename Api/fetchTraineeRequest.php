<?php
// filepath: c:\xampp\htdocs\PumpingIronGym\Api\uploads\fetchTrainerRequest.php

include 'database.php';

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if the request_id parameter is provided
    if (isset($_GET['request_id'])) {
        $request_id = intval($_GET['request_id']); // Ensure request_id is an integer

        // Prepare the SQL statement to fetch the trainer request with the given request_id
        $stmt = $conn->prepare("SELECT * FROM trainer_request WHERE request_id = ?");
        $stmt->bind_param("i", $request_id);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $request = $result->fetch_assoc(); // Fetch the single request
            echo json_encode([
                "status" => "success",
                "message" => "Trainer request fetched successfully.",
                "request" => $request
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Trainer request not found."
            ]);
        }

        // Close the statement and the database connection
        $stmt->close();
        $conn->close();
    } 
    // Check if the email parameter is provided
    else if (isset($_GET['email'])) {
        $email = $_GET['email'];

        // Prepare the SQL statement to fetch all trainer requests with the given email
        $stmt = $conn->prepare("SELECT * FROM trainer_request WHERE user_email = ?");
        $stmt->bind_param("s", $email);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $requests = [];
            while ($row = $result->fetch_assoc()) {
                $requests[] = $row;
            }
            echo json_encode([
                "status" => "success",
                "message" => "Trainer requests fetched successfully.",
                "requests" => $requests
            ]);
        } else {
            echo json_encode([
                "status" => "success",
                "message" => "No trainer requests found.",
                "requests" => []
            ]);
        }

        // Close the statement and the database connection
        $stmt->close();
        $conn->close();
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Either request_id or email parameter is required."
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method."
    ]);
}
?>