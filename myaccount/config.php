<?php

// --- Environment Variable Loader ---

// .env file ka path
$envFilePath = __DIR__ . '/.env.txt';

if (file_exists($envFilePath)) {
    $lines = file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if ((substr($value, 0, 1) == "'" && substr($value, -1) == "'") || (substr($value, 0, 1) == '"' && substr($value, -1) == '"')) {
                $value = substr($value, 1, -1);
            }
            
            if (!defined($name)) {
                define($name, $value);
            }
        }
    }
}

// --- DIAGNOSTIC CHECK ---
// Yeh check karega ke API key load hui hai ya nahi.
if (!defined('SENDGRID_API_KEY') || SENDGRID_API_KEY === '') {
    // Agar key load nahi hui to foran error dikhayein.
    die("FATAL ERROR: SENDGRID_API_KEY not loaded from .env.txt file. Please check the file path and content.");
}

// --- PHPMailer/SendGrid Configuration ---

define('MAIL_PASSWORD', SENDGRID_API_KEY);

// Baaki ki settings
define('MAIL_HOST', 'smtp.sendgrid.net');
define('MAIL_USERNAME', 'apikey'); // Yeh hamesha 'apikey' hi rahega
define('MAIL_FROM', 'ayeshahanif.0317@gmail.com'); // IMPORTANT: Yahan apna SendGrid ka verified email daalein
define('MAIL_FROM_NAME', 'Trendy Wear');

?>

