<?php
require_once __DIR__ . '/../app/auth/admin_auth.php';
require_once __DIR__ . '/../app/config/security.php';
require_once __DIR__ . '/../app/core/router.php';
require_once __DIR__ . '/../app/classes/ContactMessage.php';

// Set security headers
setSecurityHeaders();

requireAdminLogin();

// Flash messages
$message = $_SESSION['flash_message'] ?? '';
$error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Validate CSRF token
    validateCSRF();
    
    if ($_POST['action'] === 'update_status') {
        $messageId = (int)$_POST['message_id'];
        $status = $_POST['status'];
        
        try {
            ContactMessage::updateStatus($messageId, $status);
            $message = 'Status updated successfully';
        } catch (Exception $e) {
            $error = 'Failed to update status';
        }
    }
}

try {
    $messages = ContactMessage::getAll();
} catch (Exception $e) {
    $messages = [];
    $error = 'Failed to load contact messages';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Cinema Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'partials/header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Contact Messages</h1>
            <a href="<?= route('admin.dashboard') ?>" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>

        <?php if ($message): ?>
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-sm border">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($messages)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-4"></i>
                                    <p>No contact messages found</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($messages as $msg): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($msg['name']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?= htmlspecialchars($msg['email']) ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate"><?= htmlspecialchars($msg['subject']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $statusColors = [
                                            'new' => 'bg-blue-100 text-blue-800',
                                            'read' => 'bg-yellow-100 text-yellow-800',
                                            'replied' => 'bg-green-100 text-green-800'
                                        ];
                                        $color = $statusColors[$msg['status']] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $color ?>">
                                            <?= ucfirst($msg['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= date('M j, Y g:i A', strtotime($msg['created_at'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button onclick="viewMessage(<?= $msg['id'] ?>, '<?= htmlspecialchars(addslashes($msg['name'])) ?>', '<?= htmlspecialchars(addslashes($msg['email'])) ?>', '<?= htmlspecialchars(addslashes($msg['subject'])) ?>', '<?= htmlspecialchars(addslashes($msg['message'])) ?>', '<?= $msg['status'] ?>')"
                                                class="text-purple-600 hover:text-purple-900">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <form method="POST" class="inline">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                                            <select name="status" onchange="this.form.submit()" class="text-xs border rounded px-2 py-1">
                                                <option value="new" <?= $msg['status'] === 'new' ? 'selected' : '' ?>>New</option>
                                                <option value="read" <?= $msg['status'] === 'read' ? 'selected' : '' ?>>Read</option>
                                                <option value="replied" <?= $msg['status'] === 'replied' ? 'selected' : '' ?>>Replied</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Message Modal -->
    <div id="messageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full max-h-96 overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Message Details</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div id="modalContent" class="space-y-4">
                        <!-- Content will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function viewMessage(id, name, email, subject, message, status) {
        const modal = document.getElementById('messageModal');
        const content = document.getElementById('modalContent');
        
        content.innerHTML = `
            <div class="border-b pb-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">From:</span>
                        <span class="text-gray-900">${name}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Email:</span>
                        <span class="text-gray-900">${email}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Subject:</span>
                        <span class="text-gray-900">${subject}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Status:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            ${status.charAt(0).toUpperCase() + status.slice(1)}
                        </span>
                    </div>
                </div>
            </div>
            <div>
                <span class="font-medium text-gray-700 block mb-2">Message:</span>
                <div class="bg-gray-50 p-4 rounded-lg text-gray-900 whitespace-pre-wrap">${message}</div>
            </div>
        `;
        
        modal.classList.remove('hidden');
    }
    
    function closeModal() {
        document.getElementById('messageModal').classList.add('hidden');
    }
    
    document.getElementById('messageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
    </script>
</body>
</html>
