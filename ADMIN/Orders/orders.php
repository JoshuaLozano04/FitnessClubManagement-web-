<?php
include 'database.php';

// Fetch total number of orders
$total_orders_query = "SELECT COUNT(*) AS total FROM purchase_orders";
$total_orders_result = $conn->query($total_orders_query);
$total_orders_row = $total_orders_result->fetch_assoc();
$total_orders = $total_orders_row['total'];

// Fetch products from inventory
$product_query = "SELECT id, product_name, price FROM inventory"; // Ensure table name is correct
$product_result = $conn->query($product_query);
$products = [];

while ($product_row = $product_result->fetch_assoc()) {
    $products[] = $product_row;
}

// Delete order if delete parameter is set
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $deleteQuery = $conn->prepare("DELETE FROM purchase_orders WHERE id = ?");
    $deleteQuery->bind_param("i", $delete_id);

    if ($deleteQuery->execute()) {
        header("Location: orders.php?success=Order deleted successfully");
        exit;
    } else {
        echo "Error deleting order.";
    }
}

// Fetch orders from the database
$sql = "SELECT * FROM purchase_orders ORDER BY order_date DESC";
$result = $conn->query($sql);

// but oCheck if status is "Ready for Pickup" and send notification
while ($row = $result->fetch_assoc()) {
    if ($row['status'] === "Ready for Pickup") {
        $order_id = $row['id'];
        $product_name = $row['product_name'];
        $user_email = $row['user_email']; // Assuming this column exists in the database
        $status = $row['status'];

        // Prepare the notification message
        $notification_message = "Your $product_name is now $status";

        // Insert notification into the notification table
        $notificationQuery = $conn->prepare("INSERT INTO notification (email, message) VALUES (?, ?)");
        $notificationQuery->bind_param("ss", $user_email, $notification_message);


        if (!$notificationQuery->execute()) {
            echo "Error sending notification for order ID: $order_id";
        }
    }
}

// Fetch orders from the database
$sql = "SELECT * FROM purchase_orders ORDER BY order_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Orders</title>
    <link rel="stylesheet" href="orders/orderStyles.css">
</head>
<body>
    <div class="title">
        <h1>Orders Management</h1>
        <p>Manage and track customer orders efficiently</p>
    </div>

    <div class="container">
        <div class="header-order">
            <h2>Total Orders: <strong><?php echo $total_orders; ?></strong></h2>
            <div class="header-actions-order">
                <div class="search-bar">
                    <input type="text" id="orderSearch" placeholder="Search Orders...">
                </div>
                <button class="add-button" onclick="openModal()">+ Add Purchase Order</button>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Order Date</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
		            <th>Total Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { 
                        $total_price = $row['price'] * $row['quantity'];
                ?>
                <tr>
                    <td><?php echo $row['customer_name']; ?></td>
                    <td><?php echo $row['order_date']; ?></td>
                    <td><?php echo $row['product_name']; ?></td>
                    <td>₱<?php echo number_format($row['price'], 2); ?></td>
                    <td><?php echo $row['quantity']; ?></td>
		            <td>₱<?php echo number_format($total_price, 2); ?></td> <!-- Display total price -->
                    <td class="status <?php echo strtolower(str_replace(' ', '-', $row['status'])); ?>"><?php echo $row['status']; ?></td>
                    <td>
                        <a href="index.php?page=Orders/editOrder&edit=<?php echo $row['id']; ?>" class="edit-btn">Edit</a>
                        <a href="index.php?page=Orders/editOrder&delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Add Order Form -->
    <div id="orderForm" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal()">&times;</button>
            <form action="Orders/add_order.php" method="POST">
                <h2>Add Purchase Order</h2>
                <label for="customer_name">Customer Name:</label>
                <input type="text" id="customer_name" name="customer_name" placeholder="Enter customer name" required>
                <label for="order_date">Order Date:</label>
                <input type="date" id="order_date" name="order_date" required>
                <label for="product_id">Product Name:</label>
                <select id="product_id" name="product_id" required onchange="updatePrice()">
                    <option value="" disabled selected>Select Product</option>
                    <?php foreach ($products as $product) { ?>
                        <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>">
                            <?php echo $product['product_name']; ?>
                        </option>
                    <?php } ?>
                </select>
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" placeholder="Enter price" required readonly>
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" placeholder="Enter quantity" required>
                <label for="status">Order Status:</label>
                <select id="status" name="status" required>
                    <option value="" disabled selected>Select Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Ready for Pickup">Ready for Pickup</option>
                    <option value="Picked Up">Picked Up</option>
                    <option value="Unclaimed">Unclaimed</option>
                </select>

                <button class="add-order-button" type="submit">Add Order</button>
            </form>
        </div>
    </div>
    <script src="Orders/orderScript.js"></script>
</body>
</html>