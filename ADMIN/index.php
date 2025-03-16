<?php
session_start();
include 'database.php'; // Ensure the database connection is included

// Query to check product quantities and insert notifications
$productQuery = "SELECT * FROM inventory WHERE stock_quantity <= 5";
$productResult = $conn->query($productQuery);

if ($productResult) {
    while ($product = $productResult->fetch_assoc()) {
        $message = "Product " . $product['product_name'] . " is almost out of stock (Quantity: " . $product['stock_quantity'] . ")";

        // Check if the notification already exists
        $checkQuery = $conn->prepare("SELECT COUNT(*) FROM admin_notifications WHERE message = ?");
        $checkQuery->bind_param("s", $message);
        $checkQuery->execute();
        $checkQuery->bind_result($count);
        $checkQuery->fetch();
        $checkQuery->close();

        if ($count == 0) {
            // Insert the notification if it doesn't already exist
            $stmt = $conn->prepare("INSERT INTO admin_notifications (message) VALUES (?)");
            $stmt->bind_param("s", $message);
            $stmt->execute();
            $stmt->close();
        }
    }
} else {
    echo "Error: " . $conn->error;
}

// Query to check for new trainer requests and insert notifications
$requestQuery = "SELECT * FROM trainer_request WHERE status = 'pending'";
$requestResult = $conn->query($requestQuery);

if ($requestResult) {
    while ($request = $requestResult->fetch_assoc()) {
        $message = "New trainer request from " . $request['user_name'] . " for " . date('F j, Y', strtotime($request['date_of_training'])) . " at " . $request['time_start'];

        // Check if the notification already exists
        $checkQuery = $conn->prepare("SELECT COUNT(*) FROM admin_notifications WHERE message = ?");
        $checkQuery->bind_param("s", $message);
        $checkQuery->execute();
        $checkQuery->bind_result($count);
        $checkQuery->fetch();
        $checkQuery->close();

        if ($count == 0) {
            // Insert the notification if it doesn't already exist
            $stmt = $conn->prepare("INSERT INTO admin_notifications (message) VALUES (?)");
            $stmt->bind_param("s", $message);
            $stmt->execute();
            $stmt->close();
        }
    }
} else {
    echo "Error: " . $conn->error;
}

// Query to get notifications
$notifications = $conn->query("SELECT * FROM admin_notifications ORDER BY created_at DESC");

