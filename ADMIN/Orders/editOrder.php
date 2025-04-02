<?php
include 'database.php';

// Handle Edit Order
$edit_id = null;
$edit_order = null;

if (isset($_GET['edit'])) {  
    $edit_id = intval($_GET['edit']);  
    $edit_result = mysqli_query($conn, "SELECT * FROM purchase_orders WHERE id=$edit_id");
    $edit_order = mysqli_fetch_assoc($edit_result);
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

    $query = "UPDATE purchase_orders SET 
                customer_name='$customer_name', 
                order_date='$order_date', 
                product_name='$product_name', 
                price='$price', 
                quantity='$quantity', 
                status='$status' 
                WHERE id=$id";

    mysqli_query($conn, $query);
    echo "<script>window.location.href = 'index.php?page=Orders/orders';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order</title>
    <link rel="stylesheet" href="orders/orderStyles.css">
</head>
<body>
    <div class="order-form">
        <h2>Edit Order</h2>
        <form method="POST" action="/PumpingIronGym/ADMIN/index.php?page=Orders/editOrder">
            <input type="hidden" name="id" value="<?php echo isset($edit_order['id']) ? intval($edit_order['id']) : ''; ?>">
            <input type="text" name="customer_name" placeholder="Customer Name" required 
                value="<?php echo isset($edit_order['customer_name']) ? htmlspecialchars($edit_order['customer_name']) : ''; ?>">
            <input type="date" name="order_date" required 
                value="<?php echo isset($edit_order['order_date']) ? htmlspecialchars($edit_order['order_date']) : ''; ?>">
            <input type="text" name="product_name" placeholder="Product Name" required 
                value="<?php echo isset($edit_order['product_name']) ? htmlspecialchars($edit_order['product_name']) : ''; ?>">
            <input type="number" step="0.01" min="1" name="price" placeholder="Price" required 
                value="<?php echo isset($edit_order['price']) ? floatval($edit_order['price']) : ''; ?>">
            <input type="number" min="1" name="quantity" placeholder="Quantity" required 
                value="<?php echo isset($edit_order['quantity']) ? intval($edit_order['quantity']) : ''; ?>">
            <label for="status">Order Status:</label>
            <select name="status" required>
                <option value="" disabled>Select Status</option>
                <option value="Pending" <?php echo (isset($edit_order['status']) && $edit_order['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="Ready for Pickup" <?php echo (isset($edit_order['status']) && $edit_order['status'] == 'Ready for Pickup') ? 'selected' : ''; ?>>Ready for Pickup</option>
                <option value="Picked Up" <?php echo (isset($edit_order['status']) && $edit_order['status'] == 'Picked Up') ? 'selected' : ''; ?>>Picked Up</option>
                <option value="Unclaimed" <?php echo (isset($edit_order['status']) && $edit_order['status'] == 'Unclaimed') ? 'selected' : ''; ?>>Unclaimed</option>
            </select>
            <button type="submit" name="update_order">Update Order</button>
            <button type="button" onclick="window.location.href='/PumpingIronGym/ADMIN/index.php?page=Orders/orders';">Cancel</button>
        </form>
    </div>
</body>
</html>