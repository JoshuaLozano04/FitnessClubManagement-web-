<?php
function addMonthlyHighlight($conn, $user_email, $caption, $image_urls) {
    // Start a transaction to ensure both tables are updated
    $conn->begin_transaction();
    
    try {
        // Insert into monthly_highlights table
        $stmt = $conn->prepare("INSERT INTO monthly_highlights (user_email, caption) VALUES (?, ?)");
        $stmt->bind_param("ss", $user_email, $caption);
        $stmt->execute();
        $highlight_id = $stmt->insert_id; // Get the last inserted ID
        $stmt->close();

        // Insert images into monthly_highlight_images table
        $stmt = $conn->prepare("INSERT INTO monthly_highlight_images (highlight_id, image_url) VALUES (?, ?)");
        foreach ($image_urls as $image_url) {
            $stmt->bind_param("is", $highlight_id, $image_url);
            $stmt->execute();
        }
        $stmt->close();

        // Commit transaction
        $conn->commit();
        return ["success" => true, "message" => "Post added successfully!"];
    } catch (Exception $e) {
        $conn->rollback(); // Rollback on error
        return ["success" => false, "message" => "Error: " . $e->getMessage()];
    }
}

require "database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_email = $_POST['user_email'];
    $caption = $_POST['caption'];
    $image_urls = [];

    // Check if user_email exists in users table
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "Error: User email does not exist."]);
        exit;
    }
    $stmt->close();

    // Ensure the uploads directory exists and is writable
    $uploads_dir = "uploads";
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0777, true);
    }

    // Debugging: Check contents of $_FILES array
    if (empty($_FILES)) {
        echo json_encode(["success" => false, "message" => "No files received", "files" => $_FILES]);
        exit;
    }

    // Handle file uploads
    if (isset($_FILES['image_urls']['tmp_name']) && is_array($_FILES['image_urls']['tmp_name'])) {
        foreach ($_FILES['image_urls']['tmp_name'] as $key => $tmp_name) {
            $file_info = pathinfo($_FILES['image_urls']['name'][$key]);
            $file_name = $file_info['basename'];
            $target_file = $uploads_dir . "/" . $file_name;

            // Check if the file is a valid image
            $image_info = getimagesize($tmp_name);
            if ($image_info !== false) {
                if (move_uploaded_file($tmp_name, $target_file)) {
                    $image_urls[] = $target_file;
                } else {
                    echo json_encode(["success" => false, "message" => "Error: Failed to move uploaded file."]);
                    exit;
                }
            } else {
                echo json_encode(["success" => false, "message" => "Error: Only image files are allowed."]);
                exit;
            }
        }
    } else {
        echo json_encode(["success" => false, "message" => "Error: No files uploaded."]);
        exit;
    }

    // Debugging: Check if image_urls array is populated
    if (empty($image_urls)) {
        echo json_encode(["success" => false, "message" => "Error: No images uploaded."]);
        exit;
    }

    $result = addMonthlyHighlight($conn, $user_email, $caption, $image_urls);
    echo json_encode($result); // Output response as JSON
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>
