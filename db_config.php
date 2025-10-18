<?php
/**
 * Centralized database connection.
 * Include this file in any script that needs database access.
 */

// Database configuration constants
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'onlinestore');

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection and stop on failure
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>