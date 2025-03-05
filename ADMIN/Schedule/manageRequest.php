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
        <form method="POST" action="updateRequest.php">
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
                    <td colspan="2"><label for="request_date">Date:</label></td>
                    <td colspan="2"><label for="start_time">Time:</label></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="date" id="request_date" name="request_date" required></td>
                    <td colspan="2"><input type="time" id="start_time" name="start_time" required></td>
                    <td colspan="2"><input type="time" id="end_time" name="end_time" required></td>
                </tr>
                <tr>
                    <td colspan="2"><label for="status">Status:</label></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <select id="status" name="status" required>
                            <option value="pending" <?php echo $request['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
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
</body>
</html>