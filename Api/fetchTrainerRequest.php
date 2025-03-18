<?php
// filepath: c:\xampp\htdocs\PumpingIronGym\Api\uploads\fetchTrainerRequest.php

include 'database.php';

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if the email parameter is provided
    if (isset($_GET['email'])) {
        $email = $_GET['email'];

        // Prepare the SQL statement to fetch all trainer requests with the given email
        $stmt = $conn->prepare("SELECT * FROM trainer_request WHERE trainer_email = ?");
        $stmt->bind_param("s", $email);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $requests = [];
            while ($row = $result->fetch_assoc()) {
                $requests[] = $row;
            }
            echo json_encode($requests);
        } else {
            echo json_encode([]);
        }

        // Close the statement and the database connection
        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(["error" => "Email parameter is required."]);
    }
} else {
    echo json_encode(["error" => "Invalid request method."]);
}
?>