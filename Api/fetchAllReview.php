<?php
require 'database.php'; // Include your database connection file

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['trainer_email'])) {
        $trainer_email = $_GET['trainer_email'];

        // Prepare SQL query to fetch reviews for the trainer
        $stmt = $conn->prepare("SELECT * FROM trainer_review WHERE trainer_email = ?");
        $stmt->bind_param("s", $trainer_email);
        $stmt->execute();
        $result = $stmt->get_result();

        $reviews = [];
        $totalRating = 0;
        $reviewCount = 0;

        while ($row = $result->fetch_assoc()) {
            $totalRating += $row['rating']; // Assuming 'rating' column exists in trainer_review table
            $reviewCount++;
            $reviews[] = $row;
        }

        $averageRating = ($reviewCount > 0) ? round($totalRating / $reviewCount, 2) : 0;

        // Add average rating inside each review
        foreach ($reviews as &$review) {
            $review['average_rating'] = $averageRating;
        }

        echo json_encode([
            'success' => true,
            'reviews' => $reviews
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'trainer_email parameter is required'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>
