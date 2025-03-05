<?php
include 'database.php';

// Initialize $edit_product variable
$edit_product = null;

// Fetch all inventory products
$result = mysqli_query($conn, "SELECT * FROM inventory");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <link rel="stylesheet" href="Inventory/inventoryStyle.css">
</head>
<body>
    <div class="title">
        <h1>Inventory Management</h1>
        <p>Manage your products and stock levels</p>
    </div>

    <!-- Inventory List -->
    <div class="inventory-content">
        <header>
        <h2>All Products</h2>
            <div class="search-add-container">
                <input type="text" id="search" placeholder="Search Product..." onkeyup="filterProducts()">
                <a href="index.php?page=Inventory/editInventory" class="add-btn">+ Add Product</a>
            </div>
        </header>

        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody">
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo number_format($row['price'], 2); ?></td>
                    <td><?php echo $row['stock_quantity']; ?></td>
                    <td>
                        <a href="index.php?page=Inventory/editInventory&edit=<?php echo $row['id']; ?>" class="edit-btn">Edit</a>
                        <a href="index.php?page=Inventory/editInventory&delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="Inventory/inventoryScript.js"></script>
    
</body>
</html>

<?php mysqli_close($conn); ?>
