<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule</title>
    <link rel="stylesheet" href="Schedule/scheduleStyle.css">
    <style>
        body {
            background: #f2f2f2;
        }
        .calendar-container, .table-container {
            background-color: rgb(255, 255, 255);
            padding: 20px;
            margin-top: 20px;
            border-radius: 10px;
        }
        .calendar-label, .table-label {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid black;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .manage-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .manage-button:hover {
            background-color: #0056b3;
        }
        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
        }
        .calendar-day {
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .calendar-day-header {
            font-weight: bold;
            text-align: center;
        }
        .assignment {
            background-color: #007bff;
            color: white;
            padding: 5px;
            border-radius: 3px;
            margin-top: 5px;
        }
    </style>
</head>
<body>

    <?php
    include 'database.php';

    // Fetch all trainer assignments for the calendar
    $assignments = [];
    $result = $conn->query("SELECT * FROM trainer_assignments");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $assignments[] = $row;
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

    // Fetch all trainer requests
    $result = $conn->query("SELECT * FROM trainer_request");

    if ($result->num_rows > 0) {
        echo "<div class='table-container'>";
        echo "<div class='table-label'>Trainer Requests</div>";
        echo "<table>";
        echo "<tr class='table-header'><td><strong>Trainee Name</strong></td><td><Strong>Trainer Name</Strong></td><td><Strong>Request Date</Strong></td><td><Strong>Status</Strong></td><td><Strong>Actions</Strong></td></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['user_name'] . "</td>";
            echo "<td>" . $row['trainer_name'] . "</td>";
            echo "<td>" . $row['request_date'] . "</td>";
            echo "<td>" . $row['status'] . "</td>";
            echo "<td>
                    <a href='Schedule/manageRequest.php?request_id=" . $row['request_id'] . "'><button type='button' class='manage-button'>Manage</button></a>
                  </td>";
            echo "</tr>";
        }

        echo "</table>";
        echo "</div>";
    } else {
        echo "<p>No trainer requests found.</p>";
    }

    // Fetch all trainer assignments
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
            echo "<td>" . $row['assignment_date'] . "</td>";
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