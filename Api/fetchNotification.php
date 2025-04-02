<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'database.php';

    // Get email from form-urlencoded data
    $email = $_POST['email'] ?? null;

    if ($email) {
        $conn->begin_transaction();

        try {
            $stmt = $conn->prepare("SELECT * FROM notification WHERE email = ? AND is_read = FALSE ORDER BY created_at DESC");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            $notifications = array();
            while ($row = $result->fetch_assoc()) {
                $notifications[] = array(
                    'id' => $row['id'],
                    'message' => $row['message'],
                    'created_at' => $row['created_at']
                );
            }

            // Update notifications to mark them as read
            if (!empty($notifications)) {
                $updateStmt = $conn->prepare("UPDATE notification SET is_read = TRUE WHERE email = ? AND is_read = FALSE");
                $updateStmt->bind_param("s", $email);
                $updateStmt->execute();
                $updateStmt->close();
            }

            $conn->commit();

            echo json_encode([
                "status" => "success",
                "notifications" => $notifications
            ]);

            $stmt->close();
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode([
                "status" => "error",
                "message" => "Error processing notifications: " . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Email is required."
        ]);
    }
    $conn->close();
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method."
    ]);
}
?>
