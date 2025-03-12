<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule</title>
    <link rel="stylesheet" href="Schedule/scheduleStyle.css">
</head>
<body>
    <p>Welcome to the schedule page</p>
    <?php
    include 'database.php';

    // Fetch all trainer assignments for the calendar
    $assignments = [];
    $result = $conn->query("SELECT * FROM trainer_assignments");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $endDateTime = strtotime($row['assignment_date'] . ' ' . $row['end_time']);
            $currentDateTime = time();
            if ($endDateTime < $currentDateTime) {
                // Delete the assignment if the end time has passed
                $deleteQuery = $conn->prepare("DELETE FROM trainer_assignments WHERE id = ?");
                $deleteQuery->bind_param('i', $row['id']);
                $deleteQuery->execute();
                $deleteQuery->close();
            } elseif ($row['status'] !== 'rejected') {
                // Only add assignments that are not rejected
                $assignments[] = $row;
            }
        }
    }

    // Fetch all trainer requests
    $result = $conn->query("SELECT * FROM trainer_request");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $endDateTime = strtotime($row['date_of_training'] . ' ' . $row['time_end']);
            $currentDateTime = time();
            if ($endDateTime < $currentDateTime) {
                // Delete the request if the end time has passed
                $deleteQuery = $conn->prepare("DELETE FROM trainer_request WHERE request_id = ?");
                $deleteQuery->bind_param('i', $row['request_id']);
                $deleteQuery->execute();
                $deleteQuery->close();
            }
        }
    }

    // Get the current month and year
    $currentMonth = date('m');
    $currentYear = date('Y');
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
    $firstDayOfMonth = date('w', strtotime("$currentYear-$currentMonth-01"));

    echo "<div class='calendar-container'>";
    echo "<div class='calendar-label'>Trainer Assignments for " . date('F Y') . "</div>";
    echo "<div class='calendar'>";
    echo "<div class='calendar-day-header'>Sun</div>";
    echo "<div class='calendar-day-header'>Mon</div>";
    echo "<div class='calendar-day-header'>Tue</div>";
    echo "<div class='calendar-day-header'>Wed</div>";
    echo "<div class='calendar-day-header'>Thu</div>";
    echo "<div class='calendar-day-header'>Fri</div>";
    echo "<div class='calendar-day-header'>Sat</div>";

    // Print empty days for the first week
    for ($i = 0; $i < $firstDayOfMonth; $i++) {
        echo "<div class='calendar-day'></div>";
    }

    // Print days of the month
    for ($day = 1; $day <= $daysInMonth; $day++) {
        echo "<div class='calendar-day'>";
        echo "<div class='calendar-day-number'>$day</div>";
        
        // Print assignments for the day
        foreach ($assignments as $assignment) {
            if (date('Y-m-d', strtotime($assignment['assignment_date'])) == "$currentYear-$currentMonth-$day") {
                echo "<div class='assignment'>";
                echo $assignment['trainer_name'] . " (" . $assignment['start_time'] . " - " . $assignment['end_time'] . ")";
                echo "</div>";
            }
        }

        echo "</div>";
    }

    echo "</div>";
    echo "</div>";

    // Fetch all trainer requests again to display them
    $result = $conn->query("SELECT * FROM trainer_request");

    if ($result->num_rows > 0) {
        echo "<div class='table-container'>";
        echo "<div class='table-label'>Trainer Requests</div>";
        echo "<table>";
        echo "<tr class='table-header'><td><strong>Trainee Name</strong></td><td><strong>Trainer Name</strong></td><td><Strong>Date of Training</Strong></td><td><Strong>Time Start</Strong></td><td><Strong>Time End</Strong></td><td><Strong>Description</Strong></td><td><Strong>Request Date</Strong></td><td><Strong>Status</Strong></td><td><Strong>Actions</Strong></td></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['user_name'] . "</td>";
            echo "<td>" . $row['trainer_name'] . "</td>";
            echo "<td>" . date('F j, Y', strtotime($row['date_of_training'])) . "</td>";
            echo "<td>" . $row['time_start'] . "</td>";
            echo "<td>" . $row['time_end'] . "</td>";
            echo "<td>" . $row['description'] . "</td>";
            echo "<td>" . date('F j, Y', strtotime($row['request_date'])) . "</td>";
            echo "<td>" . $row['status'] . "</td>";
            $buttonText = $row['status'] == 'approved' ? 'Edit' : 'Manage';
            echo "<td>
                    <a href='Schedule/manageRequest.php?request_id=" . $row['request_id'] . "'><button type='button' class='manage-button'>$buttonText</button></a>
                  </td>";
            echo "</tr>";
        }

        echo "</table>";
        echo "</div>";
    } else {
        echo "<p>No trainer requests found.</p>";
    }

    // Fetch all trainer assignments again to display them
    $result = $conn->query("SELECT * FROM trainer_assignments");

    if ($result->num_rows > 0) {
        echo "<div class='table-container'>";
        echo "<div class='table-label'>Trainer Assignments</div>";
        echo "<table>";
        echo "<tr class='table-header'><td><strong>Trainee Name</strong></td><td><Strong>Trainer Name</Strong></td><td><Strong>Scheduled Date</Strong></td><td><Strong>Start Time</Strong></td><td><Strong>End Time</Strong></td><td><Strong>Status</Strong></td></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['user_name'] . "</td>";
            echo "<td>" . $row['trainer_name'] . "</td>";
            echo "<td>" . date('F j, Y', strtotime($row['assignment_date'])) . "</td>";
            echo "<td>" . $row['start_time'] . "</td>";
            echo "<td>" . $row['end_time'] . "</td>";
            echo "<td>" . $row['status'] . "</td>";
            echo "</tr>";
        }

        echo "</table>";
        echo "</div>";
    } else {
        echo "<p>No trainer assignments found.</p>";
    }

    $conn->close();
    ?>
</body>
</html>