<?php
require_once '../includes/config.php';

// Attempt select query execution
$sql = "SELECT id, name, description, price, sku, image_url, created_at FROM products ORDER BY created_at DESC";
$products = [];
if ($result = $mysqli->query($sql)) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        $result->free();
    } else {
        $no_products_message = "<p class='no-data'><em>No products found.</em></p>";
    }
} else {
    echo "ERROR: Could not able to execute $sql. " . $mysqli->error;
}

// $mysqli->close(); // Connection might be needed for other operations on the same page or included files
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Products</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; color: #333; }
        .container { width: 90%; margin: auto; background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        td img.product-image { max-width: 100px; max-height: 100px; border-radius: 3px; }
        .actions a { margin-right: 10px; text-decoration: none; padding: 5px 10px; border-radius: 3px; }
        .actions .edit { background-color: #ffc107; color: black; }
        .actions .delete { background-color: #dc3545; color: white; }
        .actions .edit:hover { background-color: #e0a800; }
        .actions .delete:hover { background-color: #c82333; }
        .add-product-link { display: inline-block; margin-bottom: 20px; padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 3px; }
        .add-product-link:hover { background-color: #218838; }
        .no-data { text-align: center; color: #777; font-style: italic; }
        .nav-links { text-align: center; margin-bottom: 20px;}
        .nav-links a { margin: 0 10px; text-decoration: none; color: #007bff; font-weight: bold;}
        .nav-links a:hover { text-decoration: underline; }
        .description-col { max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Product Catalog</h2>
        <div class="nav-links">
            <a href="../public/index.php">Home</a>
            <a href="../users/view_users.php">View Users</a>
        </div>
        <a href="create_product.php" class="add-product-link">Add New Product</a>
        <?php if (!empty($products)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th class="description-col">Description</th>
                        <th>Price</th>
                        <th>SKU</th>
                        <th>Added At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product["id"]); ?></td>
                        <td>
                            <?php if (!empty($product["image_url"]) && filter_var($product["image_url"], FILTER_VALIDATE_URL)): ?>
                                <img src="<?php echo htmlspecialchars($product["image_url"]); ?>" alt="<?php echo htmlspecialchars($product["name"]); ?>" class="product-image">
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($product["name"]); ?></td>
                        <td class="description-col" title="<?php echo htmlspecialchars($product["description"]); ?>"><?php echo htmlspecialchars($product["description"]); ?></td>
                        <td>$<?php echo htmlspecialchars(number_format($product["price"], 2)); ?></td>
                        <td><?php echo htmlspecialchars($product["sku"] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($product["created_at"]); ?></td>
                        <td class="actions">
                            <a href="update_product.php?id=<?php echo $product["id"]; ?>" class="edit">Edit</a>
                            <a href="delete_product.php?id=<?php echo $product["id"]; ?>" class="delete" onclick="return confirm(\'Are you sure you want to delete this product? This action cannot be undone.\");">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <?php echo $no_products_message ?? 
            "<p class=\"no-data\"><em>No products found. Click \'Add New Product\' to create one.</em></p>"; ?>
        <?php endif; ?>
    </div>
</body>
</html>
<?php $mysqli->close(); ?>
