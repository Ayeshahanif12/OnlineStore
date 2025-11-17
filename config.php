<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Database Connection ---
$conn = mysqli_connect('localhost', 'root', '', 'onlinestore');
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// --- Base URL Configuration ---
define('BASE_URL', 'http://localhost/OnlineStore');

// --- PHPMailer Autoloader ---
require_once __DIR__ . '/vendor/autoload.php';

// --- Load Environment Variables for Mailer ---
include_once __DIR__ . '/myaccount/configs.php';

?>