<?php
// This can be a simple landing page for the CRUD application.
// It can provide links to User Management and Product Management sections.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP CRUD Application</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; color: #333; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; }
        .container { background-color: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); text-align: center; }
        h1 { color: #333; margin-bottom: 30px; }
        .nav-links { margin-top: 20px; }
        .nav-links a {
            display: inline-block;
            margin: 10px 15px;
            padding: 12px 25px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.1em;
            transition: background-color 0.3s ease;
        }
        .nav-links a:hover {
            background-color: #0056b3;
        }
        .nav-links a.products {
            background-color: #28a745;
        }
        .nav-links a.products:hover {
            background-color: #218838;
        }
        footer {
            margin-top: 30px;
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to the PHP CRUD Application</h1>
        <p>Manage your users and products efficiently.</p>
        <div class="nav-links">
            <a href="../users/view_users.php">Manage Users</a>
            <a href="../products/view_products.php" class="products">Manage Products</a>
        </div>
    </div>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> PHP CRUD App</p>
    </footer>
</body>
</html>

