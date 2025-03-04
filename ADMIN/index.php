<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
</head>
<body>
    <!-- Sidebar Navigation -->
    <ul id="side-nav">
        <li class="logo">
            <img src="img/PumpingIronLogo.png" alt="Logo">
        </li>
        <!-- Navigation Links -->
        <li><a href="index.php?page=dashboard" class="<?= ($_GET['page'] ?? 'dashboard') == 'dashboard' ? 'active' : ''; ?>"><i class="ri-dashboard-3-line"></i>Dashboard</a></li>
        <li><a href="index.php?page=Members/members" class="<?= ($_GET['page'] ?? '') == 'Members/members' ? 'active' : ''; ?>"><i class="ri-user-community-line"></i>Members</a></li>
        <li><a href="index.php?page=schedule" class="<?= ($_GET['page'] ?? '') == 'schedule' ? 'active' : ''; ?>"><i class="ri-calendar-2-line"></i>Schedule</a></li>
        <li><a href="index.php?page=Inventory/inventory" class="<?= ($_GET['page'] ?? '') == 'Inventory/inventory' ? 'active' : ''; ?>"><i class="ri-store-fill"></i>Inventory</a></li>
        <li><a href="index.php?page=orders" class="<?= ($_GET['page'] ?? '') == 'orders' ? 'active' : ''; ?>"><i class="ri-shopping-bag-4-fill"></i>Orders</a></li>
        <li><a href="index.php?page=report" class="<?= ($_GET['page'] ?? '') == 'report' ? 'active' : ''; ?>"><i class="ri-bar-chart-2-fill"></i>Report</a></li>
        <li><a href="index.php?page=attendance" class="<?= ($_GET['page'] ?? '') == 'attendance' ? 'active' : ''; ?>"><i class="ri-check-line"></i>Attendance</a></li>
        <li><a href="index.php?page=users" class="<?= ($_GET['page'] ?? '') == 'users' ? 'active' : ''; ?>"><i class="ri-group-fill"></i>User Account</a></li>
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
                        'schedule' => 'Schedule',
                        'attendance' => 'Attendance',
                        'report' => 'Report',
                        'orders' => 'Orders',
                        'Inventory/inventory' => 'Inventory',
                        'Inventory/editInventory' => 'Edit Inventory',
                        'users' => 'User Account'
                    ];
                    echo "<h2>" . $page_titles[$page] . "</h2>";
                ?>
            </div>

            <!-- Right side of the header -->
            <div class="header-right">
                <button class="notification-btn">
                    <i class="ri-notification-3-line"></i>
                </button>
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
                                    <a href ="#" class="dropdown_link">
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
                        

        <?php
        $allowed_pages = ['dashboard', 'Members/members', 'Members/editMembers', 'schedule', 'attendance', 'report', 'Inventory/inventory', 'Inventory/editInventory', 'orders', 'users'];
        $page = $_GET['page'] ?? 'dashboard';
        if (in_array($page, $allowed_pages)) {
            include "$page.php";
        } else {
            echo "<h1>Page Not Found</h1>";
        }
        ?>
    </div>
</body>
</html>