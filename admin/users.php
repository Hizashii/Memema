<?php
/**
 * Users Management - Admin Panel
 * Uses OOP User class for all database operations
 */
require_once __DIR__ . '/../app/auth/admin_auth.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/config/security.php';
require_once __DIR__ . '/../app/classes/Database.php';
require_once __DIR__ . '/../app/classes/User.php';
require_once __DIR__ . '/../app/core/database.php';
require_once __DIR__ . '/../app/core/router.php';

setSecurityHeaders();
requireAdminLogin();

$message = $_SESSION['flash_message'] ?? '';
$error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action === 'create') {
            $email = trim($_POST['email'] ?? '');
            
            // Check if email already exists
            if (User::getByEmail($email)) {
                throw new Exception('Email already exists.');
            }
            
            $user = new User([
                'full_name' => trim($_POST['full_name'] ?? ''),
                'email' => $email,
                'phone' => trim($_POST['phone'] ?? ''),
                'password' => 'defaultpass123' // Default password for admin-created users
            ]);
            
            if (empty($user->getFullName()) || empty($user->getEmail())) {
                throw new Exception('Please fill in all required fields.');
            }
            
            $user->create();
            $_SESSION['flash_message'] = 'User created successfully!';
            
        } elseif ($action === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            $email = trim($_POST['email'] ?? '');
            if ($id <= 0) throw new Exception('Invalid user ID.');
            
            // Check if email already exists for another user
            $existingUser = User::getByEmail($email);
            if ($existingUser && $existingUser['id'] != $id) {
                throw new Exception('Email already exists.');
            }
            
            $user = new User([
                'id' => $id,
                'full_name' => trim($_POST['full_name'] ?? ''),
                'email' => $email,
                'phone' => trim($_POST['phone'] ?? '')
            ]);
            
            if (empty($user->getFullName()) || empty($user->getEmail())) {
                throw new Exception('Please fill in all required fields.');
            }
            
            $user->update();
            $_SESSION['flash_message'] = 'User updated successfully!';
            
        } elseif ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Invalid user ID.');
            
            User::delete($id);
            $_SESSION['flash_message'] = 'User deleted successfully!';
        }
        
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
        
    } catch (Exception $e) {
        $_SESSION['flash_error'] = $e->getMessage();
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
}

try {
    $users = Database::query("
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
    <?php include __DIR__ . '/partials/header.php'; ?>

    <div class="flex">
        <?php $currentPage = 'users'; include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="flex-1 p-8">
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Users Management</h1>
                        <p class="text-gray-600">Manage user accounts and profiles</p>
                    </div>
                    <button onclick="openModal('create')" class="bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded-md">
                        <i class="fas fa-plus mr-2"></i>Add User
                    </button>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                    <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                    <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bookings</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Spent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#<?= $user['id'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($user['full_name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($user['email']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($user['phone'] ?: 'N/A') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <?= $user['booking_count'] ?> booking<?= $user['booking_count'] != 1 ? 's' : '' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">$<?= number_format($user['total_spent'], 2) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick='openModal("update", <?= htmlspecialchars(json_encode($user)) ?>)' class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteUser(<?= $user['id'] ?>)" class="text-red-600 hover:text-red-900">
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
                    <?= csrfField() ?>
                    <input type="hidden" name="action" id="formAction">
                    <input type="hidden" name="id" id="userId">
                    
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Add User</h3>
                    </div>
                    
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                            <input type="text" id="full_name" name="full_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                            <input type="email" id="email" name="email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                            <input type="tel" id="phone" name="phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-purple-700 text-white rounded-md hover:bg-purple-800">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="deleteForm" method="POST" style="display: none;">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="deleteId">
    </form>

    <script>
        function openModal(action, user = null) {
            document.getElementById('formAction').value = action;
            
            if (action === 'create') {
                document.getElementById('modalTitle').textContent = 'Add User';
                document.getElementById('userForm').reset();
                document.getElementById('userId').value = '';
            } else if (action === 'update' && user) {
                document.getElementById('modalTitle').textContent = 'Edit User';
                document.getElementById('userId').value = user.id;
                document.getElementById('full_name').value = user.full_name;
                document.getElementById('email').value = user.email;
                document.getElementById('phone').value = user.phone || '';
            }
            
            document.getElementById('modal').classList.remove('hidden');
        }
        
        function closeModal() { document.getElementById('modal').classList.add('hidden'); }
        
        function deleteUser(id) {
            if (confirm('Are you sure you want to delete this user? This will also delete all their bookings.')) {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
        
        document.getElementById('modal').addEventListener('click', e => { if (e.target.id === 'modal') closeModal(); });
    </script>
</body>
</html>
