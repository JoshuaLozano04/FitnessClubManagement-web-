<?php
include 'database.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $deleteQuery = $conn->prepare("DELETE FROM admin_notifications WHERE id = ?");
    $deleteQuery->bind_param("i", $id);
    $deleteQuery->execute();

    if ($deleteQuery->affected_rows > 0) {
        echo "success";
    } else {
        echo "error";
    }

    $deleteQuery->close();
} else {
    echo "error";
}
?>