<?php
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['request_id'])) {
    $request_id = intval($_GET['request_id']);
    $query = "SELECT * FROM trainer_request WHERE request_id = $request_id";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $request = $result->fetch_assoc();
    } else {
        echo "Request not found.";
        exit();
    }

    // Fetch trainers from users table
    $trainersQuery = "SELECT fullname FROM users WHERE role = 'trainer'";
    $trainersResult = $conn->query($trainersQuery);
    $trainers = [];
    if ($trainersResult->num_rows > 0) {
        while ($row = $trainersResult->fetch_assoc()) {
            $trainers[] = $row['fullname'];
        }
    }
} else {
    echo "Invalid request.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Trainer Request</title>
    <link rel="stylesheet" href="manageRequestStyle.css">
</head>
<body>
    <div class="form-container">
        <h2>Manage Trainer Request</h2>
        <form method="POST" action="updateRequest.php" onsubmit="return validateForm()">
            <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
            <table>
                <tr>
                    <td colspan="2"><label for="user_name">Trainee Name:</label></td>
                    <td colspan="2"><label for="trainer_name">Trainer Name:</label></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="text" id="user_name" name="user_name" value="<?php echo $request['user_name']; ?>" readonly required></td>
                    <td colspan="2"><input type="text" id="trainer_name" name="trainer_name" value="<?php echo $request['trainer_name']; ?>" readonly required></td>
                </tr>
                <tr>
                    <td colspan="2"><label for="date_of_training">Date of Training:</label></td>
                    <td colspan="2"><label for="time_start">Start Time:</label></td>
                    <td colspan="2"><label for="time_end">End Time:</label></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="date" id="date_of_training" name="date_of_training" value="<?php echo $request['date_of_training']; ?>" required></td>
                    <td colspan="2"><input type="time" id="time_start" name="time_start" value="<?php echo $request['time_start']; ?>" required></td>
                    <td colspan="2"><input type="time" id="time_end" name="time_end" value="<?php echo $request['time_end']; ?>" required></td>
                </tr>
                <tr>
                    <td colspan="2"><label for="status">Status:</label></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <select id="status" name="status" required>
                            <option value="approved" <?php echo $request['status'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="rejected" <?php echo $request['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center;">
                        <button type="submit">Update Request</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <script>
        function validateForm() {
            var status = document.getElementById('status').value;
            if (status === 'pending') {
                alert('Please change the status from pending before updating.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>