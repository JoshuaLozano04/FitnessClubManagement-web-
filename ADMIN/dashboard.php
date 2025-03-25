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

// Fetch monthly revenue
$monthlyRevenueResult = mysqli_query($conn, "
    SELECT 
        DATE_FORMAT(order_date, '%Y-%m') as month, 
        SUM(price) as total_revenue 
    FROM purchase_orders 
    GROUP BY DATE_FORMAT(order_date, '%Y-%m')
    ORDER BY month ASC
");

$monthlyRevenueData = [];
while ($row = mysqli_fetch_assoc($monthlyRevenueResult)) {
    $monthlyRevenueData[] = $row;
}

// Prepare data for Chart.js
$months = [];
$revenues = [];
foreach ($monthlyRevenueData as $data) {
    $months[] = $data['month'];
    $revenues[] = $data['total_revenue'];
}
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
                        <th>‎ </th>
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
                    <td>
                        <?php if ($row['stock_quantity'] > 6) : ?>
                            <span class="in-stock">In Stock</span>
                        <?php elseif ($row['stock_quantity'] >= 5) : ?>
                            <span class="restock">Restock</span>
                        <?php else : ?>
                            <span class="out-of-stock">Out of Stock</span>
                        <?php endif; ?>
                    </td>
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
    <script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [{
                label: 'Monthly Revenue',
                data: <?php echo json_encode($revenues); ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 2,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Allows you to control the height and width
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        boxWidth: 10, // Reduce the size of legend boxes
                        font: {
                            size: 10 // Reduce font size for legend
                        }
                    }
                }
            },
            layout: {
                padding: {
                    top: 10,
                    bottom: 10,
                    left: 10,
                    right: 10
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Month',
                        font: {
                            size: 12 // Reduce font size for x-axis title
                        }
                    },
                    ticks: {
                        font: {
                            size: 10 // Reduce font size for x-axis labels
                        }
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Revenue (₱)',
                        font: {
                            size: 12 // Reduce font size for y-axis title
                        }
                    },
                    ticks: {
                        font: {
                            size: 10 // Reduce font size for y-axis labels
                        },
                        beginAtZero: true
                    }
                }
            }
        }
    });
    </script>
</body>
</html>