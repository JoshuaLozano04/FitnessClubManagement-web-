<?php
include 'database.php';

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        // Fetch inventory product by ID
        $id = intval($_GET['id']);
        $query = "SELECT * FROM inventory WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);

        // Execute the statement
        if ($stmt->execute()) {
            // Get the result
            $result = $stmt->get_result();
            $inventory = [];
            if ($row = $result->fetch_assoc()) {
                $inventory[] = $row;
                echo json_encode([
                    'status' => 'success',
                    'data' => $inventory
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Product not found'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error fetching inventory: ' . $stmt->error
            ]);
        }
    } else {
        // Fetch all inventory products
        $query = "SELECT * FROM inventory";
        $stmt = $conn->prepare($query);

        // Execute the statement
        if ($stmt->execute()) {
            // Get the result
            $result = $stmt->get_result();

            $inventory = [];
            while ($row = $result->fetch_assoc()) {
                $inventory[] = $row;
            }

            echo json_encode([
                'status' => 'success',
                'data' => $inventory
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error fetching inventory: ' . $stmt->error
            ]);
        }
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method. Please use GET.'
    ]);
}

// Close the database connection
mysqli_close($conn);
?>