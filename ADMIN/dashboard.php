<!-- filepath: c:\xampp\htdocs\PumpingIronGym\ADMIN\dashboard.php -->
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
                <h3>Trainees</h3>
                <p>191</p>
                <div class="details">
                    <span>New 90</span>
                    <span>Returning 27</span>
                    <span>Inactive 74</span>
                </div>
            </div>
            <div class="stat">
                <h3>Total Trainers</h3>
                <p>60</p>
            </div>
            <div class="stat">
                <h3>Total Orders</h3>
                <p>00</p>
            </div>
            <div class="stat">
                <h3>Total Revenues</h3>
                <p>₱50K</p>
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
                        <th></th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Sales</th>
                        <th>Stocks</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><img src="img/product.png" alt="Product 1"></td>
                        <td>Optimum Nutrition's Gold Standard...</td>
                        <td>₱3,990.00</td>
                        <td>20</td>
                        <td>30</td>
                        <td class="in-stock">In Stock</td>
                    </tr>
                    <tr>
                        <td><img src="img/product.png" alt="Product 2"></td>
                        <td>Optimum Nutrition Micronized Creat...</td>
                        <td>₱1,500.00</td>
                        <td>50</td>
                        <td>0</td>
                        <td class="out-of-stock">Out of Stock</td>
                    </tr>
                    <tr>
                        <td><img src="img/product.png" alt="Product 3"></td>
                        <td>Optimum Nutrition Serious Mass</td>
                        <td>₱2,410.00</td>
                        <td>40</td>
                        <td>5</td>
                        <td class="restock">Restock</td>
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
                    <tr>
                        <td>Jacob Jones</td>
                        <td>Jan 24, 2025</td>
                        <td>Optimum Nutrition's Gold Standard 100% Whey</td>
                        <td>₱3,990.00</td>
                        <td>1</td>
                        <td>Picked Up</td>
                    </tr>
                    <tr>
                        <td>Kathryn Murphy</td>
                        <td>Jan 24, 2025</td>
                        <td>Optimum Nutrition's Gold Standard 100% Whey</td>
                        <td>₱3,990.00</td>
                        <td>1</td>
                        <td>Picked Up</td>
                    </tr>
                    <tr>
                        <td>Ronald Richards</td>
                        <td>Jan 24, 2025</td>
                        <td>Optimum Nutrition's Gold Standard 100% Whey</td>
                        <td>₱3,990.00</td>
                        <td>1</td>
                        <td>Picked Up</td>
                    </tr>
                    <tr>
                        <td>Jerome Bell</td>
                        <td>Jan 24, 2025</td>
                        <td>Optimum Nutrition's Gold Standard 100% Whey</td>
                        <td>₱3,990.00</td>
                        <td>1</td>
                        <td>Picked Up</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script src="scripts.js"></script>
</body>
</html>