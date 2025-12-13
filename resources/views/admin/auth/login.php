<?php
if (!function_exists('adminUrl')) {
    function adminUrl($path = '/') {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/public/index.php';
        
        if (preg_match('#^/([^/]+)/public#', $scriptName, $matches)) {
            $base = '/' . $matches[1];
        } elseif (preg_match('#^/([^/]+)#', $scriptName, $matches)) {
            $base = '/' . $matches[1];
        } else {
            $requestUri = $_SERVER['REQUEST_URI'] ?? '';
            if (preg_match('#^/([^/]+)/public#', $requestUri, $matches)) {
                $base = '/' . $matches[1];
            } else {
                $base = '/Cinema';
            }
        }
        
        $queryString = '';
        if (strpos($path, '?') !== false) {
            list($path, $queryString) = explode('?', $path, 2);
        }
        $path = '/' . trim($path, '/');
        if ($path === '/') $path = '';
        $url = $base . '/public/index.php?route=' . urlencode($path);
        if (!empty($queryString)) {
            $url .= '&' . $queryString;
        }
        return $url;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - CinemaBook Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-purple-700 rounded-full flex items-center justify-center">
                <i class="fas fa-film text-white text-2xl"></i>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Admin Dashboard
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Sign in to manage CinemaBook
            </p>
        </div>
        
        <form class="mt-8 space-y-6" method="POST" action="<?= adminUrl('/admin/login') ?>">
            <?= Csrf::field() ?>
            <?php if (isset($error)): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?= View::e($error) ?>
                </div>
            <?php endif; ?>
            
            <div class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">
                        Username
                    </label>
                    <div class="mt-1 relative">
                        <input id="username" name="username" type="text" required
                               class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm"
                               placeholder="Enter username">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password
                    </label>
                    <div class="mt-1 relative">
                        <input id="password" name="password" type="password" required
                               class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm"
                               placeholder="Enter password">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-purple-700 hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-sign-in-alt text-purple-500 group-hover:text-purple-400"></i>
                    </span>
                    Sign in
                </button>
            </div>
            
            <div class="text-center">
                <a href="<?= adminUrl('/') ?>" class="text-sm text-purple-600 hover:text-purple-500">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Back to Website
                </a>
            </div>
        </form>
    </div>
</body>
</html>

