<?php
include 'database.php';

// Handle Add Product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $stock_quantity = intval($_POST['stock_quantity']);
    $product_image = $_FILES['product_image']['name'];

    if ($product_image) {
        $target_dir = "../storage/products/";
        $target_file = $target_dir . basename($product_image);
        move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file);
    }

    $query = "INSERT INTO inventory (product_name, description, price, stock_quantity, product_image) 
              VALUES ('$product_name', '$description', '$price', '$stock_quantity', '$product_image')";

    if (mysqli_query($conn, $query)) {
        echo "<script>window.location.href = 'index.php?page=Inventory/inventory';</script>";
        exit();
    } else {
        echo "Error adding product: " . mysqli_error($conn);
    }
}

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
    $product_image = $_FILES['product_image']['name'];

    if ($product_image) {
        $target_dir = "../storage/products/";
        $target_file = $target_dir . basename($product_image);
        move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file);
    } else {
        $product_image = $_POST['existing_image'];
    }

    $query = "UPDATE inventory SET 
                product_name='$product_name', 
                description='$description', 
                price='$price', 
                stock_quantity='$stock_quantity', 
                product_image='$product_image' 
                WHERE id=$id";
    mysqli_query($conn, $query);
    echo "<script>window.location.href = 'index.php?page=Inventory/inventory';</script>";
    exit();
}

// Handle Delete Product
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM inventory WHERE id=$id");
    echo "<script>window.location.href = 'index.php?page=Inventory/inventory';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Inventory</title>
    <link rel="stylesheet" href="Inventory/inventoryStyle.css">
</head>
<body>
    <div class="inventory-form">
        <h2><?php echo isset($edit_product) ? "Edit Product" : "Add New Product"; ?></h2>
        <form method="POST" action="/PumpingIronGym/ADMIN/index.php?page=Inventory/editInventory" enctype="multipart/form-data" onsubmit="return validateUpdate(event)">
            <input type="hidden" name="id" value="<?php echo isset($edit_product['id']) ? intval($edit_product['id']) : ''; ?>">
            <input type="text" id="product_name" name="product_name" placeholder="Product Name" required
                value="<?php echo isset($edit_product['product_name']) ? htmlspecialchars($edit_product['product_name']) : ''; ?>">
            <textarea id="description" name="description" placeholder="Description" required><?php echo isset($edit_product['description']) ? htmlspecialchars($edit_product['description']) : ''; ?></textarea>
            <input type="number" step="0.01" min="1" id="price" name="price" placeholder="Price" required 
                value="<?php echo isset($edit_product['price']) ? floatval($edit_product['price']) : ''; ?>">
            <input type="number" min="1" id="stock_quantity" name="stock_quantity" placeholder="Stock Quantity" required min="0"
                value="<?php echo isset($edit_product['stock_quantity']) ? intval($edit_product['stock_quantity']) : ''; ?>">
            <label for="product_image">Product Image:</label>
            <input type="file" id="product_image" name="product_image" accept="image/*">
            <?php if (isset($edit_product['product_image'])): ?>
                <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($edit_product['product_image']); ?>">
                <p>Current Image: <?php echo htmlspecialchars($edit_product['product_image']); ?></p>
            <?php endif; ?>
            <button type="submit" class="submit-button" id="submit_button" name="<?php echo isset($edit_product) ? "update_product" : "add_product"; ?>">
                <?php echo isset($edit_product) ? "Update Product" : "Add Product"; ?>
            </button>
            <button type="button" class="cancel-button" onclick="window.location.href='/PumpingIronGym/ADMIN/index.php?page=Inventory/inventory';">
                Cancel
            </button>
        </form>
    </div>
</body>
</html>