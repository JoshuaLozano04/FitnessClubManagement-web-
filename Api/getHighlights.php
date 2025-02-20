<?php
require "database.php";
// Fetch highlights with images
$sql = "SELECT mh.id, mh.name, mh.user_email, mh.caption, mh.created_at, mhi.image_url 
        FROM monthly_highlights mh
        LEFT JOIN monthly_highlight_images mhi ON mh.id = mhi.highlight_id
        ORDER BY mh.created_at DESC";

$result = $conn->query($sql);

$highlights = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $highlight_id = $row["id"];
        
        // If the highlight_id is not in the array, create a new entry
        if (!isset($highlights[$highlight_id])) {
            $highlights[$highlight_id] = [
                "id" => $row["id"],
                "name" => $row["name"],
                "user_email" => $row["user_email"],
                "caption" => $row["caption"],
                "created_at" => $row["created_at"],
                "images" => []
            ];
        }

        // Add the image URL to the images array (if it exists)
        if (!empty($row["image_url"])) {
            $highlights[$highlight_id]["images"][] = $row["image_url"];
        }
    }
}

// Convert associative array to indexed array
$highlights = array_values($highlights);

// Return JSON response
header("Content-Type: application/json");
echo json_encode(["success" => true, "highlights" => $highlights]);

$conn->close();
?>
