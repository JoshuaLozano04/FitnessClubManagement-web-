<?php
include 'database.php'; // Ensure the database connection is included

// Query to get the counts of inactive, active, and returning members with the role "member"
$inactiveMembersQuery = "SELECT COUNT(*) as count FROM users WHERE status = 'inactive' AND role = 'member'";
$activeMembersQuery = "SELECT COUNT(*) as count FROM users WHERE status = 'active' AND role = 'member'";
$returningMembersQuery = "SELECT COUNT(*) as count FROM users WHERE status = 'returning' AND role = 'member'";

$inactiveMembersResult = $conn->query($inactiveMembersQuery);
$activeMembersResult = $conn->query($activeMembersQuery);
$returningMembersResult = $conn->query($returningMembersQuery);

if ($inactiveMembersResult && $activeMembersResult && $returningMembersResult) {
    $inactiveMembersCount = $inactiveMembersResult->fetch_assoc()['count'];
    $activeMembersCount = $activeMembersResult->fetch_assoc()['count'];
    $returningMembersCount = $returningMembersResult->fetch_assoc()['count'];
} else {
    $inactiveMembersCount = 0;
    $activeMembersCount = 0;
    $returningMembersCount = 0;
}

// Query to get the count of trainers
$trainersQuery = "SELECT COUNT(*) as count FROM users WHERE role = 'trainer'";
$trainersResult = $conn->query($trainersQuery);

if ($trainersResult) {
    $trainersCount = $trainersResult->fetch_assoc()['count'];
} else {
    $trainersCount = 0;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        #membersPieChart {
            background-color:rgb(255, 255, 255);
            padding: 5px;
            border-radius: 10px;
            margin-top: 35px;
            max-width: 200px; /* Adjust the size as needed */
            max-height: 200px; /* Adjust the size as needed */
        }
        #trainersCounter {
            background-color:rgb(255, 255, 255);
            padding: 10px;
            border-radius: 10px;
            margin-top: 35px;
            max-width: 200px;/* Adjust the size as needed */
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-left: 20px;
        }
        body {
            background-color: #f1f1f1;
        }
        #membersChartContainer {
            display: flex;
            justify-content: space-around;
            flex-direction: row;
            align-items: center;
            max-width: fit-content;
        }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
</head>
<body>
    <p style="margin-top: 20px;">Welcome to the dashboard</p>

    <div id="membersChartContainer">
        <canvas id="membersPieChart" width="50" height="50"></canvas>
        <div id="trainersCounter" class="ri-user-2-fill"></div>
        
    </div>

    <script>
        // Data from PHP
        const inactiveMembersCount = <?php echo $inactiveMembersCount; ?>;
        const activeMembersCount = <?php echo $activeMembersCount; ?>;
        const returningMembersCount = <?php echo $returningMembersCount; ?>;
        const trainersCount = <?php echo $trainersCount; ?>;

        // Data for the pie chart
        const data = {
            labels: ['Inactive Members', 'Active Members', 'Returning Members'],
            datasets: [{
                label: 'Members',
                data: [inactiveMembersCount, activeMembersCount, returningMembersCount],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(75, 192, 192, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        };

        // Configuration options for the pie chart
        const config = {
            type: 'pie',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Members Distribution'
                    }
                }
            },
        };

        // Render the pie chart
        const membersPieChart = new Chart(
            document.getElementById('membersPieChart'),
            config
        );

        // Display the trainers count
        document.getElementById('trainersCounter').innerText = `${trainersCount} Trainers`;
    </script>
</body>
</html>