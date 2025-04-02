<?php
include 'database.php';

// Fetch total reviews
$totalReviewsResult = mysqli_query($conn, "SELECT COUNT(*) as total_reviews FROM trainer_review");
$totalReviewsRow = mysqli_fetch_assoc($totalReviewsResult);
$totalReviews = $totalReviewsRow['total_reviews'];

// Fetch average rating
$averageRatingResult = mysqli_query($conn, "SELECT AVG(rating) as average_rating FROM trainer_review");
$averageRatingRow = mysqli_fetch_assoc($averageRatingResult);
$averageRating = round($averageRatingRow['average_rating'] ?? 0, 1);

// Fetch recent reviews with trainer names
$recentReviewsResult = mysqli_query($conn, "
    SELECT trainer_name, user_name, rating, comment, created_at 
    FROM trainer_review 
    ORDER BY created_at DESC 
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Report</title>
    <link rel="stylesheet" href="reportStyle.css">
</head>
<body>
    <div class="review-report">
        <div class="review-summary">
            <div class="total-reviews">
                <h3>Total Reviews</h3>
                <p><?php echo number_format($totalReviews); ?></p>
            </div>
            <div class="average-rating">
                <h3>Average Rating</h3>
                <p><?php echo $averageRating; ?> 
                    <span class="stars">
                        <?php
                        // Display stars based on the average rating
                        $fullStars = floor($averageRating);
                        $halfStar = ($averageRating - $fullStars) >= 0.5 ? 1 : 0;
                        for ($i = 0; $i < $fullStars; $i++) echo '★';
                        if ($halfStar) echo '☆';
                        for ($i = $fullStars + $halfStar; $i < 5; $i++) echo '☆';
                        ?>
                    </span>
                </p>
            </div>
        </div>
        <div class="recent-reviews">
            <h3>Recent Reviews</h3>
            <select>
                <option>Jan 2025 - Mar 2025</option>
                <!-- Add more options dynamically if needed -->
            </select>
            <ul>
                <?php while ($review = mysqli_fetch_assoc($recentReviewsResult)) : ?>
                <li>
                    <div class="reviewer-info">
                        <img src="img/user.png" alt="Reviewer Image">
                        <div>
                            <h4><strong>Trainer:</strong> <?php echo htmlspecialchars($review['trainer_name']); ?></h4>
                            <p>
                                <?php
                                // Display stars for each review
                                $rating = $review['rating'];
                                for ($i = 0; $i < $rating; $i++) echo '★';
                                for ($i = $rating; $i < 5; $i++) echo '☆';
                                ?>
                            </p>
                        </div>
                    </div>
                    <small><?php echo htmlspecialchars($review['created_at']); ?></small>
                </li>
               
                <li>
                    <p><strong>Comment:</strong> <?php echo htmlspecialchars($review['comment']); ?></p>
                    <p><strong>From:</strong> <?php echo htmlspecialchars($review['user_name']); ?></p>
                </li>
                <li class="divider"></li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>
</body>
</html>