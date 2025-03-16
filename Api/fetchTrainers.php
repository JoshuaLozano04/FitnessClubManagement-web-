<?php
include 'database.php';

// Define the base URL for profile pictures
$base_url = 'profiles/'; // Replace 'yourdomain.com' with your actual domain

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Prepare the SQL statement to fetch all trainers without the password field
    $stmt = $conn->prepare("SELECT id, profile_picture, fullname, email, role FROM users WHERE role = ?");
    $role = 'trainer';
    $stmt->bind_param("s", $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $trainers = [];
        while ($row = $result->fetch_assoc()) {
            // Trim any whitespace or newline characters from the profile_picture field
            $row['profile_picture'] = trim($row['profile_picture']);
            // Add the full URL to the profile_picture field
            $row['profile_picture'] = $base_url . $row['profile_picture'];
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