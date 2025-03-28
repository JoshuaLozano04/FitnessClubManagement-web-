<?php
require 'database.php'; // Ensure you have a connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if request_id is set
    if (isset($_POST['request_id'])) {
        $request_id = $_POST['request_id'];

        // Prepare and execute the delete query
        $stmt = $conn->prepare("DELETE FROM trainer_request WHERE request_id = ?");
        $stmt->bind_param("i", $request_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Appointment deleted successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to delete appointment."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Invalid request."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

$conn->close();
?>
