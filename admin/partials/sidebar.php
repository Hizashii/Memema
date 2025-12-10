<?php
/**
 * Admin Sidebar Partial
 * 
 * Set $currentPage before including to highlight active menu item
 */
$currentPage = $currentPage ?? '';

$menuItems = [
    ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'fa-tachometer-alt', 'route' => 'admin.dashboard'],
    ['id' => 'movies', 'label' => 'Movies', 'icon' => 'fa-film', 'route' => 'admin.movies'],
    ['id' => 'news', 'label' => 'News', 'icon' => 'fa-newspaper', 'route' => 'admin.news'],
    ['id' => 'venues', 'label' => 'Venues', 'icon' => 'fa-building', 'route' => 'admin.venues'],
    ['id' => 'bookings', 'label' => 'Bookings', 'icon' => 'fa-ticket-alt', 'route' => 'admin.bookings'],
    ['id' => 'users', 'label' => 'Users', 'icon' => 'fa-users', 'route' => 'admin.users'],
    ['id' => 'contact', 'label' => 'Messages', 'icon' => 'fa-envelope', 'route' => 'admin.contact'],
];
?>
<div class="w-64 bg-white shadow-sm min-h-screen">
    <div class="p-4">
        <nav class="space-y-2">
            <?php foreach ($menuItems as $item): ?>
                <a href="<?= route($item['route']) ?>" 
                   class="flex items-center px-4 py-2 text-sm font-medium <?= $currentPage === $item['id'] ? 'text-white bg-purple-700' : 'text-gray-700 hover:bg-gray-100' ?> rounded-md">
                    <i class="fas <?= $item['icon'] ?> mr-3"></i>
                    <?= $item['label'] ?>
                </a>
            <?php endforeach; ?>
            <hr class="my-4 border-gray-200">
            <a href="<?= route('public.home') ?>" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">
                <i class="fas fa-external-link-alt mr-3"></i>View Website
            </a>
        </nav>
    </div>
</div>

