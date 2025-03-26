<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include the database connection file
    include 'database.php';

    // Check if the request has a JSON payload
    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);

    // Determine if the request is JSON or form-urlencoded
    if ($data) {
        // JSON payload
        $user_email = $data['user_email'] ?? null;
        $trainer_email = $data['trainer_email'] ?? null;
        $date_of_training = $data['date_of_training'] ?? null;
        $time_start = $data['time_start'] ?? null;
        $time_end = $data['time_end'] ?? null;
        $description = $data['description'] ?? null;
    } else {
        // Form-urlencoded payload
        $user_email = $_POST['user_email'] ?? null;
        $trainer_email = $_POST['trainer_email'] ?? null;
        $date_of_training = $_POST['date_of_training'] ?? null;
        $time_start = $_POST['time_start'] ?? null;
        $time_end = $_POST['time_end'] ?? null;
        $description = $_POST['description'] ?? null;
    }

    // Check if all required parameters are set
    if ($user_email && $trainer_email && $date_of_training && $time_start && $time_end && $description) {
        // Validate date_of_training
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_of_training)) {
            echo json_encode(["status" => "error", "message" => "Invalid date format. Use YYYY-MM-DD."]);
            exit;
        }

        // Validate time_start and time_end in 12-hour format
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

        // Get the full name of the user
        $userQuery = $conn->prepare("SELECT fullname FROM users WHERE email = ?");
        $userQuery->bind_param("s", $user_email);
        $userQuery->execute();
        $userResult = $userQuery->get_result();
        if ($userResult->num_rows > 0) {
            $user_name = $userResult->fetch_assoc()['fullname'];
        } else {
            echo json_encode(["status" => "error", "message" => "User not found."]);
            exit;
        }
        $userQuery->close();

        // Get the full name of the trainer
        $trainerQuery = $conn->prepare("SELECT fullname FROM users WHERE email = ?");
        $trainerQuery->bind_param("s", $trainer_email);
        $trainerQuery->execute();
        $trainerResult = $trainerQuery->get_result();
        if ($trainerResult->num_rows > 0) {
            $trainer_name = $trainerResult->fetch_assoc()['fullname'];
        } else {
            echo json_encode(["status" => "error", "message" => "Trainer not found."]);
            exit;
        }
        $trainerQuery->close();

        // Prepare the SQL statement to insert the request
        $stmt = $conn->prepare("INSERT INTO trainer_request (user_email, user_name, trainer_email, trainer_name, date_of_training, time_start, time_end, description, request_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'pending')");
        $stmt->bind_param("ssssssss", $user_email, $user_name, $trainer_email, $trainer_name, $date_of_training, $time_start_24, $time_end_24, $description);

        // Execute the statement
        if ($stmt->execute()) {
            $request_id = $stmt->insert_id; // Get the ID of the newly created request
            echo json_encode([
                "status" => "success",
                "message" => "Trainer request added successfully.",
                "request_id" => $request_id
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
        }

        // Close the statement and the database connection
        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Missing required parameters."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>