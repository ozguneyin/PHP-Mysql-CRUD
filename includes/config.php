<?php
// Database configuration
define("DB_SERVER", "localhost"); // MySQL server, usually localhost
define("DB_USERNAME", "hozgune"); // Your MySQL username
define("DB_PASSWORD", "kedili"); // Your MySQL password
define("DB_NAME", "crud"); // The name of your database

// Attempt to connect to MySQL database
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($mysqli === false){
    die("ERROR: Could not connect. " . $mysqli->connect_error);
}

// Optional: Set charset to utf8mb4 for better character support
if (!$mysqli->set_charset("utf8mb4")) {
    // printf("Error loading character set utf8mb4: %s\n", $mysqli->error);
    // For production, you might want to log this error instead of printing it
} else {
    // printf("Current character set: %s\n", $mysqli->character_set_name());
}

// Function to sanitize input data (basic example)
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// You might want to include session_start() here if you plan to use sessions across your application
// session_start();

?>
