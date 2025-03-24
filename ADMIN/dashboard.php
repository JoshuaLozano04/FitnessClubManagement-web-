<?php
include 'database.php';

// Fetch all inventory products
$result = mysqli_query($conn, "SELECT * FROM inventory");

// Fetch the number of members
$memberCountResult = mysqli_query($conn, "SELECT COUNT(*) as member_count FROM users WHERE role = 'member'");
$memberCountRow = mysqli_fetch_assoc($memberCountResult);
$memberCount = $memberCountRow['member_count'];

// Fetch the number of trainers
$trainerCountResult = mysqli_query($conn, "SELECT COUNT(*) as trainer_count FROM users WHERE role = 'trainer'");
$trainerCountRow = mysqli_fetch_assoc($trainerCountResult);
$trainerCount = $trainerCountRow['trainer_count'];

// Fetch the number of orders
$orderCountResult = mysqli_query($conn, "SELECT COUNT(*) as order_count FROM purchase_orders");
$orderCountRow = mysqli_fetch_assoc($orderCountResult);
$orderCount = $orderCountRow['order_count'];

// Fetch the total revenue
$totalRevenueResult = mysqli_query($conn, "SELECT SUM(price) as total_revenue FROM purchase_orders");
$totalRevenueRow = mysqli_fetch_assoc($totalRevenueResult);
$totalRevenue = $totalRevenueRow['total_revenue'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="dashboard">
        <div class="stats">
            <div class="stat">
                <h3>Members</h3>
                <p><?php echo $memberCount; ?></p>
            </div>
            <div class="stat">
                <h3>Total Trainers</h3>
                <p><?php echo $trainerCount; ?></p>
            </div>
            <div class="stat">
                <h3>Total Orders</h3>
                <p><?php echo $orderCount; ?></p>
            </div>
            <div class="stat">
                <h3>Total Revenues</h3>
                <p>₱<?php echo number_format($totalRevenue, 2); ?></p>
            </div>
        </div>
        <div class="charts">
            <canvas id="revenueChart"></canvas>
        </div>
        <div class="stocks">
            <h3>Stocks</h3>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Stocks</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td>
                        <?php   
                        $productImagePath = '../storage/products/' . htmlspecialchars($row['product_image']);
                        if (file_exists($productImagePath)) {
                            echo "<img src='$productImagePath' alt='Product Image' width='50' height='50'>";
                        } else {
                            echo "Image not found: $productImagePath";
                        }
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo number_format($row['price'], 2); ?></td>
                    <td><?php echo $row['stock_quantity']; ?></td>
                </tr>
                <?php endwhile; ?>
                <tr>
                </tr>        
                </tbody>
            </table>
        </div>
        <div class="orders">
            <h3>Recent Orders</h3>
            <table>
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Order Date</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $orderResult = mysqli_query($conn, "SELECT * FROM purchase_orders ORDER BY order_date DESC LIMIT 5");
                    while ($orderRow = mysqli_fetch_assoc($orderResult)) :
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($orderRow['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($orderRow['order_date']); ?></td>
                        <td><?php echo htmlspecialchars($orderRow['product_name']); ?></td>
                        <td><?php echo number_format($orderRow['price'], 2); ?></td>
                        <td><?php echo $orderRow['quantity']; ?></td>
                        <td><?php echo htmlspecialchars($orderRow['status']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="scripts.js"></script>
</body>
</html>