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
        body {
            background-color: #f1f1f1;
        }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <p style="margin-top: 20px;">Welcome to the dashboard</p>
    
    <canvas id="membersPieChart" width="50" height="50"></canvas>
    
    <?php
    include 'database.php'; // Ensure this line has a semicolon

    // Query to get the counts of inactive and active members
    $inactiveMembersQuery = "SELECT COUNT(*) as count FROM users WHERE status = 'inactive'";
    $activeMembersQuery = "SELECT COUNT(*) as count FROM users WHERE status = 'active'";
    $returningMembersQuery = "SELECT COUNT(*) as count FROM users WHERE status = 'returning'";

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

    $conn->close();
    ?>

    <script>
        // Data from PHP
        const inactiveMembersCount = <?php echo $inactiveMembersCount; ?>;
        const activeMembersCount = <?php echo $activeMembersCount; ?>;
        const returningMembersCount = <?php echo $returningMembersCount; ?>;

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
    </script>
</body>
</html>