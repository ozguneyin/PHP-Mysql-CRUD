<?php
require_once '../includes/config.php';

$name = $description = $price = $sku = $image_url = "";
$name_err = $price_err = $sku_err = $image_url_err = "";
$product_id = null;

// Check if ID is set and is a valid integer for GET request (initial load)
if (isset($_GET["id"]) && !empty(trim($_GET["id"])) && filter_var(trim($_GET["id"]), FILTER_VALIDATE_INT)) {
    $product_id = trim($_GET["id"]);

    $sql = "SELECT id, name, description, price, sku, image_url FROM products WHERE id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $param_id);
        $param_id = $product_id;

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $name = $row["name"];
                $description = $row["description"];
                $price = $row["price"];
                $sku = $row["sku"];
                $image_url = $row["image_url"];
            } else {
                echo "Product not found.";
                exit();
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $mysqli->error;
        exit();
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && !empty(trim($_POST["id"])) && filter_var(trim($_POST["id"]), FILTER_VALIDATE_INT)) {
    // Process update after form submission
    $product_id = trim($_POST["id"]);

    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter a product name.";
    } else {
        $name = sanitize_input($_POST["name"]);
    }

    // Validate price
    if (empty(trim($_POST["price"]))) {
        $price_err = "Please enter the price.";
    } elseif (!is_numeric(trim($_POST["price"])) || floatval(trim($_POST["price"])) < 0) {
        $price_err = "Please enter a valid positive price.";
    } else {
        $price = trim($_POST["price"]);
    }

    // Validate SKU (optional, but if provided, check for uniqueness against other products)
    if (!empty(trim($_POST["sku"]))) {
        $sku = sanitize_input($_POST["sku"]);
        $sql_check_sku = "SELECT id FROM products WHERE sku = ? AND id != ?";
        if ($stmt_check_sku = $mysqli->prepare($sql_check_sku)) {
            $stmt_check_sku->bind_param("si", $param_sku, $param_id);
            $param_sku = $sku;
            $param_id = $product_id;
            if ($stmt_check_sku->execute()) {
                $stmt_check_sku->store_result();
                if ($stmt_check_sku->num_rows > 0) {
                    $sku_err = "This SKU is already taken by another product.";
                }
            }
            $stmt_check_sku->close();
        }
    } else {
        $sku = null; // Allow SKU to be optional
    }

    $description = sanitize_input($_POST["description"]);

    // Validate Image URL (optional, basic validation)
    if (!empty(trim($_POST["image_url"]))) {
        if (!filter_var(trim($_POST["image_url"]), FILTER_VALIDATE_URL)) {
            $image_url_err = "Please enter a valid URL for the image.";
        } else {
            $image_url = trim($_POST["image_url"]);
        }
    } else {
        $image_url = null; // Allow image_url to be optional
    }

    // Reload current values if errors occur, to prefill the form
    if(!empty($name_err) || !empty($price_err) || !empty($sku_err) || !empty($image_url_err)){
        // Values from POST are already set or re-set to $name, $price etc.
        // No need to re-fetch from DB if there are validation errors on POST
    }

    // Check input errors before updating in database
    if (empty($name_err) && empty($price_err) && empty($sku_err) && empty($image_url_err)) {
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, sku = ?, image_url = ? WHERE id = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("ssdssi", $param_name, $param_description, $param_price, $param_sku, $param_image_url, $param_id);

            $param_name = $name;
            $param_description = $description;
            $param_price = $price;
            $param_sku = $sku;
            $param_image_url = $image_url;
            $param_id = $product_id;

            if ($stmt->execute()) {
                echo "<p>Product updated successfully! <a href=\"view_products.php\">View All Products</a></p>";
                // header("location: view_products.php");
                // exit();
            } else {
                echo "Something went wrong. Please try again later. SQL Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    // $mysqli->close(); // Keep connection open if form is re-displayed with errors

} else {
    // This block handles cases where ID is not provided correctly in GET or POST
    if (empty(trim($_GET["id"])) && $_SERVER["REQUEST_METHOD"] != "POST") {
        echo "Error: No product ID specified for update.";
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Product</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; color: #333; }
        .wrapper { width: 450px; padding: 20px; margin: auto; background-color: #fff; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"], textarea { width: calc(100% - 22px); padding: 10px; border: 1px solid #ddd; border-radius: 3px; }
        textarea { resize: vertical; min-height: 80px; }
        .help-block { color: red; font-size: 0.9em; }
        .btn { padding: 10px 15px; background-color: #ffc107; color: black; border: none; border-radius: 3px; cursor: pointer; font-size: 1em; }
        .btn:hover { background-color: #e0a800; }
        .text-center { text-align: center; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Update Product</h2>
        <p>Please edit the input values and submit to update the product record.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="id" value="<?php echo $product_id; ?>"/>
            <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                <label>Product Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
                <span class="help-block"><?php echo $name_err; ?></span>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description"><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            <div class="form-group <?php echo (!empty($price_err)) ? 'has-error' : ''; ?>">
                <label>Price</label>
                <input type="number" name="price" step="0.01" value="<?php echo htmlspecialchars($price); ?>">
                <span class="help-block"><?php echo $price_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($sku_err)) ? 'has-error' : ''; ?>">
                <label>SKU (Stock Keeping Unit - Optional)</label>
                <input type="text" name="sku" value="<?php echo htmlspecialchars($sku ?? ''); ?>">
                <span class="help-block"><?php echo $sku_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($image_url_err)) ? 'has-error' : ''; ?>">
                <label>Image URL (Optional)</label>
                <input type="text" name="image_url" value="<?php echo htmlspecialchars($image_url ?? ''); ?>">
                <span class="help-block"><?php echo $image_url_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn" value="Update Product">
            </div>
            <p class="text-center"><a href="view_products.php">Back to Product List</a> | <a href="../public/index.php">Back to Home</a></p>
        </form>
    </div>
</body>
</html>
<?php if($_SERVER["REQUEST_METHOD"] != "POST" || !empty($name_err) || !empty($price_err) || !empty($sku_err) || !empty($image_url_err) ) { $mysqli->close(); } ?>
