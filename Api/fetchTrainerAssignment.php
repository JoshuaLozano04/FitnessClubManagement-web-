<?php
include 'database.php';

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if the date parameter is provided
    if (isset($_GET['date'])) {
        $date = $_GET['date'];

        // Prepare the SQL statement to fetch all trainer assignments for the given date
        $stmt = $conn->prepare("SELECT * FROM trainer_assignments WHERE assignment_date = ?");
        $stmt->bind_param("s", $date);
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
        echo json_encode(["error" => "Date parameter is required."]);
    }
} else {
    echo json_encode(["error" => "Invalid request method."]);
}
?>