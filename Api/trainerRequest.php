<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include the database connection file
    include 'database.php';

    // Check if all required POST parameters are set
    if (isset($_POST['user_email'], $_POST['trainer_email'])) {
        $user_email = $_POST['user_email'];
        $trainer_email = $_POST['trainer_email'];

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
        $stmt = $conn->prepare("INSERT INTO trainer_request (user_email, user_name, trainer_email, trainer_name, request_date, status) VALUES (?, ?, ?, ?, NOW(), 'pending')");
        $stmt->bind_param("ssss", $user_email, $user_name, $trainer_email, $trainer_name);

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
        echo "Error: Missing required POST parameters.";
    }
} else {
    echo "Invalid request method.";
}
?>