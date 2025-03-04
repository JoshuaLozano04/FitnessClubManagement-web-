<?php
include 'database.php';

$id = $_GET['id'];
$sql = "DELETE FROM users WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    header("Location: users.php");
    exit();
} else {
    echo "Error deleting record: " . $conn->error;
}
?>
