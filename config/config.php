<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'sweetlife_hotel');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site configuration
define('SITE_URL', 'http://localhost');
define('SITE_NAME', 'SweetLife Hotel');

// Paystack configuration
define('PAYSTACK_PUBLIC_KEY', 'pk_test_your_public_key_here');
define('PAYSTACK_SECRET_KEY', 'sk_test_your_secret_key_here');

// Email configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your_email@gmail.com');
define('SMTP_PASSWORD', 'your_app_password');

// Security
define('ENCRYPTION_KEY', 'your_32_character_secret_key_here');

// File upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_PATH', 'uploads/');

// Timezone
date_default_timezone_set('Africa/Lagos');
?>