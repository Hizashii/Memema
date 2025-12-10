<?php
/**
 * Venues Management - Admin Panel
 * Uses OOP Venue class for all database operations
 */
require_once __DIR__ . '/../app/auth/admin_auth.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/config/security.php';
require_once __DIR__ . '/../app/classes/Database.php';
require_once __DIR__ . '/../app/classes/Venue.php';
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
            $venue = new Venue([
                'name' => trim($_POST['name'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'image' => trim($_POST['image'] ?? '')
            ]);
            
            if (empty($venue->getName()) || empty($venue->getAddress()) || empty($venue->getPhone())) {
                throw new Exception('Please fill in all required fields.');
            }
            
            $venue->create();
            $_SESSION['flash_message'] = 'Venue created successfully!';
            
        } elseif ($action === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Invalid venue ID.');
            
            $venue = new Venue([
                'id' => $id,
                'name' => trim($_POST['name'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'image' => trim($_POST['image'] ?? '')
            ]);
            
            if (empty($venue->getName()) || empty($venue->getAddress()) || empty($venue->getPhone())) {
                throw new Exception('Please fill in all required fields.');
            }
            
            $venue->update();
            $_SESSION['flash_message'] = 'Venue updated successfully!';
            
        } elseif ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Invalid venue ID.');
            
            Venue::delete($id);
            $_SESSION['flash_message'] = 'Venue deleted successfully!';
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
    $venues = Venue::getAll();
} catch (Exception $e) {
    $venues = [];
    $error = "Unable to load venues.";
}

$admin = getAdminInfo();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venues Management - CinemaBook Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include __DIR__ . '/partials/header.php'; ?>

    <div class="flex">
        <?php $currentPage = 'venues'; include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="flex-1 p-8">
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Venues Management</h1>
                        <p class="text-gray-600">Manage cinema venues and locations</p>
                    </div>
                    <button onclick="openModal('create')" class="bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded-md">
                        <i class="fas fa-plus mr-2"></i>Add Venue
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

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($venues as $venue): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <img src="<?= getImagePath($venue['image']) ?>" alt="<?= htmlspecialchars($venue['name']) ?>" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2"><?= htmlspecialchars($venue['name']) ?></h3>
                            <div class="space-y-2 text-sm text-gray-600">
                                <div class="flex items-start">
                                    <i class="fas fa-map-marker-alt text-purple-600 mt-1 mr-2"></i>
                                    <span><?= htmlspecialchars($venue['address']) ?></span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-phone text-purple-600 mr-2"></i>
                                    <span><?= htmlspecialchars($venue['phone']) ?></span>
                                </div>
                            </div>
                            <div class="mt-4 flex space-x-2">
                                <button onclick='openModal("update", <?= htmlspecialchars(json_encode($venue)) ?>)' class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-md text-sm">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </button>
                                <button onclick="deleteVenue(<?= $venue['id'] ?>)" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <form method="POST" id="venueForm">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" id="formAction">
                    <input type="hidden" name="id" id="venueId">
                    
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Add Venue</h3>
                    </div>
                    
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                            <input type="text" id="name" name="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">Address *</label>
                            <textarea id="address" name="address" rows="3" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"></textarea>
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone *</label>
                            <input type="tel" id="phone" name="phone" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700">Venue Image</label>
                            <div class="mt-1 flex space-x-4">
                                <input type="text" id="image" name="image" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500" placeholder="./assets/img/venue.jpg">
                                <button type="button" onclick="openFileUpload()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md text-sm">
                                    <i class="fas fa-upload mr-1"></i>Upload
                                </button>
                            </div>
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

    <!-- File Upload Modal -->
    <div id="uploadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Upload Image</h3>
                </div>
                <div class="px-6 py-4">
                    <form id="uploadForm" enctype="multipart/form-data">
                        <div class="mb-4">
                            <input type="file" id="imageFile" name="image" accept="image/*" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700">
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeUploadModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-purple-700 text-white rounded-md hover:bg-purple-800">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <form id="deleteForm" method="POST" style="display: none;">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="deleteId">
    </form>

    <script>
        function openModal(action, venue = null) {
            document.getElementById('formAction').value = action;
            
            if (action === 'create') {
                document.getElementById('modalTitle').textContent = 'Add Venue';
                document.getElementById('venueForm').reset();
                document.getElementById('venueId').value = '';
            } else if (action === 'update' && venue) {
                document.getElementById('modalTitle').textContent = 'Edit Venue';
                document.getElementById('venueId').value = venue.id;
                document.getElementById('name').value = venue.name;
                document.getElementById('address').value = venue.address;
                document.getElementById('phone').value = venue.phone;
                document.getElementById('image').value = venue.image || '';
            }
            
            document.getElementById('modal').classList.remove('hidden');
        }
        
        function closeModal() { document.getElementById('modal').classList.add('hidden'); }
        
        function deleteVenue(id) {
            if (confirm('Are you sure you want to delete this venue?')) {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
        
        document.getElementById('modal').addEventListener('click', e => { if (e.target.id === 'modal') closeModal(); });
        
        function openFileUpload() { document.getElementById('uploadModal').classList.remove('hidden'); }
        function closeUploadModal() { document.getElementById('uploadModal').classList.add('hidden'); document.getElementById('uploadForm').reset(); }
        
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const btn = this.querySelector('button[type="submit"]');
            btn.textContent = 'Uploading...';
            btn.disabled = true;
            
            fetch('<?= route('admin.upload') ?>', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('image').value = data.relative_path || data.path;
                    closeUploadModal();
                } else {
                    alert('Upload failed: ' + data.error);
                }
            })
            .catch(err => alert('Upload failed: ' + err))
            .finally(() => { btn.textContent = 'Upload'; btn.disabled = false; });
        });
        
        document.getElementById('uploadModal').addEventListener('click', e => { if (e.target.id === 'uploadModal') closeUploadModal(); });
    </script>
</body>
</html>
