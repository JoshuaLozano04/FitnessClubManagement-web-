<?php
include 'database.php';

// Handle Add Product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $stock_quantity = intval($_POST['stock_quantity']);

    $query = "INSERT INTO inventory (product_name, description, price, stock_quantity) 
              VALUES ('$product_name', '$description', '$price', '$stock_quantity')";
    mysqli_query($conn, $query);
    header("Location: index.php?page=inventory");
    exit();
}

// Fetch all inventory products
$result = mysqli_query($conn, "SELECT * FROM inventory");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <link rel="stylesheet" href="InventoryStyle.css">
</head>
<body>
    <!-- Inventory List -->
    <div class="inventory-content">
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <div class="title">
                    <h1>Inventory Management</h1>
                    <p>Manage your products and stock levels</p>
                </div>
                <div class="add-product">
                    <a href="index.php?page=Inventory/editInventory" class="add-product-btn">Add Product</a>
                </div>
                <tr>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo number_format($row['price'], 2); ?></td>
                    <td><?php echo $row['stock_quantity']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td>
                        <a href="index.php?page=Inventory/editInventory&edit=<?php echo $row['id']; ?>" class="edit-btn">Edit</a>
                        <a href="index.php?page=Inventory/editInventory&delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>
</html>

<?php mysqli_close($conn); ?>
