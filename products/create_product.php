<?php
require_once '../includes/config.php';

$name = $description = $price = $sku = $image_url = "";
$name_err = $price_err = $sku_err = $image_url_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    // Validate SKU (optional, but if provided, check for uniqueness)
    if (!empty(trim($_POST["sku"]))) {
        $sku = sanitize_input($_POST["sku"]);
        $sql_check_sku = "SELECT id FROM products WHERE sku = ?";
        if ($stmt_check_sku = $mysqli->prepare($sql_check_sku)) {
            $stmt_check_sku->bind_param("s", $param_sku);
            $param_sku = $sku;
            if ($stmt_check_sku->execute()) {
                $stmt_check_sku->store_result();
                if ($stmt_check_sku->num_rows > 0) {
                    $sku_err = "This SKU is already taken.";
                }
            }
            $stmt_check_sku->close();
        }
    } else {
        $sku = null; // Allow SKU to be optional
    }

    // Description is optional
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

    // Check input errors before inserting in database
    if (empty($name_err) && empty($price_err) && empty($sku_err) && empty($image_url_err)) {
        $sql = "INSERT INTO products (name, description, price, sku, image_url) VALUES (?, ?, ?, ?, ?)";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("ssdss", $param_name, $param_description, $param_price, $param_sku, $param_image_url);

            $param_name = $name;
            $param_description = $description;
            $param_price = $price;
            $param_sku = $sku;
            $param_image_url = $image_url;

            if ($stmt->execute()) {
                echo "<p>Product added successfully!</p><p><a href=\"view_products.php\">View All Products</a></p><p><a href=\"../public/index.php\">Go to Home</a></p>";
                // header("location: view_products.php");
                // exit;
            } else {
                echo "Something went wrong. Please try again later. SQL Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    // $mysqli->close(); // Keep connection open if form is re-displayed with errors
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; color: #333; }
        .wrapper { width: 450px; padding: 20px; margin: auto; background-color: #fff; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"], textarea { width: calc(100% - 22px); padding: 10px; border: 1px solid #ddd; border-radius: 3px; }
        textarea { resize: vertical; min-height: 80px; }
        .help-block { color: red; font-size: 0.9em; }
        .btn { padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 1em; }
        .btn:hover { background-color: #218838; }
        .text-center { text-align: center; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Add New Product</h2>
        <p>Please fill this form to add a new product to the catalog.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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
                <input type="text" name="sku" value="<?php echo htmlspecialchars($sku); ?>">
                <span class="help-block"><?php echo $sku_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($image_url_err)) ? 'has-error' : ''; ?>">
                <label>Image URL (Optional)</label>
                <input type="text" name="image_url" value="<?php echo htmlspecialchars($image_url); ?>">
                <span class="help-block"><?php echo $image_url_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn" value="Add Product">
            </div>
            <p class="text-center"><a href="view_products.php">View All Products</a> | <a href="../public/index.php">Back to Home</a></p>
        </form>
    </div>
</body>
</html>
<?php if($_SERVER["REQUEST_METHOD"] != "POST" || !empty($name_err) || !empty($price_err) || !empty($sku_err) || !empty($image_url_err) ) { $mysqli->close(); } ?>
