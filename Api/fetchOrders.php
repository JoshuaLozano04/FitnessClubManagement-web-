<?php
// filepath: c:\xampp\htdocs\PumpingIronGym\Api\fetchOrders.php

include 'database.php';

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if the email parameter is provided
    if (isset($_GET['email'])) {
        $email = $_GET['email'];

        // Fetch the fullname from the users table using the email
        $userQuery = "SELECT fullname FROM users WHERE email = ?";
        $stmt = $conn->prepare($userQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $userResult = $stmt->get_result();
        $userData = $userResult->fetch_assoc();
        $stmt->close();

        if ($userData) {
            $fullname = $userData['fullname'];

            // Fetch all purchase orders that match the fullname
            $ordersQuery = "SELECT * FROM purchase_orders WHERE user_email = ?";
            $stmt = $conn->prepare($ordersQuery);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $ordersResult = $stmt->get_result();

            $orders = [];
            while ($row = $ordersResult->fetch_assoc()) {
                $orders[] = $row;
            }
            $stmt->close();

            // Return a flat response
            echo json_encode([
                "status" => "success",
                "message" => "Orders fetched successfully.",
                "fullname" => $fullname,
                "orders" => $orders
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "User not found."
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Email parameter is required."
        ]);
    }

    $conn->close();
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method."
    ]);
}
?>