<?php
if (!function_exists('url')) {
    require_once __DIR__ . '/../../../app/Core/helpers.php';
}
$authService = new AuthService();
$adminInfo = [
    'username' => $_SESSION['admin_username'] ?? 'Admin',
    'email' => $_SESSION['admin_email'] ?? ''
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - CinemaBook</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
<nav class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <div class="flex-shrink-0 flex items-center">
                    <i class="fas fa-film text-purple-700 text-2xl mr-3"></i>
                    <span class="text-xl font-bold text-gray-900">CinemaBook Admin</span>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-gray-700">Welcome, <?= View::e($adminInfo['username']) ?></span>
                <?php
                // Simple logout URL
                $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/public/index.php';
                if (preg_match('#^/([^/]+)/public#', $scriptName, $matches)) {
                    $base = '/' . $matches[1];
                } elseif (preg_match('#^/([^/]+)#', $scriptName, $matches)) {
                    $base = '/' . $matches[1];
                } else {
                    $base = '/Cinema';
                }
                $logoutUrl = $base . '/public/index.php?route=/admin/logout';
                ?>
                <a href="<?= $logoutUrl ?>" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm">
                    <i class="fas fa-sign-out-alt mr-1"></i>Logout
                </a>
            </div>
        </div>
    </div>
</nav>

