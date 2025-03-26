<?php
include 'database.php';

$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$trainee_query = "SELECT * FROM attendance WHERE checkin_date = '$date' ORDER BY checkin_time ASC";
$trainee_result = mysqli_query($conn, $trainee_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance</title>
    <link rel="stylesheet" href="Attendance/attendanceStyle.css">
</head>
<body>
    
    <div class="title">
        <h1>Attendance Management</h1>
        <p>Track and manage trainee attendance records efficiently</p>
    </div>

    <div class="attendance-container">

        <div class="attendance-header">
            <h2>All Attendance</h2>
            <div class="search-date-container">
                <label for="attendance-date">Select Date:</label>
                <input type="date" id="attendance-date" value="<?php echo $date; ?>">
                <input type="text" id="search" placeholder="Search trainee..." onkeyup="searchTrainee()">
            </div>
        </div>

        <table class="attendance-table">
            <thead>
                <tr>
                    <th>Trainee</th>
                    <th>Date</th>
                    <th>Check-in Time</th>
                    <th>Check-out Time</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody id="attendance-table-body">
                <?php while ($row = mysqli_fetch_assoc($trainee_result)) { ?>
                    <tr class="trainee-row">
                        <td>
                            <img src="<?php echo $row['trainee_image']; ?>" alt="Trainee Image">
                            <span class="trainee-name"><?php echo htmlspecialchars($row['trainee_name']); ?></span>
                        </td>
                        <td><?php echo date('F d, Y', strtotime($row['checkin_date'])); ?></td>
                        <td><?php echo date('h:i A', strtotime($row['checkin_time'])); ?></td>
                        <td>
                            <?php 
                                if (!empty($row['checkout_time'])) {
                                    echo date('h:i A', strtotime($row['checkout_time']));
                                } else {
                                    echo "<span class='not-checked-out'>Not Checked Out</span>";
                                }
                            ?>
                        </td>
                        <td style="text-align: center;">
                            <button class="view-btn" data-id="<?php echo $row['id']; ?>">View</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Attendance Details</h2>
            <p><strong>Trainee Name:</strong> <span id="trainee-name"></span></p>
            <p><strong>Check-in Time:</strong> <span id="checkin-time"></span></p>
            <p><strong>Check-out Time:</strong> <span id="checkout-time"></span></p>
            <p><strong>Total Hours Stayed:</strong> <span id="total-hours"></span></p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="Attendance/attendanceScript.js"></script>
</body> 
</html>
