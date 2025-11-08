<?php
require_once __DIR__ . '/../app/auth/admin_auth.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/core/database.php';
require_once __DIR__ . '/../app/core/router.php';

requireAdminLogin();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            
            if ($id <= 0) {
                throw new Exception('Invalid booking ID.');
            }
            
            executePreparedQuery("DELETE FROM bookings WHERE id = ?", [$id], 'i');
            
            $message = 'Booking deleted successfully!';
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$filter_movie = $_GET['movie'] ?? '';
$filter_venue = $_GET['venue'] ?? '';
$filter_date = $_GET['date'] ?? '';

$where_conditions = [];
$params = [];
$types = '';

if (!empty($filter_movie)) {
    $where_conditions[] = "m.title LIKE ?";
    $params[] = "%$filter_movie%";
    $types .= 's';
}

if (!empty($filter_venue)) {
    $where_conditions[] = "v.name LIKE ?";
    $params[] = "%$filter_venue%";
    $types .= 's';
}

if (!empty($filter_date)) {
    $where_conditions[] = "b.show_date = ?";
    $params[] = $filter_date;
    $types .= 's';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

try {
    $bookings = executePreparedQuery("
        SELECT b.*, m.title as movie_title, v.name as venue_name, s.screen_name,
               u.full_name as user_name, u.email as user_email
        FROM bookings b 
        JOIN movies m ON b.movie_id = m.id 
        JOIN venues v ON b.venue_id = v.id 
        JOIN screens s ON b.screen_id = s.id
        JOIN users u ON b.user_id = u.id
        $where_clause
        ORDER BY b.created_at DESC
    ", $params, $types);
    
    $stats = [
        'total_bookings' => executeQuery("SELECT COUNT(*) as count FROM bookings")[0]['count'],
        'total_revenue' => executeQuery("SELECT SUM(total_price) as total FROM bookings")[0]['total'] ?? 0,
        'today_bookings' => executeQuery("SELECT COUNT(*) as count FROM bookings WHERE DATE(created_at) = CURDATE()")[0]['count'],
        'today_revenue' => executeQuery("SELECT SUM(total_price) as total FROM bookings WHERE DATE(created_at) = CURDATE()")[0]['total'] ?? 0
    ];
    
    $recent_bookings = executeQuery("
        SELECT DATE(created_at) as date, COUNT(*) as count, SUM(total_price) as revenue
        FROM bookings 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date ASC
    ");
    
} catch (Exception $e) {
    $bookings = [];
    $stats = ['total_bookings' => 0, 'total_revenue' => 0, 'today_bookings' => 0, 'today_revenue' => 0];
    $recent_bookings = [];
    $error = "Unable to load bookings data.";
}

$admin = getAdminInfo();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings Management - CinemaBook Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
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
                    <span class="text-gray-700">Welcome, <?= htmlspecialchars($admin['username']) ?></span>
                    <a href="<?= route('admin.logout') ?>" 
                       class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm">
                        <i class="fas fa-sign-out-alt mr-1"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-sm min-h-screen">
            <div class="p-4">
                <nav class="space-y-2">
                    <a href="<?= route('admin.dashboard') ?>" 
                       class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        Dashboard
                    </a>
                    <a href="<?= route('admin.movies') ?>" 
                       class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-film mr-3"></i>
                        Movies
                    </a>
                    <a href="<?= route('admin.news') ?>" 
                       class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-newspaper mr-3"></i>
                        News
                    </a>
                    <a href="<?= route('admin.venues') ?>" 
                       class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-building mr-3"></i>
                        Venues
                    </a>
                    <a href="<?= route('admin.bookings') ?>" 
                       class="flex items-center px-4 py-2 text-sm font-medium text-white bg-purple-700 rounded-md">
                        <i class="fas fa-ticket-alt mr-3"></i>
                        Bookings
                    </a>
                    <a href="<?= route('admin.users') ?>" 
                       class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-users mr-3"></i>
                        Users
                    </a>
                    <a href="<?= route('public.home') ?>" 
                       class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-external-link-alt mr-3"></i>
                        View Website
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Bookings Management</h1>
                <p class="text-gray-600">View and manage all bookings</p>
            </div>

            <!-- Messages -->
            <?php if ($message): ?>
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-ticket-alt text-purple-600 text-2xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Bookings</dt>
                                    <dd class="text-lg font-medium text-gray-900"><?= $stats['total_bookings'] ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-dollar-sign text-green-600 text-2xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                                    <dd class="text-lg font-medium text-gray-900">$<?= number_format($stats['total_revenue'], 2) ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-calendar-day text-blue-600 text-2xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Today's Bookings</dt>
                                    <dd class="text-lg font-medium text-gray-900"><?= $stats['today_bookings'] ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-chart-line text-yellow-600 text-2xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Today's Revenue</dt>
                                    <dd class="text-lg font-medium text-gray-900">$<?= number_format($stats['today_revenue'], 2) ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white shadow rounded-lg p-6 mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Filters</h3>
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="movie" class="block text-sm font-medium text-gray-700">Movie</label>
                        <input type="text" id="movie" name="movie" value="<?= htmlspecialchars($filter_movie) ?>"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div>
                        <label for="venue" class="block text-sm font-medium text-gray-700">Venue</label>
                        <input type="text" id="venue" name="venue" value="<?= htmlspecialchars($filter_venue) ?>"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                        <input type="date" id="date" name="date" value="<?= htmlspecialchars($filter_date) ?>"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" 
                                class="w-full bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded-md">
                            <i class="fas fa-search mr-2"></i>
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Bookings Table -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">All Bookings</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Movie</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Venue</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Show Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seats</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #<?= $booking['id'] ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($booking['user_name']) ?></div>
                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($booking['user_email']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($booking['movie_title']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= htmlspecialchars($booking['venue_name']) ?><br>
                                        <span class="text-xs text-gray-400"><?= htmlspecialchars($booking['screen_name']) ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= date('M j, Y', strtotime($booking['show_date'])) ?><br>
                                        <span class="text-xs"><?= date('g:i A', strtotime($booking['show_time'])) ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= $booking['seats_count'] ?> seat<?= $booking['seats_count'] > 1 ? 's' : '' ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        $<?= number_format($booking['total_price'], 2) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="deleteBooking(<?= $booking['id'] ?>)" 
                                                class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="deleteId">
    </form>

    <script>
        function deleteBooking(id) {
            if (confirm('Are you sure you want to delete this booking?')) {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>
</html>
