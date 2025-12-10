<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Flag to indicate pages are loaded via index.php (header/footer handled here)
define('LOADED_VIA_INDEX', true);

require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/core/router.php';
require_once __DIR__ . '/../../app/core/database.php';
require_once __DIR__ . '/../../app/auth/user_auth.php';
require_once __DIR__ . '/../../app/classes/autoload.php';

$PAGES_DIR    = __DIR__ . '/pages';
$PARTIALS_DIR = __DIR__ . '/partials';

$reqPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);

$route = trim($reqPath, '/');

if (strpos($route, 'public/frontend/') === 0) {
    $route = substr($route, strlen('public/frontend/'));
} elseif (strpos($reqPath, $scriptPath) === 0 && $scriptPath !== '/' && $scriptPath !== '.') {
    $route = trim(substr($reqPath, strlen($scriptPath)), '/');
}

if (empty($route) || $route === 'index.php') {
    $route = '';
}

$routes = [
  ''          => 'home.php',
  'home'      => 'home.php',
  'movies'    => 'movies.php',
  'news'      => 'news.php',
  'venues'    => 'venues.php',
  'contact'   => 'contact.php',
  'login'     => 'login.php',
  'logout'    => 'logout.php',
  'profile'   => 'profile.php',
  'booking'   => 'booking.php',
  'checkout'  => 'checkout.php',
  'seat-selection' => 'seat-selection.php',
];

$file = $routes[$route] ?? '404.php';
$full = $PAGES_DIR . '/' . $file;

require $PARTIALS_DIR . '/header.php';
if (is_file($full)) {
  require $full;
} else {
  http_response_code(404);
  echo "<div style='padding:2rem;'>Page not found</div>";
}
require $PARTIALS_DIR . '/footer.php';
