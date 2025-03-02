<?php
    include 'database.php';

    $users = $conn->query("SELECT * FROM users WHERE role = 'Admin'");
    $total_users = $users->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="userStyle.css">
</head>
<body>
    <div class="main-content">
    <h2 style="font-size: 24px; color: #333; margin-bottom: 15px;">
        Total Admins: <b><?php echo $total_users; ?></b>
    </h2>
        <div class="top-bar">
            <input type="text" id="search" placeholder="Search Admin..." onkeyup="filterUsers()">
            <button class="btn btn-primary" onclick="window.location.href='#'">+ Add Admin</button>
        </div>

        <div class="admin-container">
            <?php while ($user = $users->fetch_assoc()): ?>
                <div class="admin-info">
                    <div class="admin-img">
                        <img src="default-avatar.png" alt="Admin Profile">
                    </div>
                    <div class="admin-details">
                        <p><b>Name:</b> <?php echo $user['fullname']; ?></p>
                        <p><b>Email:</b> <?php echo $user['email']; ?></p>
                        <p><b>Role:</b> <?php echo $user['role']; ?></p>
                        <p><b>Status:</b>
                        <p><b>Access:</b>
                    </div>
                    <div class="admin-actions">
                        <a href="#?id=<?php echo $user['id']; ?>" class="edit-btn">Edit</a>
                        <a href="deleteUsers.php?id=<?php echo $user['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?');">Delete</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

    </div>

    <script src="userScript.js"></script>

</body>
</html>