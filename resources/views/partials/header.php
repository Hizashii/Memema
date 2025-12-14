<?php
if (!defined('CINEMA_APP')) {
    define('CINEMA_APP', true);
}

require_once __DIR__ . '/../../../app/config/security.php';

if (!function_exists('url')) {
    require_once __DIR__ . '/../../../app/Core/helpers.php';
}

if (!function_exists('getImagePath')) {
    require_once __DIR__ . '/../../../app/Core/helpers.php';
}

setSecurityHeaders();

$authService = new AuthService();
$isLoggedIn = $authService->isUserLoggedIn();
$user = $isLoggedIn ? $authService->getCurrentUser() : null;

$basePath = getBasePath();
$navItems = [
  ['label' => 'Home', 'url' => url('/')],
  ['label' => 'Movies', 'url' => url('/movies')],
  ['label' => 'News', 'url' => url('/news')],
  ['label' => 'Venues', 'url' => url('/venues')],
  ['label' => 'Contact', 'url' => url('/contact')],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CinemaBook</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎬</text></svg>">
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
    <a href="<?= url('/') ?>" class="text-purple-700 font-bold text-xl flex items-center space-x-2">
      <i class="fas fa-film"></i>
      <span>CinemaBook</span>
    </a>

    <nav class="flex space-x-6 text-sm">
      <?php foreach ($navItems as $item): ?>
        <a href="<?= $item['url'] ?>" class="text-gray-700 hover:text-purple-700">
          <?= View::e($item['label']) ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="flex items-center space-x-4">
      <?php if ($isLoggedIn && $user): ?>
        <div class="flex items-center space-x-3">
          <a href="<?= url('/profile') ?>" class="text-sm text-gray-600 hover:text-purple-700"><?= View::e($user['name']) ?></a>
          <a href="<?= url('/logout') ?>" class="text-gray-700 hover:text-purple-700">
            <i class="fas fa-sign-out-alt mr-1"></i>Logout
          </a>
        </div>
      <?php else: ?>
        <a href="<?= url('/login') ?>" class="text-gray-700 hover:text-purple-700">Login</a>
        <a href="<?= url('/register') ?>" class="bg-purple-700 text-white px-4 py-1 rounded-md hover:bg-purple-800">Sign Up</a>
      <?php endif; ?>
    </div>
  </div>
</header>
