<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include the database connection file
    include 'database.php';

    // Check if all required POST parameters are set
    if (isset($_POST['email'], $_POST['name'], $_POST['product_name'], $_POST['product_price'], $_POST['quantity'], $_POST['status'])) {
        $user_email = $_POST['email'];
        $user_name = $_POST['name'];
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price'];
        $quantity = $_POST['quantity'];
        $order_status = $_POST['status'];

        // Calculate the total price
        $total_price = $product_price * $quantity;

        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO Orders (user_email, user_name, product_name, product_price, order_date, quantity, order_status) VALUES (?, ?, ?, ?, NOW(), ?, ?)");
        $stmt->bind_param("sssdis", $user_email, $user_name, $product_name, $total_price, $quantity, $order_status);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Order added successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement and the database connection
        $stmt->close();
        $conn->close();
    } else {
        echo "Error: Missing required POST parameters.";
    }
} else {
    echo "Invalid request method.";
}
?>