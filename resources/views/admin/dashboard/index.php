<?php
require_once __DIR__ . '/../_helpers.php';
View::partial('admin_header');
?>
<div class="flex">
<?php
View::partial('admin_sidebar', ['currentPage' => 'dashboard']);
?>
<div class="flex-1 p-8">

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Total Movies</p>
                <p class="text-3xl font-bold text-purple-700"><?= $stats['movies'] ?? 0 ?></p>
            </div>
            <div class="bg-purple-100 rounded-full p-4">
                <i class="fas fa-film text-purple-700 text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Total News</p>
                <p class="text-3xl font-bold text-purple-700"><?= $stats['news'] ?? 0 ?></p>
            </div>
            <div class="bg-purple-100 rounded-full p-4">
                <i class="fas fa-newspaper text-purple-700 text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Total Venues</p>
                <p class="text-3xl font-bold text-purple-700"><?= $stats['venues'] ?? 0 ?></p>
            </div>
            <div class="bg-purple-100 rounded-full p-4">
                <i class="fas fa-building text-purple-700 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-2xl font-bold mb-4">Welcome to Admin Dashboard</h2>
    <p class="text-gray-600">Manage your cinema operations from here.</p>
</div>
</div>
</div>
<?php
View::partial('admin_footer');
?>

