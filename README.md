# PHP CRUD Application with User and Product Management

This project is a simple PHP and MySQL based CRUD (Create, Read, Update, Delete) application that allows managing users and products.

## Features

- **User Management:**
    - Create new users (Sign Up)
    - View a list of existing users
    - Update user information (username, email, full name, password)
    - Delete users
    - Password hashing for security
- **Product Management:**
    - Add new products (name, description, price, SKU, image URL)
    - View a list of existing products
    - Update product information
    - Delete products
- Basic input validation and sanitization.

## Project Structure

```
php_crud_app/
├── includes/
│   └── config.php         # Database connection and common functions
├── users/
│   ├── create_user.php    # Handles user creation (Sign Up)
│   ├── view_users.php     # Displays list of users
│   ├── update_user.php    # Handles user updates
│   └── delete_user.php    # Handles user deletion
├── products/
│   ├── create_product.php # Handles product creation
│   ├── view_products.php  # Displays list of products
│   ├── update_product.php # Handles product updates
│   └── delete_product.php # Handles product deletion
└── public/
    └── index.php          # Main landing page

database_schema.sql        # SQL script to create database tables
README.md                  # This file
```

## Prerequisites

- A web server with PHP support (e.g., Apache, Nginx)
- MySQL database server
- PHP (version 7.4 or higher recommended, tested with PHP 8.1)
- PHP extensions: `mysqli`, `mbstring`, `xml`, `zip` (usually enabled by default or easily installable)

## Setup Instructions

1.  **Database Setup:**
    *   Ensure your MySQL server is running.
    *   Create a new database. You can name it `php_crud_app_db` or choose your own name.
    *   Import the `database_schema.sql` file into your newly created database. This will create the `users` and `products` tables.
        ```bash
        mysql -u your_mysql_username -p your_database_name < database_schema.sql
        ```
    *   Alternatively, you can use a MySQL client like phpMyAdmin to create the database and import the schema.

2.  **Configure Application:**
    *   Open the `php_crud_app/includes/config.php` file.
    *   Update the database connection details:
        ```php
        define("DB_SERVER", "localhost"); // Or your MySQL server address
        define("DB_USERNAME", "your_mysql_username");
        define("DB_PASSWORD", "your_mysql_password");
        define("DB_NAME", "php_crud_app_db"); // Or the name you chose in step 1
        ```

3.  **Deploy Application:**
    *   Copy the entire `php_crud_app` directory to your web server's document root (e.g., `/var/www/html/` for Apache on Linux, or `htdocs/` for XAMPP).
    *   Ensure your web server has the necessary permissions to read and execute files in this directory.

4.  **Access Application:**
    *   Open your web browser and navigate to the application's public directory. For example, if you placed the `php_crud_app` folder directly in your web root, the URL would be:
        `http://localhost/php_crud_app/public/index.php`

## Usage

-   The main page (`index.php`) provides links to "Manage Users" and "Manage Products".
-   **User Management:**
    -   Click "Add New User" to register a new user.
    -   The user list displays existing users with options to "Edit" or "Delete" them.
-   **Product Management:**
    -   Click "Add New Product" to add a product to the catalog.
    -   The product list displays existing products with options to "Edit" or "Delete" them.

## Important Notes on Testing

Due to persistent connectivity issues with the temporary web exposure environment during development, comprehensive end-to-end testing of the web UI could not be fully completed by the AI agent. 

**It is highly recommended that you perform thorough testing in your own local or staging environment to ensure all functionalities work as expected.**

The core backend logic for CRUD operations, database connection, input sanitization, and password hashing has been implemented. The database schema is provided in `database_schema.sql`.

## Troubleshooting

-   **Connection Errors:** Double-check your database credentials and server address in `config.php`. Ensure your MySQL server is running and accessible.
-   **Permissions:** If you encounter file access errors, ensure your web server user (e.g., `www-data` for Apache) has the necessary read/write permissions for the project directory, especially if file uploads were to be implemented (not part of this basic version).
-   **PHP Errors:** Check your web server's PHP error logs for more detailed information if pages are not loading correctly.


