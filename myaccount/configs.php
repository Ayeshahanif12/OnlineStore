<?php

// --- Environment Variable Loader ---
$envFilePath = __DIR__ . '/.env.txt';

if (file_exists($envFilePath)) {
    $lines = file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Ignore comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parse name=value pairs
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Remove quotes from value
            if ((substr($value, 0, 1) == "'" && substr($value, -1) == "'") || (substr($value, 0, 1) == '"' && substr($value, -1) == '"')) {
                $value = substr($value, 1, -1);
            }
            
            // Define constant if not already defined
            if (!defined($name)) {
                define($name, $value);
            }
        }
    }
}

// --- PHPMailer/SendGrid Configuration ---
define('MAIL_PASSWORD', SENDGRID_API_KEY);
define('MAIL_HOST', 'smtp.sendgrid.net');
define('MAIL_USERNAME', 'apikey'); // This will always be 'apikey' for SendGrid
define('MAIL_FROM', 'ayeshahanif.0317@gmail.com'); // IMPORTANT: Use your verified SendGrid email here
define('MAIL_FROM_NAME', 'Trendy Wear');

?>
