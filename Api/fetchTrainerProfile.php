<?php
include 'database.php';

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if the email parameter is provided
    if (isset($_GET['email'])) {
        $email = $_GET['email'];

        // Fetch user data from the users table
        $userQuery = "SELECT fullname, email, role, profile_picture FROM users WHERE email = ? AND role = 'trainer'";
        $stmt = $conn->prepare($userQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $userResult = $stmt->get_result();
        $userData = $userResult->fetch_assoc();
        $stmt->close();

        if ($userData) {
            $aboutQuery = "SELECT about FROM trainers_about WHERE email = ?";
            $stmt = $conn->prepare($aboutQuery);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $aboutResult = $stmt->get_result();
            $aboutData = $aboutResult->fetch_assoc();
            $stmt->close();

            // Set default text if about info is null
            $aboutText = $aboutData['about'] ?? "Update your about info";

            // Clean up the profile_picture value
            $profilePicture = isset($userData['profile_picture']) ? trim($userData['profile_picture']) : null;

            // Return a flat response
            echo json_encode([
                "status" => "success",
                "message" => "Trainer profile fetched successfully.",
                "fullname" => $userData['fullname'],
                "email" => $userData['email'],
                "role" => $userData['role'],
                "profile_picture" => $profilePicture,
                "about" => $aboutText
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Trainer not found."
            ]);
        }
    } else {
        // Fetch all trainer profiles
        $userQuery = "SELECT u.fullname, u.email, u.role, u.profile_picture, ta.about 
                      FROM users u 
                      LEFT JOIN trainers_about ta ON u.email = ta.email
                      WHERE u.role = 'trainer'";
        $result = $conn->query($userQuery);

        $profiles = [];
        while ($row = $result->fetch_assoc()) {
            $profiles[] = [
                "fullname" => $row['fullname'],
                "email" => $row['email'],
                "role" => $row['role'],
                "profile_picture" => isset($row['profile_picture']) ? trim($row['profile_picture']) : null,
                "about" => $row['about'] ?? "Update your about info"
            ];
        }

        echo json_encode([
            "status" => "success",
            "message" => "All trainer profiles fetched successfully.",
            "profiles" => $profiles
        ]);
    }

    // Close the database connection
    $conn->close();
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method."
    ]);
}
?>