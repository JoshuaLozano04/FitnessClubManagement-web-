<?php
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id'])) {
    $request_id = intval($_POST['request_id']);
    $request_date = $_POST['request_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $status = $_POST['status'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Update the status in the trainer_request table
        $updateStatusQuery = "UPDATE trainer_request SET status = ? WHERE request_id = ?";
        $stmt = $conn->prepare($updateStatusQuery);
        $stmt->bind_param('si', $status, $request_id);

        if (!$stmt->execute()) {
            throw new Exception("Error updating request status: " . $stmt->error);
        } else {
            echo "Request status updated successfully.<br>";
        }

        // Insert the details into the trainer_assignments table
        $insertAssignmentQuery = "INSERT INTO trainer_assignments (request_id, user_email, user_name, trainer_email, trainer_name, assignment_date, start_time, end_time, status) SELECT request_id, user_email, user_name, trainer_email, trainer_name, ?, ?, ?, ? FROM trainer_request WHERE request_id = ?";
        $stmt = $conn->prepare($insertAssignmentQuery);
        $stmt->bind_param('ssssi', $request_date, $start_time, $end_time, $status, $request_id);

        if (!$stmt->execute()) {
            throw new Exception("Error creating assignment: " . $stmt->error);
        } else {
            echo "Assignment created successfully.<br>";
        }

        // Commit the transaction
        $conn->commit();
        echo "Transaction committed successfully.<br>";

        // Redirect to the schedule page
        header("Location: ../index.php?page=Schedule/schedule");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction if any error occurs
        $conn->rollback();
        echo "Transaction rolled back: " . $e->getMessage();
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>