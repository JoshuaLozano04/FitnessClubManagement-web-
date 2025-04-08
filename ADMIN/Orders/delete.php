<?php
include 'database.php'; // Adjust the path to include the database connection

if (isset($_GET['id'])) {
    $delete_id = intval($_GET['id']); // Sanitize input
    if ($delete_id > 0) { // Validate input
        $deleteQuery = $conn->prepare("DELETE FROM purchase_orders WHERE id = ?");
        $deleteQuery->bind_param("i", $delete_id);

        if ($deleteQuery->execute()) {
            header("Location: ../index.php?page=Orders/orders&success=Order deleted successfully");
            exit;
        } else {
            header("Location: ../index.php?page=Orders/orders&error=Error deleting order");
            exit;
        }
        $deleteQuery->close();
    } else {
        header("Location: ../index.php?page=Orders/orders&error=Invalid order ID");
        exit;
    }
} else {
    header("Location: ../index.php?page=Orders/orders&error=No order ID provided");
    exit;
}
?>