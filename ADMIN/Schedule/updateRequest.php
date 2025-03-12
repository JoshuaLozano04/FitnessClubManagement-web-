<?php
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id'])) {
    $request_id = intval($_POST['request_id']);
    $trainer_name = $_POST['trainer_name'] ?? '';
    $status = $_POST['status'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Check if the request_id exists and get the current status
        $checkQuery = "SELECT status, date_of_training, time_start, time_end, user_email, user_name FROM trainer_request WHERE request_id = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param('i', $request_id);
        $stmt->execute();
        $stmt->bind_result($current_status, $date_of_training, $time_start, $time_end, $user_email, $user_name);
        $stmt->fetch();
        $stmt->close();

        if ($current_status === null) {
            echo "Request ID $request_id does not exist.<br>";
            throw new Exception("Request ID does not exist.");
        }

        // Update the status in the trainer_request table only if a new status is provided
        if (!empty($status)) {
            $updateStatusQuery = "UPDATE trainer_request SET status = ? WHERE request_id = ?";
            $stmt = $conn->prepare($updateStatusQuery);
            $stmt->bind_param('si', $status, $request_id);

            if (!$stmt->execute()) {
                throw new Exception("Error updating request status: " . $stmt->error);
            }

            echo "Request status updated successfully.<br>";
        }

        // Fetch the trainer's email based on the trainer's name
        $trainerEmailQuery = "SELECT email FROM users WHERE fullname = ? AND role = 'trainer'";
        $stmt = $conn->prepare($trainerEmailQuery);
        $stmt->bind_param('s', $trainer_name);
        $stmt->execute();
        $stmt->bind_result($trainer_email);
        $stmt->fetch();
        $stmt->close();

        if ($trainer_email === null) {
            throw new Exception("Trainer email not found for the selected trainer.");
        }

        // Check if an assignment already exists for this request
        $checkAssignmentQuery = "SELECT COUNT(*) FROM trainer_assignments WHERE request_id = ?";
        $stmt = $conn->prepare($checkAssignmentQuery);
        $stmt->bind_param('i', $request_id);
        $stmt->execute();
        $stmt->bind_result($assignment_count);
        $stmt->fetch();
        $stmt->close();

        if ($assignment_count > 0) {
            // Update the existing assignment
            $updateAssignmentQuery = "
                UPDATE trainer_assignments 
                SET user_email = ?, user_name = ?, trainer_email = ?, trainer_name = ?, assignment_date = ?, start_time = ?, end_time = ?, status = ?
                WHERE request_id = ?
            ";
            $stmt = $conn->prepare($updateAssignmentQuery);
            $stmt->bind_param('ssssssssi', $user_email, $user_name, $trainer_email, $trainer_name, $date_of_training, $time_start, $time_end, $status, $request_id);

            if (!$stmt->execute()) {
                throw new Exception("Error updating assignment: " . $stmt->error);
            } else {
                echo "Assignment updated successfully.<br>";
            }
        } else {
            // Insert a new assignment
            $insertAssignmentQuery = "
                INSERT INTO trainer_assignments 
                (request_id, user_email, user_name, trainer_email, trainer_name, assignment_date, start_time, end_time, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ";
            $stmt = $conn->prepare($insertAssignmentQuery);
            $stmt->bind_param('issssssss', $request_id, $user_email, $user_name, $trainer_email, $trainer_name, $date_of_training, $time_start, $time_end, $status);

            if (!$stmt->execute()) {
                throw new Exception("Error creating assignment: " . $stmt->error);
            } else {
                echo "Assignment created successfully.<br>";
            }
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
