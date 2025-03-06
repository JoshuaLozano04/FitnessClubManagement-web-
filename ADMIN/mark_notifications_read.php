<?php
include 'database.php';

// Update the read status of all notifications to 'read'
$updateQuery = "UPDATE admin_notifications SET read_status = 1 WHERE read_status = 0";
$conn->query($updateQuery);

if ($conn->affected_rows > 0) {
    echo "success";
} else {
    echo "no_changes";
}
?>