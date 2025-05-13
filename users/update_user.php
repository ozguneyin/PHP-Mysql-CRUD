<?php
require_once '../includes/config.php';

$username = $email = $full_name = "";
$username_err = $password_err = $email_err = $full_name_err = "";
$user_id = null;

// Check if ID is set and is a valid integer
if (isset($_GET["id"]) && !empty(trim($_GET["id"])) && filter_var(trim($_GET["id"]), FILTER_VALIDATE_INT)) {
    $user_id = trim($_GET["id"]);

    // Prepare a select statement to fetch user data
    $sql = "SELECT id, username, email, full_name FROM users WHERE id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $param_id);
        $param_id = $user_id;

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $username = $row["username"];
                $email = $row["email"];
                $full_name = $row["full_name"];
            } else {
                // URL doesn't contain valid id. Redirect to error page or user list
                echo "User not found.";
                // header("location: error.php");
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
    $user_id = trim($_POST["id"]);
    // Process update after form submission

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", trim($_POST["username"]))) {
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else {
        // Check if username is taken by another user
        $sql = "SELECT id FROM users WHERE username = ? AND id != ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("si", $param_username, $param_id);
            $param_username = trim($_POST["username"]);
            $param_id = $user_id;
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $username_err = "This username is already taken by another user.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    } else {
        // Check if email is taken by another user
        $sql = "SELECT id FROM users WHERE email = ? AND id != ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("si", $param_email, $param_id);
            $param_email = trim($_POST["email"]);
            $param_id = $user_id;
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $email_err = "This email is already registered by another user.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }

    // Validate full name (optional)
    $full_name = sanitize_input($_POST["full_name"]);
    $new_password = trim($_POST["password"]);

    // Validate new password if provided
    if (!empty($new_password)) {
        if (strlen($new_password) < 6) {
            $password_err = "Password must have at least 6 characters.";
        }
    }

    // Check input errors before updating in database
    if (empty($username_err) && empty($email_err) && empty($password_err)) {
        if (!empty($new_password)) {
            $sql = "UPDATE users SET username = ?, email = ?, full_name = ?, password = ? WHERE id = ?";
        } else {
            $sql = "UPDATE users SET username = ?, email = ?, full_name = ? WHERE id = ?";
        }

        if ($stmt = $mysqli->prepare($sql)) {
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt->bind_param("ssssi", $username, $email, $full_name, $hashed_password, $user_id);
            } else {
                $stmt->bind_param("sssi", $username, $email, $full_name, $user_id);
            }

            if ($stmt->execute()) {
                // Records updated successfully. Redirect to user list page
                echo "<p>User updated successfully! <a href=\"view_users.php\">View Users</a></p>";
                // header("location: view_users.php");
                // exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
    // $mysqli->close(); // Keep connection open if form is re-displayed with errors

} else {
    if (empty(trim($_GET["id"])) && $_SERVER["REQUEST_METHOD"] != "POST") {
         // URL doesn't contain id parameter or it is not a POST request without ID (which is an error state for update)
        echo "Error: No user ID specified for update.";
        // header("location: error.php");
        exit();
    }
   
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update User</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; color: #333; }
        .wrapper { width: 360px; padding: 20px; margin: auto; background-color: #fff; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="password"], input[type="email"] { width: calc(100% - 22px); padding: 10px; border: 1px solid #ddd; border-radius: 3px; }
        .help-block { color: red; font-size: 0.9em; }
        .btn { padding: 10px 15px; background-color: #ffc107; color: black; border: none; border-radius: 3px; cursor: pointer; font-size: 1em; }
        .btn:hover { background-color: #e0a800; }
        .text-center { text-align: center; margin-top: 15px; }
        .note { font-size: 0.9em; color: #555; margin-bottom: 10px;}
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Update User</h2>
        <p>Please edit the input values and submit to update the user record.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="id" value="<?php echo $user_id; ?>"/>
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label>Full Name (Optional)</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>">
                <span class="help-block"><?php echo $full_name_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>New Password</label>
                <input type="password" name="password">
                <span class="help-block"><?php echo $password_err; ?></span>
                <p class="note">Leave blank if you don't want to change the password.</p>
            </div>
            <div class="form-group">
                <input type="submit" class="btn" value="Update User">
            </div>
            <p class="text-center"><a href="view_users.php">Back to Users List</a></p>
        </form>
    </div>
</body>
</html>
<?php if($_SERVER["REQUEST_METHOD"] != "POST" || !empty($username_err) || !empty($email_err) || !empty($password_err)) { $mysqli->close(); } ?>
