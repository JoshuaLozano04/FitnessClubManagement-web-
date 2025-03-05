<?php
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id'])) {
    $request_id = intval($_POST['request_id']);
    $request_date = $_POST['request_date'] ?? '';
    $start_time = $_POST['start_time'] ?? '';
    $end_time = $_POST['end_time'] ?? '';
    $status = $_POST['status'] ?? 'pending'; // Default to 'pending' if not provided

    // Validate input data
    $valid_statuses = ['pending', 'approved', 'rejected'];
    if (!in_array($status, $valid_statuses)) {
        echo "Invalid status value provided: $status<br>";
        $status = 'pending'; // Fallback to default
    }

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Check if the request_id exists and get the current status
        $checkQuery = "SELECT status FROM trainer_request WHERE request_id = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param('i', $request_id);
        $stmt->execute();
        $stmt->bind_result($current_status);
        $stmt->fetch();
        $stmt->close();

        if ($current_status === null) {
            echo "Request ID $request_id does not exist.<br>";
            throw new Exception("Request ID does not exist.");
        }

        // Debugging output
        echo "Request ID: $request_id<br>";
        echo "Current Status: $current_status<br>";
        echo "New Status: $status<br>";
        echo "Request Date: $request_date<br>";
        echo "Start Time: $start_time<br>";
        echo "End Time: $end_time<br>";

        // Update the status in the trainer_request table
        $updateStatusQuery = "UPDATE trainer_request SET status = ? WHERE request_id = ?";
        $stmt = $conn->prepare($updateStatusQuery);
        $stmt->bind_param('si', $status, $request_id);

        if (!$stmt->execute()) {
            throw new Exception("Error updating request status: " . $stmt->error);
        }

        // Check if any rows were actually updated
        if ($stmt->affected_rows === 0) {
            throw new Exception("No rows updated. The request ID may not exist or the status is unchanged.");
        }

        echo "Request status updated successfully.<br>";

        // Insert the details into the trainer_assignments table
        $insertAssignmentQuery = "
            INSERT INTO trainer_assignments 
            (request_id, user_email, user_name, trainer_email, trainer_name, assignment_date, start_time, end_time, status) 
            SELECT 
                request_id, user_email, user_name, trainer_email, trainer_name, ?, ?, ?, ? 
            FROM 
                trainer_request 
            WHERE 
                request_id = ?
        ";
        
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
        echo "Transaction rolled back: " . $e->getMessage() . "<br>";
    }
    $conn->close();

} else {
    echo "Invalid request.<br>";
}
?>
