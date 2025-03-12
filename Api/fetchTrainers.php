<?php
include 'database.php';

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Prepare the SQL statement to fetch all trainers without the password field
    $stmt = $conn->prepare("SELECT id, fullname, email, role FROM users WHERE role = ?");
    $role = 'trainer';
    $stmt->bind_param("s", $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $trainers = [];
        while ($row = $result->fetch_assoc()) {
            $trainers[] = $row;
        }
        echo json_encode($trainers);
    } else {
        echo json_encode([]);
    }

    // Close the statement and the database connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>