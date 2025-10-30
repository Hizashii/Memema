<?php
require_once __DIR__ . '/../app/auth/admin_auth.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/core/database.php';

requireAdminLogin();

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action === 'create') {
            $full_name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            
            if (empty($full_name) || empty($email)) {
                throw new Exception('Please fill in all required fields.');
            }
            
            // Check if email already exists
            $existing = executePreparedQuery("SELECT id FROM users WHERE email = ?", [$email], 's');
            if (!empty($existing)) {
                throw new Exception('Email already exists.');
            }
            
            executePreparedQuery(
                "INSERT INTO users (full_name, email, phone, created_at) VALUES (?, ?, ?, NOW())",
                [$full_name, $email, $phone],
                'sss'
            );
            
            $message = 'User created successfully!';
            
        } elseif ($action === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            $full_name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            
            if ($id <= 0 || empty($full_name) || empty($email)) {
                throw new Exception('Please fill in all required fields.');
            }
            
            // Check if email already exists (excluding current user)
            $existing = executePreparedQuery("SELECT id FROM users WHERE email = ? AND id != ?", [$email, $id], 'si');
            if (!empty($existing)) {
                throw new Exception('Email already exists.');
            }
            
            executePreparedQuery(
                "UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?",
                [$full_name, $email, $phone, $id],
                'sssi'
            );
            
            $message = 'User updated successfully!';
            
        } elseif ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            
            if ($id <= 0) {
                throw new Exception('Invalid user ID.');
            }
            
            executePreparedQuery("DELETE FROM users WHERE id = ?", [$id], 'i');
            
            $message = 'User deleted successfully!';
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get all users with their booking counts
try {
    $users = executeQuery("
        SELECT u.*, 
               COUNT(b.id) as booking_count,
               COALESCE(SUM(b.total_price), 0) as total_spent
        FROM users u 
        LEFT JOIN bookings b ON u.id = b.user_id 
        GROUP BY u.id 
        ORDER BY u.created_at DESC
    ");
} catch (Exception $e) {
    $users = [];
    $error = "Unable to load users.";
}

$admin = getAdminInfo();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - CinemaBook Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                    <a href="/Cinema/admin/logout.php" 
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
                    <a href="/Cinema/admin/" 
                       class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        Dashboard
                    </a>
                    <a href="/Cinema/admin/movies.php" 
                       class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-film mr-3"></i>
                        Movies
                    </a>
                    <a href="/Cinema/admin/news.php" 
                       class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-newspaper mr-3"></i>
                        News
                    </a>
                    <a href="/Cinema/admin/venues.php" 
                       class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-building mr-3"></i>
                        Venues
                    </a>
                    <a href="/Cinema/admin/bookings.php" 
                       class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-ticket-alt mr-3"></i>
                        Bookings
                    </a>
                    <a href="/Cinema/admin/users.php" 
                       class="flex items-center px-4 py-2 text-sm font-medium text-white bg-purple-700 rounded-md">
                        <i class="fas fa-users mr-3"></i>
                        Users
                    </a>
                    <a href="/Cinema/public/frontend/" 
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
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Users Management</h1>
                        <p class="text-gray-600">Manage user accounts and profiles</p>
                    </div>
                    <button onclick="openModal('create')" 
                            class="bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded-md">
                        <i class="fas fa-plus mr-2"></i>
                        Add User
                    </button>
                </div>
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

            <!-- Users Table -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bookings</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #<?= $user['id'] ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($user['full_name']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= htmlspecialchars($user['email']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= htmlspecialchars($user['phone'] ?: 'N/A') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <?= $user['booking_count'] ?> booking<?= $user['booking_count'] != 1 ? 's' : '' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    $<?= number_format($user['total_spent'], 2) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('M j, Y', strtotime($user['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="openModal('update', <?= htmlspecialchars(json_encode($user)) ?>)" 
                                            class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteUser(<?= $user['id'] ?>)" 
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

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <form method="POST" id="userForm">
                    <input type="hidden" name="action" id="formAction">
                    <input type="hidden" name="id" id="userId">
                    
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Add User</h3>
                    </div>
                    
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                            <input type="text" id="full_name" name="full_name" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                            <input type="email" id="email" name="email" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                            <input type="tel" id="phone" name="phone"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()" 
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-purple-700 text-white rounded-md hover:bg-purple-800">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="deleteId">
    </form>

    <script>
        function openModal(action, user = null) {
            const modal = document.getElementById('modal');
            const form = document.getElementById('userForm');
            const formAction = document.getElementById('formAction');
            const userId = document.getElementById('userId');
            const modalTitle = document.getElementById('modalTitle');
            
            formAction.value = action;
            
            if (action === 'create') {
                modalTitle.textContent = 'Add User';
                form.reset();
                userId.value = '';
            } else if (action === 'update' && user) {
                modalTitle.textContent = 'Edit User';
                userId.value = user.id;
                document.getElementById('full_name').value = user.full_name;
                document.getElementById('email').value = user.email;
                document.getElementById('phone').value = user.phone || '';
            }
            
            modal.classList.remove('hidden');
        }
        
        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
        }
        
        function deleteUser(id) {
            if (confirm('Are you sure you want to delete this user? This will also delete all their bookings.')) {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
        
        // Close modal on outside click
        document.getElementById('modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>
