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
                Total Users: <b><?php echo $total_users; ?></b>
        </h2>
        <div class="top-bar">
                <input type="text" id="search" placeholder="Search Admin..." onkeyup="filterUsers()">
                <button class="btn btn-primary" onclick="openModal()">+ Add User</button>
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

        <!-- Popup Modal (Hidden by Default) -->
        <div id="addAdminModal" class="modal">
            <div class="modal-content">
                <span class="close-btn" onclick="closeModal()">&times;</span>
                <h2>Add New User</h2>
                <form id="addAdminForm">
                    <label for="fullname">Full Name:</label>
                    <input type="text" id="fullname" name="fullname" required placeholder="Enter your full name">

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required placeholder="Enter a valid email address">

                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required placeholder="Enter a password">

                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="" disabled selected>Select Role</option>
                        <option value="Admin">Admin</option>
                        <option value="Staff">Staff</option>
                    </select>

                    <button type="button" class="btn" onclick="submitAdmin()">Add Admin</button>
                </form>
            </div>
        </div>
    </div>

    <script src="userScript.js"></script>

</body>
</html>