<?php
// Include database connection
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if file was uploaded without errors
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_picture']['name'];
        $filetype = $_FILES['profile_picture']['type'];
        $filesize = $_FILES['profile_picture']['size'];

        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!in_array($ext, $allowed)) {
            die("Error: Please select a valid file format.");
        }

        // Verify file size - 10MB maximum
        if ($filesize > 10 * 1024 * 1024) {
            die("Error: File size is larger than the allowed limit.");
        }

        // Verify MIME type of the file
        if (in_array($filetype, ['image/jpeg', 'image/png', 'image/gif'])) {
            $directory = __DIR__ . '/../storage/profiles'; // Correct directory path
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            // Move the uploaded file to the 'storage/profiles' directory
            $new_filename = uniqid() . "." . $ext;
            $destination = $directory . "/" . $new_filename;
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $destination)) {
                // Update the user's profile picture in the database
                $email = $_POST['email']; // Assuming email is sent via POST
                $sql = "UPDATE users SET profile_picture = ? WHERE email = ?";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param('ss', $new_filename, $email); // Save only the file name
                    if ($stmt->execute()) {
                        echo "Profile picture updated successfully.";
                    } else {
                        echo "Error: Could not update profile picture.";
                    }
                    $stmt->close();
                } else {
                    echo "Error: Could not prepare the SQL statement.";
                }
            } else {
                echo "Error: There was a problem moving the uploaded file.";
            }
        } else {
            echo "Error: There was a problem uploading your file. Please try again.";
        }
    } else {
        echo "Error: " . $_FILES['profile_picture']['error'];
    }
}
?>