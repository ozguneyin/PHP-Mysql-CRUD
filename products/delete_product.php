<?php
require_once '../includes/config.php';

$delete_message = "";
$error_message = "";

// Check if ID is set and is a valid integer
if (isset($_GET["id"]) && !empty(trim($_GET["id"])) && filter_var(trim($_GET["id"]), FILTER_VALIDATE_INT)) {
    $product_id = trim($_GET["id"]);

    // Prepare a delete statement
    $sql = "DELETE FROM products WHERE id = ?";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $param_id);
        $param_id = $product_id;

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                // Records deleted successfully. Redirect to product list page
                $delete_message = "Product deleted successfully. <a href=\"view_products.php\">Return to Product List</a>";
                // header("location: view_products.php");
                // exit();
            } else {
                $error_message = "No product found with that ID, or product already deleted. <a href=\"view_products.php\">Return to Product List</a>";
            }
        } else {
            $error_message = "Oops! Something went wrong. Please try again later. SQL Error: " . $stmt->error . " <a href=\"view_products.php\">Return to Product List</a>";
        }
        $stmt->close();
    } else {
        $error_message = "Error preparing statement: " . $mysqli->error . " <a href=\"view_products.php\">Return to Product List</a>";
    }
    $mysqli->close();
} else {
    // URL doesn't contain id parameter or it's invalid
    $error_message = "Invalid request. No product ID specified or ID is invalid. <a href=\"view_products.php\">Return to Product List</a>";
    // Optionally redirect to an error page or the product list
    // header("location: error.php");
    // exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Product</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; color: #333; text-align: center; }
        .container { width: 50%; margin: 50px auto; background-color: #fff; padding: 30px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #333; }
        .message { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Delete Product Status</h2>
        <?php if (!empty($delete_message)): ?>
            <div class="message success">
                <?php echo $delete_message; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="message error">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <p><a href="view_products.php">Go back to Product List</a></p>
    </div>
</body>
</html>
