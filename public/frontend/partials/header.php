<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../app/auth/user_auth.php';

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$scriptDir = dirname($scriptName);

$currentPathLower = strtolower($currentPath);
if (preg_match('#^/(cinema|Cinema)/public/frontend#i', $currentPath)) {
  $currentPath = preg_replace('#^/([^/]+)/public/frontend#i', '', $currentPath) ?: '/';
} elseif (preg_match('#^/public/frontend#i', $currentPath)) {
  $currentPath = preg_replace('#^/public/frontend#i', '', $currentPath) ?: '/';
} elseif ($scriptDir !== '/' && $scriptDir !== '.' && strpos($currentPath, $scriptDir) === 0) {
  $currentPath = substr($currentPath, strlen($scriptDir)) ?: '/';
}

$isInPages = strpos($currentPath, '/pages/') !== false || strpos($_SERVER['REQUEST_URI'], '/pages/') !== false;
$basePath = $isInPages ? '../' : '';

$navItems = [
  [ 'label' => 'Home',   'url' => $basePath . 'index.php' ],
  [ 'label' => 'Movies', 'url' => $basePath . 'pages/movies.php' ],
  [ 'label' => 'News',   'url' => $basePath . 'pages/news.php' ],
  [ 'label' => 'Venues', 'url' => $basePath . 'pages/venues.php' ],
  [ 'label' => 'Contact','url' => $basePath . 'pages/contact.php' ],
];

if (isUserLoggedIn()) {
  $navItems[] = [ 'label' => 'Profile','url' => $basePath . 'pages/profile.php' ];
}

function isActive($itemUrl, $currentPath) {
  if ($itemUrl === '/') return $currentPath === '/';
  return strpos($currentPath, $itemUrl) === 0;
}

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CinemaBook</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <meta name="robots" content="noindex, nofollow">
  <meta http-equiv="X-Content-Type-Options" content="nosniff">
  <meta http-equiv="X-Frame-Options" content="DENY">
  <meta http-equiv="X-XSS-Protection" content="1; mode=block">
</head>

<body class="bg-white text-gray-700">

<header class="border-b border-purple-200 px-6 py-3">
  <div class="max-w-7xl mx-auto flex items-center justify-between">
    <a href="<?= $basePath ?>index.php" class="text-purple-700 font-bold text-xl flex items-center space-x-2">
      <i class="fas fa-film"></i>
      <span>CinemaBook</span>
    </a>

    <nav class="flex space-x-6 text-sm">
      <?php foreach ($navItems as $item): $active = isActive($item['url'], $currentPath); ?>
        <a href="<?= $item['url'] ?>"
           class="<?= $active ? 'text-purple-700 font-semibold' : 'text-gray-700 hover:text-purple-700' ?>">
          <?= htmlspecialchars($item['label']) ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="flex items-center space-x-4">
      <a href="#" class="text-gray-600 hover:text-purple-700" aria-label="Search">
        <i class="fas fa-search"></i>
      </a>
      <?php if (isUserLoggedIn() && $user): ?>
        <div class="flex items-center space-x-3">
          <span class="text-sm text-gray-600">Welcome, <?= htmlspecialchars($user['name']) ?></span>
          <a href="<?= $basePath ?>pages/logout.php" class="text-gray-700 hover:text-purple-700">
            <i class="fas fa-sign-out-alt mr-1"></i>Logout
          </a>
        </div>
      <?php else: ?>
        <a href="<?= $basePath ?>pages/login.php" class="text-gray-700 hover:text-purple-700">Login</a>
        <a href="<?= $basePath ?>pages/register.php" class="bg-purple-700 text-white px-4 py-1 rounded-md hover:bg-purple-800">Sign Up</a>
      <?php endif; ?>
    </div>
  </div>
</header>