// Query to count unread notifications
$unreadNotificationsQuery = "SELECT COUNT(*) AS unread_count FROM admin_notifications WHERE read_status = 0";
$unreadNotificationsResult = $conn->query($unreadNotificationsQuery);
$unreadNotifications = $unreadNotificationsResult->fetch_assoc();
$notificationCount = $unreadNotifications['unread_count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <style>
        .notification-btn {
            position: relative;
        }
        .notification-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
        }
        .notifications-window {
            display: none;
            position: absolute;
            right: 20px;
            top: 90px; /* Position below the button */
            background: white;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            max-height: 400px;
            overflow-y: auto;
            z-index: 1000;
        }
        .notifications-window h3 {
            margin: 0;
            padding: 10px;
            background: #f4f4f4;
            border-bottom: 1px solid #ddd;
        }
        .notifications-window ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .notifications-window li {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            position: relative;
        }
        .notifications-window li small {
            display: block;
            color: #777;
        }
        .notifications-window li .delete-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            color: red;
            font-size: 16px;
            cursor: pointer;
        }

        
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <ul id="side-nav">
        <li class="logo">
            <img src="img/PumpingIronLogo.png" alt="Logo">
        </li>
        <!-- Navigation Links -->
        <li><a href="index.php?page=dashboard" class="<?= ($_GET['page'] ?? 'dashboard') == 'dashboard' ? 'active' : ''; ?>"><i class="ri-dashboard-3-line"></i>Dashboard</a></li>
        <li><a href="index.php?page=Members/members" class="<?= ($_GET['page'] ?? '') == 'Members/members' ? 'active' : ''; ?>"><i class="ri-group-fill"></i>Members</a></li>
        <li><a href="index.php?page=Schedule/schedule" class="<?= ($_GET['page'] ?? '') == 'Schedule/schedule' ? 'active' : ''; ?>"><i class="ri-calendar-2-line"></i>Schedule</a></li>
        <li><a href="index.php?page=Inventory/inventory" class="<?= ($_GET['page'] ?? '') == 'Inventory/inventory' ? 'active' : ''; ?>"><i class="ri-store-fill"></i>Inventory</a></li>
        <li><a href="index.php?page=Orders/orders" class="<?= ($_GET['page'] ?? '') == 'Orders/orders' ? 'active' : ''; ?>"><i class="ri-shopping-bag-4-fill"></i>Orders</a></li>
        <li><a href="index.php?page=report" class="<?= ($_GET['page'] ?? '') == 'report' ? 'active' : ''; ?>"><i class="ri-bar-chart-2-fill"></i>Report</a></li>
        <li><a href="index.php?page=attendance" class="<?= ($_GET['page'] ?? '') == 'attendance' ? 'active' : ''; ?>"><i class="ri-check-line"></i>Attendance</a></li>
        <li><a href="index.php?page=Users/users" class="<?= ($_GET['page'] ?? '') == 'Users/users' ? 'active' : ''; ?>"><i class="ri-group-fill"></i>User Account</a></li>
    </ul>

    <!-- Main Content -->
    <div class="content">
        <!-- Header Section -->
        <header>
            <!-- Left side of the header -->
            <div class="header-left">
                <?php
                    // Display page title based on the selected page
                    $page = $_GET['page'] ?? 'dashboard'; // Default page is 'dashboard'
                    $page_titles = [
                        'dashboard' => 'Dashboard',
                        'Members/members' => 'Members',
                        'Members/editMembers' => 'Edit Member',
                        'Schedule/schedule' => 'Schedule',
                        'Schedule/manageRequest' => 'Manage Request',
                        'attendance' => 'Attendance',
                        'report' => 'Report',
                        'Orders/orders' => 'Orders',
                        'Inventory/inventory' => 'Inventory',
                        'Inventory/editInventory' => 'Edit Inventory',
                        'Users/users' => 'User Account'
                    ];
                    echo "<h2>" . ($page_titles[$page] ?? 'Page Not Found') . "</h2>";
                ?>
            </div>

            <!-- Right side of the header -->
            <div class="header-right">
                <button class="notification-btn" onclick="toggleNotifications()">
                    <i class="ri-notification-3-line"></i>
                    <?php if ($notificationCount > 0): ?>
                        <span class="notification-count"><?php echo $notificationCount; ?></span>
                    <?php endif; ?>
                </button>
                <div class="notifications-window" id="notificationsWindow">
                    <h3>Notifications</h3>
                    <ul>
                        <?php while ($notification = $notifications->fetch_assoc()): ?>
                            <li>
                                <?php echo $notification['message']; ?> 
                                <br><small><?php echo $notification['created_at']; ?></small>
                                <button class="delete-btn" onclick="deleteNotification(<?php echo $notification['id']; ?>)">×</button>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
                <div class="user-info">
                    <?php if (isset($_SESSION['fullname'])): ?>
                        <div class="dropdown" id="dropdown">
                            <div class="dropdown_profile">
                                <div class="user-details">
                                    <h3><?php echo "Hi, " . $_SESSION['fullname']; ?></h3>
                                    <p><?php echo $_SESSION['role']; ?></p>
                                </div>

                                <div class="dropdown_image">
                                    <img src="img/01.png" alt="image">
                                </div>
                                <i class="ri-arrow-down-s-line"></i>
                                <div class="dropdown_list">
                                    <a href ="#" class="dropdown_link">
                                        <i class="ri-user-line"></i>
                                        <span>Profile</span>
                                    </a>
                                    <a href="#" class="dropdown_link" id="changePasswordLink">
                                        <i class="ri-lock-2-line"></i>
                                        <span>Change Password</span>
                                    </a>
                                    <a href ="logout.php" id="logoutBtn" onclick="return confirmLogout()" class="dropdown_link">
                                        <i class="ri-logout-box-r-line"></i>
                                        <span>Logout</span>
                                    </a>
                                </div>
                                <script src="indexScript.js"></script>
                                <script src="logout.js"></script>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <!-- Change Password Modal -->
        <div id="changePasswordModal" class="modal">
            <div class="change-password-content">
                <span class="change-password-close-btn" id="closeChangePassword">&times;</span>
                <h2>Change Password</h2>
                <form id="changePasswordForm" method="POST" action="change_password.php">
                    <label for="currentPassword">Current Password:</label>
                    <div class="change-password-wrapper">
                        <input type="password" id="currentPassword" name="currentPassword" class="change-password-input" required placeholder="Enter current password">
                        <i class="ri-eye-off-line change-password-toggle" data-target="currentPassword"></i>
                    </div>

                    <label for="newPassword">New Password:</label>
                    <div class="change-password-wrapper">
                        <input type="password" id="newPassword" name="newPassword" class="change-password-input" required placeholder="Enter new password">
                        <i class="ri-eye-off-line change-password-toggle" data-target="newPassword"></i>
                    </div>

                    <label for="confirmPassword">Confirm New Password:</label>
                    <div class="change-password-wrapper">
                        <input type="password" id="confirmPassword" name="confirmPassword" class="change-password-input" required placeholder="Confirm new password">
                        <i class="ri-eye-off-line change-password-toggle" data-target="confirmPassword"></i>
                    </div>

                    <button type="submit" class="change-password-btn">Update Password</button>
                </form>
            </div>
        </div>

        <?php
        $allowed_pages = ['dashboard', 'Members/members', 'Members/editMembers', 'Schedule/schedule', "Schedule/manageRequest",'attendance', 'report', 'Inventory/inventory', 'Inventory/editInventory', 'Orders/orders', 'Users/users'];
        $page = $_GET['page'] ?? 'dashboard';
        if (in_array($page, $allowed_pages)) {
            include "$page.php";
        } else {
            echo "<h1>Page Not Found</h1>";
        }
        ?>
    </div>
    <script>
        function toggleNotifications() {
            const notificationsWindow = document.getElementById('notificationsWindow');
            notificationsWindow.style.display = notificationsWindow.style.display === 'none' ? 'block' : 'none';

            if (notificationsWindow.style.display === 'block') {
                // Send an AJAX request to mark notifications as read
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'mark_notifications_read.php', true);
                xhr.onload = function() {
                    if (xhr.status === 200 && xhr.responseText === 'success') {
                        // Hide the notification count
                        const notificationCount = document.querySelector('.notification-count');
                        if (notificationCount) {
                            notificationCount.style.display = 'none';
                        }
                    }
                };
                xhr.send();

                // Add event listener to close notifications window when clicking outside
                document.addEventListener('click', closeNotificationsOnClickOutside);
            } else {
                // Remove event listener when notifications window is closed
                document.removeEventListener('click', closeNotificationsOnClickOutside);
            }
        }

        function closeNotificationsOnClickOutside(event) {
            const notificationsWindow = document.getElementById('notificationsWindow');
            const notificationBtn = document.querySelector('.notification-btn');
            if (!notificationsWindow.contains(event.target) && !notificationBtn.contains(event.target)) {
                notificationsWindow.style.display = 'none';
                document.removeEventListener('click', closeNotificationsOnClickOutside);
            }
        }

        function deleteNotification(notificationId) {
            // Send an AJAX request to delete the notification
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'delete_notification.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200 && xhr.responseText === 'success') {
                    // Remove the notification from the list
                    const notificationElement = document.querySelector(`button[onclick="deleteNotification(${notificationId})"]`).parentElement;
                    notificationElement.remove();
                }
            };
            xhr.send(`id=${notificationId}`);
        }
    </script>
</body>
</html>