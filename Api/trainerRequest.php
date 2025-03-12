<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include the database connection file
    include 'database.php';

    // Check if all required POST parameters are set
    if (isset($_POST['user_email'], $_POST['trainer_email'], $_POST['date_of_training'], $_POST['time_start'], $_POST['time_end'], $_POST['description'])) {
        $user_email = $_POST['user_email'];
        $trainer_email = $_POST['trainer_email'];
        $date_of_training = $_POST['date_of_training'];
        $time_start = $_POST['time_start'];
        $time_end = $_POST['time_end'];
        $description = $_POST['description'];

        // Validate date_of_training
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_of_training)) {
            echo "Error: Invalid date format. Use YYYY-MM-DD.";
            exit;
        }

        // Get the full name of the user
        $userQuery = $conn->prepare("SELECT fullname FROM users WHERE email = ?");
        $userQuery->bind_param("s", $user_email);
        $userQuery->execute();
        $userResult = $userQuery->get_result();
        if ($userResult->num_rows > 0) {
            $user_name = $userResult->fetch_assoc()['fullname'];
        } else {
            echo "Error: User not found.";
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
            echo "Error: Trainer not found.";
            exit;
        }
        $trainerQuery->close();

        // Prepare the SQL statement to insert the request
        $stmt = $conn->prepare("INSERT INTO trainer_request (user_email, user_name, trainer_email, trainer_name, date_of_training, time_start, time_end, description, request_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'pending')");
        $stmt->bind_param("ssssssss", $user_email, $user_name, $trainer_email, $trainer_name, $date_of_training, $time_start, $time_end, $description);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Trainer request added successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement and the database connection
        $stmt->close();
        $conn->close();
    } else {
        $missing_params = [];
        if (!isset($_POST['user_email'])) $missing_params[] = 'user_email';
        if (!isset($_POST['trainer_email'])) $missing_params[] = 'trainer_email';
        if (!isset($_POST['date_of_training'])) $missing_params[] = 'date_of_training';
        if (!isset($_POST['time_start'])) $missing_params[] = 'time_start';
        if (!isset($_POST['time_end'])) $missing_params[] = 'time_end';
        if (!isset($_POST['description'])) $missing_params[] = 'description';
        echo "Error: Missing required POST parameters: " . implode(', ', $missing_params) . ".";
    }
} else {
    echo "Invalid request method.";
}
?>