<?php
include 'database.php';

// Check if a search term and status filter are provided
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

// Build the query
$query = "SELECT * FROM users WHERE role = 'member'";
$conditions = [];
if ($searchTerm) {
    $conditions[] = "(fullname LIKE '%$searchTerm%')";
}
if ($statusFilter) {
    $conditions[] = "status = '$statusFilter'";
}
if ($conditions) {
    $query .= " AND " . implode(" AND ", $conditions);  
}

$result = mysqli_query($conn, $query);

// Check if query was successful
if (!$result) {
    echo "Error: " . mysqli_error($conn);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="Members/memberStyle.css">
</head>
<body>
    <div class="title">
        <h1>User Management</h1>
        <p>Manage your gym members and their membership status</p>
    </div>

    <!-- Main Content -->
    <div class="members-content">
        <header>
            <h2>All Users</h2>

            <!-- Search Bar -->
            <form method="GET" action="/PumpingIronGym/ADMIN/index.php">
                <input type="hidden" name="page" value="Members/members">
                <input type="text" name="search" placeholder="Search by Name" class="search-input" 
                value="<?php echo isset($_GET['search']) ? ($_GET['search']) : ''; ?>">

                <select name="status" class="filter-input">
                    <option value="">Filter by Status</option>
                    <option value="active" <?php echo isset($_GET['status']) && $_GET['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo isset($_GET['status']) && $_GET['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>

                <button type="submit" class="search-button">Search</button>
            </form>

        </header>

        <!-- Members Table -->
        <table>
            <thead class="table-header">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Membership Start</th>
                    <th>Membership End</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch and display members from the database
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . ($row['id']) . "</td>";
                    echo "<td>" . ($row['fullname']) . "</td>";
                    echo "<td>" . ($row['membership_start']) . "</td>";
                    echo "<td>" . ($row['membership_end']) . "</td>";
                    echo "<td>" . ($row['role']) . "</td>"; 
                    echo "<td>" . ($row['status']) . "</td>";
                    echo "<td><a href='Members/editMembers.php?id=" . $row['id'] . "' class='edit-button'>Edit</a></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>