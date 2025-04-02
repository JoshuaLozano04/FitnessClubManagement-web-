<?php
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST['customer_name'];
    $order_date = $_POST['order_date'];
    $product_id = $_POST['product_id'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $status = $_POST['status'];

    // Fetch product name from inventory using product_id
    $product_query = "SELECT product_name FROM inventory WHERE id = ?";
    $stmt = $conn->prepare($product_query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($product_name);
    $stmt->fetch();
    $stmt->close();

    // Insert order into purchase_orders table
    $query = "INSERT INTO purchase_orders (customer_name, order_date, product_name, price, quantity, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssdis", $customer_name, $order_date, $product_name, $price, $quantity, $status);

    if ($stmt->execute()) {
        header("Location: /PumpingIronGym/ADMIN/index.php?page=Orders/orders&success=Order added successfully");
        exit(); // Ensure the script stops after redirect
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>