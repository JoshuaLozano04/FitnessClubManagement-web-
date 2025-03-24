<?php
header('Content-Type: application/json'); // Ensure JSON response
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_picture']['name'];
        $filetype = $_FILES['profile_picture']['type'];
        $filesize = $_FILES['profile_picture']['size'];

        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!in_array($ext, $allowed)) {
            echo json_encode(["status" => "error", "message" => "Invalid file format."]);
            exit;
        }

        if ($filesize > 10 * 1024 * 1024) {
            echo json_encode(["status" => "error", "message" => "File size exceeds 10MB."]);
            exit;
        }

        if (in_array($filetype, ['image/jpeg', 'image/png', 'image/gif'])) {
            $directory = __DIR__ . '/../storage/profiles';
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            $new_filename = uniqid() . "." . $ext;
            $destination = $directory . "/" . $new_filename;
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $destination)) {
                $email = $_POST['email'];
                $sql = "UPDATE users SET profile_picture = ? WHERE email = ?";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param('ss', $new_filename, $email);
                    if ($stmt->execute()) {
                        echo json_encode(["status" => "success", "message" => "Profile picture updated.", "profile_picture" => $new_filename]);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Database update failed."]);
                    }
                    $stmt->close();
                } else {
                    echo json_encode(["status" => "error", "message" => "SQL preparation error."]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "File move failed."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid MIME type."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No file uploaded."]);
    }
}
?>