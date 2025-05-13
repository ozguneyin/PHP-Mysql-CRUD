<?php
require_once '../includes/config.php';

// Attempt select query execution
$sql = "SELECT id, username, email, full_name, created_at FROM users ORDER BY created_at DESC";
$users = [];
if ($result = $mysqli->query($sql)) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $result->free();
    } else {
        $no_users_message = "<p class='no-data'><em>No users found.</em></p>";
    }
} else {
    echo "ERROR: Could not able to execute $sql. " . $mysqli->error;
}

// Close connection
// $mysqli->close(); // Connection might be needed for other operations on the same page or included files
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Users</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; color: #333; }
        .container { width: 80%; margin: auto; background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .actions a { margin-right: 10px; text-decoration: none; padding: 5px 10px; border-radius: 3px; }
        .actions .edit { background-color: #ffc107; color: black; }
        .actions .delete { background-color: #dc3545; color: white; }
        .actions .edit:hover { background-color: #e0a800; }
        .actions .delete:hover { background-color: #c82333; }
        .add-user-link { display: inline-block; margin-bottom: 20px; padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 3px; }
        .add-user-link:hover { background-color: #218838; }
        .no-data { text-align: center; color: #777; font-style: italic; }
        .nav-links { text-align: center; margin-bottom: 20px;}
        .nav-links a { margin: 0 10px; text-decoration: none; color: #007bff; font-weight: bold;}
        .nav-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Management</h2>
        <div class="nav-links">
            <a href="../public/index.php">Home</a>
            <a href="../products/view_products.php">View Products</a>
        </div>
        <a href="create_user.php" class="add-user-link">Add New User</a>
        <?php if (!empty($users)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Full Name</th>
                        <th>Registered At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user["id"]); ?></td>
                        <td><?php echo htmlspecialchars($user["username"]); ?></td>
                        <td><?php echo htmlspecialchars($user["email"]); ?></td>
                        <td><?php echo htmlspecialchars($user["full_name"]); ?></td>
                        <td><?php echo htmlspecialchars($user["created_at"]); ?></td>
                        <td class="actions">
                            <a href="update_user.php?id=<?php echo $user["id"]; ?>" class="edit">Edit</a>
                            <a href="delete_user.php?id=<?php echo $user["id"]; ?>" class="delete" onclick="return confirm(\'Are you sure you want to delete this user? This action cannot be undone.\');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <?php echo $no_users_message ?? 
            "<p class=\"no-data\"><em>No users found. Click \'Add New User\' to create one.</em></p>"; ?>
        <?php endif; ?>
    </div>
</body>
</html>
<?php $mysqli->close(); ?>
