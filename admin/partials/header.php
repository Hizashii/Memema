<?php
/**
 * Admin Header Partial
 */
require_once __DIR__ . '/../../app/core/router.php';
$adminInfo = getAdminInfo();
?>
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
                <span class="text-gray-700">Welcome, <?= htmlspecialchars($adminInfo['username']) ?></span>
                <a href="<?= route('admin.logout') ?>" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm">
                    <i class="fas fa-sign-out-alt mr-1"></i>Logout
                </a>
            </div>
        </div>
    </div>
</nav>
