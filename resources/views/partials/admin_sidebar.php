<?php
$currentPage = $currentPage ?? '';
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
        
        // Normalize path
        $path = '/' . trim($path, '/');
        if ($path === '/') $path = '';
        
        // Build URL - ALWAYS use index.php?route= format
        $url = $base . '/public/index.php?route=' . urlencode($path);
        if (!empty($queryString)) {
            $url .= '&' . $queryString;
        }
        return $url;
    }
}
?>
<div class="w-64 bg-white shadow-sm min-h-screen">
    <div class="p-4">
        <nav class="space-y-2">
            <a href="<?= adminUrl('/admin') ?>" 
               class="flex items-center px-4 py-2 text-sm font-medium <?= $currentPage === 'dashboard' ? 'text-white bg-purple-700' : 'text-gray-700 hover:bg-gray-100' ?> rounded-md">
                <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
            </a>
            <a href="<?= adminUrl('/admin/movies') ?>" 
               class="flex items-center px-4 py-2 text-sm font-medium <?= $currentPage === 'movies' ? 'text-white bg-purple-700' : 'text-gray-700 hover:bg-gray-100' ?> rounded-md">
                <i class="fas fa-film mr-3"></i>Movies
            </a>
            <a href="<?= adminUrl('/admin/news') ?>" 
               class="flex items-center px-4 py-2 text-sm font-medium <?= $currentPage === 'news' ? 'text-white bg-purple-700' : 'text-gray-700 hover:bg-gray-100' ?> rounded-md">
                <i class="fas fa-newspaper mr-3"></i>News
            </a>
            <a href="<?= adminUrl('/admin/venues') ?>" 
               class="flex items-center px-4 py-2 text-sm font-medium <?= $currentPage === 'venues' ? 'text-white bg-purple-700' : 'text-gray-700 hover:bg-gray-100' ?> rounded-md">
                <i class="fas fa-building mr-3"></i>Venues
            </a>
            <a href="<?= adminUrl('/admin/bookings') ?>" 
               class="flex items-center px-4 py-2 text-sm font-medium <?= $currentPage === 'bookings' ? 'text-white bg-purple-700' : 'text-gray-700 hover:bg-gray-100' ?> rounded-md">
                <i class="fas fa-ticket-alt mr-3"></i>Bookings
            </a>
            <a href="<?= adminUrl('/admin/users') ?>" 
               class="flex items-center px-4 py-2 text-sm font-medium <?= $currentPage === 'users' ? 'text-white bg-purple-700' : 'text-gray-700 hover:bg-gray-100' ?> rounded-md">
                <i class="fas fa-users mr-3"></i>Users
            </a>
            <a href="<?= adminUrl('/admin/messages') ?>" 
               class="flex items-center px-4 py-2 text-sm font-medium <?= $currentPage === 'messages' ? 'text-white bg-purple-700' : 'text-gray-700 hover:bg-gray-100' ?> rounded-md">
                <i class="fas fa-envelope mr-3"></i>Messages
            </a>
            <a href="<?= adminUrl('/admin/shows') ?>" 
               class="flex items-center px-4 py-2 text-sm font-medium <?= $currentPage === 'shows' ? 'text-white bg-purple-700' : 'text-gray-700 hover:bg-gray-100' ?> rounded-md">
                <i class="fas fa-calendar-alt mr-3"></i>Daily Showings
            </a>
            <a href="<?= adminUrl('/admin/settings') ?>" 
               class="flex items-center px-4 py-2 text-sm font-medium <?= $currentPage === 'settings' ? 'text-white bg-purple-700' : 'text-gray-700 hover:bg-gray-100' ?> rounded-md">
                <i class="fas fa-cog mr-3"></i>Company Settings
            </a>
            <a href="<?= adminUrl('/admin/contact-info') ?>" 
               class="flex items-center px-4 py-2 text-sm font-medium <?= $currentPage === 'contact-info' ? 'text-white bg-purple-700' : 'text-gray-700 hover:bg-gray-100' ?> rounded-md">
                <i class="fas fa-address-book mr-3"></i>Contact Info
            </a>
            <hr class="my-4 border-gray-200">
            <a href="<?= adminUrl('/') ?>" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">
                <i class="fas fa-external-link-alt mr-3"></i>View Website
            </a>
        </nav>
    </div>
</div>

