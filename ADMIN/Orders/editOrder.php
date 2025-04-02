<?php
// filepath: c:\xampp\htdocs\PumpingIronGym\ADMIN\Orders\editOrder.php
include '../database.php';

// Handle Edit Order
$edit_id = null;
$edit_order = null;

if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_result = mysqli_query($conn, "SELECT * FROM purchase_orders WHERE id=$edit_id");
    $edit_order = mysqli_fetch_assoc($edit_result);

    if (!$edit_order) {
        echo "Order not found.";
        exit;
    }
}

// Handle Update Order
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_order'])) {
    $id = intval($_POST['id']);
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $order_date = mysqli_real_escape_string($conn, $_POST['order_date']);
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $query = "UPDATE purchase_orders 
              SET customer_name='$customer_name', 
                  order_date='$order_date', 
                  product_name='$product_name', 
                  price='$price', 
                  quantity='$quantity', 
                  status='$status' 
              WHERE id=$id";

    if (mysqli_query($conn, $query)) {
        header("Location: orders.php?success=Order updated successfully");
        exit();
    } else {
        echo "Error updating order: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order</title>
    <link rel="stylesheet" href="orderStyles.css">
</head>
<body>
    <div class="orders-form">
        <h2>Edit Order</h2>
        <form method="POST" action="editOrder.php">
            <input type="hidden" name="id" value="<?php echo isset($edit_order['id']) ? intval($edit_order['id']) : ''; ?>">

            <label for="customer_name">Customer Name:</label>
            <input type="text" id="customer_name" name="customer_name" placeholder="Customer Name" required
                value="<?php echo isset($edit_order['customer_name']) ? htmlspecialchars($edit_order['customer_name']) : ''; ?>">

            <label for="order_date">Order Date:</label>
            <input type="date" id="order_date" name="order_date" required
                value="<?php echo isset($edit_order['order_date']) ? htmlspecialchars($edit_order['order_date']) : ''; ?>">

            <label for="product_name">Product Name:</label>
            <input type="text" id="product_name" name="product_name" placeholder="Product Name" required
                value="<?php echo isset($edit_order['product_name']) ? htmlspecialchars($edit_order['product_name']) : ''; ?>">

            <label for="price">Price:</label>
            <input type="number" step="0.01" min="1" id="price" name="price" placeholder="Price" required 
                value="<?php echo isset($edit_order['price']) ? floatval($edit_order['price']) : ''; ?>">

            <label for="quantity">Quantity:</label>
            <input type="number" min="1" id="quantity" name="quantity" placeholder="Quantity" required
                value="<?php echo isset($edit_order['quantity']) ? intval($edit_order['quantity']) : ''; ?>">

            <label for="status">Order Status:</label>
            <select id="status" name="status" required>
                <option value="Pending" <?php echo isset($edit_order['status']) && $edit_order['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="Ready for Pickup" <?php echo isset($edit_order['status']) && $edit_order['status'] === 'Ready for Pickup' ? 'selected' : ''; ?>>Ready for Pickup</option>
                <option value="Picked Up" <?php echo isset($edit_order['status']) && $edit_order['status'] === 'Picked Up' ? 'selected' : ''; ?>>Picked Up</option>
                <option value="Unclaimed" <?php echo isset($edit_order['status']) && $edit_order['status'] === 'Unclaimed' ? 'selected' : ''; ?>>Unclaimed</option>
            </select>

            <button type="submit" class="submit-button" name="update_order">Update Order</button>
            <button type="button" class="cancel-button" onclick="window.location.href='orders.php';">Cancel</button>
        </form>
    </div>
</body>
</html>