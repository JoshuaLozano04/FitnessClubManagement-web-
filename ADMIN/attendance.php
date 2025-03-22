<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance</title>
    <link rel="stylesheet" href="attendanceStyle.css">
</head>
<body>
    <div class="attendance-container">
        <h1>Attendance</h1>
        <div class="search-container">
            <input type="text" id="search" placeholder="Search">
        </div>
        <div class="trainee-cards">
            <div class="trainee-card active">
                <img src="img/user.png" alt="Trainee Image">
                <p>Brooklyn Simmons</p>
            </div>
            <div class="trainee-card"> 
                <img src="img/user.png" alt="Trainee Image">
                <p>Brooklyn Simmons</p>
            </div>
            <div class="trainee-card">
                <img src="img/user.png" alt="Trainee Image">
                <p>Brooklyn Simmons</p>
            </div>
            <div class="trainee-card">
                <img src="img/user.png" alt="Trainee Image">
                <p>Brooklyn Simmons</p>
            </div>
            <div class="trainee-card">
                <img src="img/user.png" alt="Trainee Image">
                <p>Brooklyn Simmons</p>
            </div>
        </div>
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>Trainee</th>
                    <th>Date</th>
                    <th>Check-in Time</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <img src="img/user.png" alt="Trainee Image">
                        Jacob Jones
                    </td>
                    <td>January 01, 2025</td>
                    <td>12:00 PM</td>
                    <td><button class="view-btn">View</button></td>
                </tr>
                <tr>
                    <td>
                        <img src="img/user.png" alt="Trainee Image">
                        Kathryn Murphy
                    </td>
                    <td>January 01, 2025</td>
                    <td>12:00 PM</td>
                    <td><button class="view-btn">View</button></td>
                </tr>
                <tr>
                    <td>
                        <img src="img/user.png" alt="Trainee Image">
                        Ronald Richards
                    </td>
                    <td>January 01, 2025</td>
                    <td>12:00 PM</td>
                    <td><button class="view-btn">View</button></td>
                </tr>
                <tr>
                    <td>
                        <img src="img/user.png" alt="Trainee Image">
                        Jerome Bell
                    </td>
                    <td>January 01, 2025</td>
                    <td>12:00 PM</td>
                    <td><button class="view-btn">View</button></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>