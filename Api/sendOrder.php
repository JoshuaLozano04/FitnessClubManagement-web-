<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include the database connection file
    include 'database.php';

    // Get the raw POST data
    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);

    // Check if all required POST parameters are set
    $missing_params = [];
    if (!isset($data['email'])) {
        $missing_params[] = 'email';
    }
    if (!isset($data['product_name'])) {
        $missing_params[] = 'product_name';
    }
    if (!isset($data['quantity'])) {
        $missing_params[] = 'quantity';
    }

    if (!empty($missing_params)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing required POST parameters: ' . implode(', ', $missing_params)
        ]);
        exit;
    }

    $user_email = $data['email'];
    $product_name = $data['product_name'];
    $quantity = intval($data['quantity']);

    // Get the user's full name using the email
    $stmt = $conn->prepare("SELECT fullname FROM users WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $stmt->bind_result($user_name);
    $stmt->fetch();
    $stmt->close();

    if (!$user_name) {
        echo json_encode([
            'status' => 'error',
            'message' => 'User not found.'
        ]);
        exit;
    }

    // Get the product price using the product name
    $stmt = $conn->prepare("SELECT price FROM inventory WHERE product_name = ?");
    $stmt->bind_param("s", $product_name);
    $stmt->execute();
    $stmt->bind_result($product_price);
    $stmt->fetch();
    $stmt->close();

    if (!$product_price) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Product not found.'
        ]);
        exit;
    }

    // Calculate the total price
    $total_price = $product_price * $quantity;

    // Get the current date for the order_date
    $order_date = date('Y-m-d H:i:s');

    // Prepare the SQL statement to insert the order
    $stmt = $conn->prepare("INSERT INTO purchase_orders (customer_name, product_name, price, quantity, status, order_date) VALUES (?, ?, ?, ?, 'pending', ?)");
    $stmt->bind_param("ssdiss", $user_name, $product_name, $total_price, $quantity, $order_date);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Order added successfully.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error: ' . $stmt->error
        ]);
    }

    // Close the statement and the database connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method. Please use POST.'
    ]);
}
?>