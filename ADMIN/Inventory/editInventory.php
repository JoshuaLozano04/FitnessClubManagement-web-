<?php
include 'database.php';
// Handle Edit Product
$edit_id = null;
$edit_product = null;

if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_result = mysqli_query($conn, "SELECT * FROM inventory WHERE id=$edit_id");
    $edit_product = mysqli_fetch_assoc($edit_result);
}

// Handle Update Product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    $id = intval($_POST['id']);
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $stock_quantity = intval($_POST['stock_quantity']);

    $query = "UPDATE inventory SET 
                product_name='$product_name', 
                description='$description', 
                price='$price', 
                stock_quantity='$stock_quantity' 
              WHERE id=$id";
    mysqli_query($conn, $query);
    header("Location: index.php?page=Inventory/inventory");
    exit();
}

// Handle Delete Product
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM inventory WHERE id=$id");
    header("Location: index.php?page=Inventory/inventory");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="Inventory/InventoryStyle.css">
</head>
<body>
    <div class="inventory-form">
        <h2><?php echo $edit_product ? "Edit Product" : "Add New Product"; ?></h2>
        <form method="POST" action="/PumpingIronGym/ADMIN/index.php?page=Inventory/inventory">
            <input type="hidden" name="id" value="<?php echo $edit_product['id'] ?? ''; ?>">
            <input type="text" name="product_name" placeholder="Product Name" required 
                    value="<?php echo $edit_product['product_name'] ?? ''; ?>">
            <textarea name="description" placeholder="Description"><?php echo $edit_product['description'] ?? ''; ?></textarea>
            <input type="number" step="0.01" name="price" placeholder="Price" required 
                    value="<?php echo $edit_product['price'] ?? ''; ?>">
            <input type="number" name="stock_quantity" placeholder="Stock Quantity" required 
                    value="<?php echo $edit_product['stock_quantity'] ?? ''; ?>">
            <button type="submit" name="<?php echo $edit_product ? "update_product" : "add_product"; ?>">
                <?php echo $edit_product ? "Update Product" : "Add Product"; ?>
            </button>
        </form>
    </div>
</body>
</html>