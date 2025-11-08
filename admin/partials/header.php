<?php
require_once __DIR__ . '/../../app/core/router.php';
require_once __DIR__ . '/../config/user_auth.php';
$adminInfo = getAdminInfo();
?>

<nav class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="<?= route('admin.dashboard') ?>" class="text-xl font-bold text-purple-700">
                    <i class="fas fa-film mr-2"></i>Cinema Dashboard
                </a>
            </div>
            
            <div class="flex items-center space-x-4">
                <a href="<?= route('public.home') ?>" class="text-gray-600 hover:text-gray-900" target="_blank">
                    <i class="fas fa-external-link-alt mr-1"></i>View Site
                </a>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600">Welcome, <?= htmlspecialchars($adminInfo['username']) ?></span>
                    <a href="<?= route('admin.logout') ?>" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
