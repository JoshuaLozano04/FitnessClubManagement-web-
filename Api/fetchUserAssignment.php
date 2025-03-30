<?php
include 'database.php';

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if the date parameter is provided
    if (isset($_GET['date'])) {
        $date = $_GET['date'];
        
        // Check if email is also provided
        if (isset($_GET['email'])) {
            $email = $_GET['email'];
            // Prepare the SQL statement to fetch user assignments for the given date and email
            $stmt = $conn->prepare("SELECT * FROM trainer_assignments WHERE assignment_date = ? AND user_email = ?");
            $stmt->bind_param("ss", $date, $email);
        } else {
            // Prepare the SQL statement to fetch all user assignments for the given date
            $stmt = $conn->prepare("SELECT * FROM trainer_assignments WHERE assignment_date = ?");
            $stmt->bind_param("s", $date);
        }
    } else if (isset($_GET['email'])) {
        // If only email is provided, fetch all assignments for that user
        $email = $_GET['email'];
        $stmt = $conn->prepare("SELECT * FROM trainer_assignments WHERE user_email = ?");
        $stmt->bind_param("s", $email);
    } else {
        // Prepare the SQL statement to fetch all user assignments
        $stmt = $conn->prepare("SELECT * FROM trainer_assignments");
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $assignments = [];
        while ($row = $result->fetch_assoc()) {
            $assignments[] = $row;
        }
        echo json_encode($assignments);
    } else {
        echo json_encode([]);
    }

    // Close the statement and the database connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "Invalid request method."]);
}
?> 