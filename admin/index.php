<?php
/**
 * Admin Dashboard - Admin Panel
 * Uses OOP Database class for all operations
 */
require_once __DIR__ . '/../app/auth/admin_auth.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/config/security.php';
require_once __DIR__ . '/../app/classes/Database.php';
require_once __DIR__ . '/../app/core/database.php';
require_once __DIR__ . '/../app/core/router.php';

setSecurityHeaders();
requireAdminLogin();

try {
    $stats = [
        'movies' => Database::count('movies'),
        'news' => Database::count('news'),
        'venues' => Database::count('venues'),
        'bookings' => Database::count('bookings'),
        'users' => Database::count('users'),
        'revenue' => Database::sum('bookings', 'total_price')
    ];
    
    $recent_bookings = Database::query("
        SELECT b.*, m.title as movie_title, v.name as venue_name 
        FROM bookings b 
        LEFT JOIN movies m ON b.movie_id = m.id 
        LEFT JOIN venues v ON b.venue_id = v.id 
        ORDER BY b.created_at DESC 
        LIMIT 5
    ");
} catch (Exception $e) {
    $stats = ['movies' => 0, 'news' => 0, 'venues' => 0, 'bookings' => 0, 'users' => 0, 'revenue' => 0];
    $recent_bookings = [];
    $error = "Unable to load dashboard data.";
}

$admin = getAdminInfo();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CinemaBook Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include __DIR__ . '/partials/header.php'; ?>

    <div class="flex">
        <?php $currentPage = 'dashboard'; include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="flex-1 p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Dashboard Overview</h1>
                <p class="text-gray-600">Welcome to the CinemaBook admin panel</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                    <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow rounded-lg p-5">
                    <div class="flex items-center">
                        <i class="fas fa-film text-purple-600 text-2xl"></i>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500">Total Movies</p>
                            <p class="text-lg font-medium text-gray-900"><?= $stats['movies'] ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg p-5">
                    <div class="flex items-center">
                        <i class="fas fa-ticket-alt text-green-600 text-2xl"></i>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500">Total Bookings</p>
                            <p class="text-lg font-medium text-gray-900"><?= $stats['bookings'] ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg p-5">
                    <div class="flex items-center">
                        <i class="fas fa-dollar-sign text-yellow-600 text-2xl"></i>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                            <p class="text-lg font-medium text-gray-900">$<?= number_format($stats['revenue'], 2) ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg p-5">
                    <div class="flex items-center">
                        <i class="fas fa-newspaper text-blue-600 text-2xl"></i>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500">News Articles</p>
                            <p class="text-lg font-medium text-gray-900"><?= $stats['news'] ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg p-5">
                    <div class="flex items-center">
                        <i class="fas fa-building text-indigo-600 text-2xl"></i>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500">Venues</p>
                            <p class="text-lg font-medium text-gray-900"><?= $stats['venues'] ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg p-5">
                    <div class="flex items-center">
                        <i class="fas fa-users text-pink-600 text-2xl"></i>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500">Users</p>
                            <p class="text-lg font-medium text-gray-900"><?= $stats['users'] ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Bookings</h3>
                    <?php if (empty($recent_bookings)): ?>
                        <p class="text-gray-500">No recent bookings found.</p>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Movie</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Venue</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seats</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($recent_bookings as $booking): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($booking['movie_title'] ?? 'Deleted') ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?= htmlspecialchars($booking['venue_name'] ?? 'Deleted') ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?= date('M j, Y', strtotime($booking['show_date'])) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?= $booking['seats_count'] ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                $<?= number_format($booking['total_price'], 2) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
