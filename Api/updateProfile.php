<?php
// Include database connection
include 'db_connection.php';

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
            // Check whether the 'profiles' directory exists, if not, create it
            if (!is_dir('profiles')) {
                mkdir('profiles', 0777, true);
            }

            // Move the uploaded file to the 'profiles' directory
            $new_filename = uniqid() . "." . $ext;
            move_uploaded_file($_FILES['profile_picture']['tmp_name'], "profiles/" . $new_filename);

            // Update the user's profile picture in the database
            $email = $_POST['email']; // Assuming email is sent via POST
            $sql = "UPDATE users SET profile_picture = ? WHERE email = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param('ss', $new_filename, $email);
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
            echo "Error: There was a problem uploading your file. Please try again.";
        }
    } else {
        echo "Error: " . $_FILES['profile_picture']['error'];
    }
}
?>