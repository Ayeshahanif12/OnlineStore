<?php
// config.php
// Mail settings for SendGrid
define('MAIL_HOST', 'smtp.sendgrid.net');
define('MAIL_USERNAME', 'apikey');                // Always 'apikey'
define('MAIL_PASSWORD', getenv('SENDGRID_API_KEY'));
define('MAIL_FROM', 'ayeshahanif.0317@gmail.com');   // Apna email (domain ka)
define('MAIL_FROM_NAME', 'Clothing Store');
?>