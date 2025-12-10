<?php
/**
 * Autoloader for classes
 * Automatically loads classes when needed
 */
spl_autoload_register(function ($className) {
    $classFile = __DIR__ . '/' . $className . '.php';
    if (file_exists($classFile)) {
        require_once $classFile;
    }
});

// Manually require base classes
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/ImageUpload.php';
require_once __DIR__ . '/Movie.php';
require_once __DIR__ . '/Venue.php';
require_once __DIR__ . '/Screen.php';
require_once __DIR__ . '/News.php';
require_once __DIR__ . '/Booking.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/ContactMessage.php';

