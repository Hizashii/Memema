<?php
/**
 * Movies Management - Admin Panel
 * Uses OOP Movie class for all database operations
 */
require_once __DIR__ . '/../app/auth/admin_auth.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/config/security.php';
require_once __DIR__ . '/../app/classes/Database.php';
require_once __DIR__ . '/../app/classes/Movie.php';
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
            $movie = new Movie([
                'title' => trim($_POST['title'] ?? ''),
                'img' => trim($_POST['img'] ?? ''),
                'duration_minutes' => (int)($_POST['duration_minutes'] ?? 0),
                'rating' => (float)($_POST['rating'] ?? 0),
                'genres' => $_POST['genres'] ?? []
            ]);
            
            if (empty($movie->getTitle()) || empty($movie->getImg()) || $movie->getDurationMinutes() <= 0) {
                throw new Exception('Please fill in all required fields.');
            }
            
            $movie->create();
            $_SESSION['flash_message'] = 'Movie created successfully!';
            
        } elseif ($action === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Invalid movie ID.');
            
            $existingMovie = Movie::getById($id);
            if (!$existingMovie) throw new Exception('Movie not found.');
            
            $movie = new Movie([
                'id' => $id,
                'title' => trim($_POST['title'] ?? ''),
                'img' => trim($_POST['img'] ?? ''),
                'duration_minutes' => (int)($_POST['duration_minutes'] ?? 0),
                'rating' => (float)($_POST['rating'] ?? 0),
                'genres' => $_POST['genres'] ?? []
            ]);
            
            if (empty($movie->getTitle()) || empty($movie->getImg()) || $movie->getDurationMinutes() <= 0) {
                throw new Exception('Please fill in all required fields.');
            }
            
            $movie->update();
            $_SESSION['flash_message'] = 'Movie updated successfully!';
            
        } elseif ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Invalid movie ID.');
            
            Movie::delete($id);
            $_SESSION['flash_message'] = 'Movie deleted successfully!';
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
    $movies = Movie::getAll();
    $all_genres = Database::query("SELECT DISTINCT genre FROM movie_genres ORDER BY genre");
} catch (Exception $e) {
    $movies = [];
    $all_genres = [];
    $error = "Unable to load movies.";
}

$admin = getAdminInfo();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movies Management - CinemaBook Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include __DIR__ . '/partials/header.php'; ?>

    <div class="flex">
        <?php $currentPage = 'movies'; include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="flex-1 p-8">
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Movies Management</h1>
                        <p class="text-gray-600">Manage your movie catalog</p>
                    </div>
                    <button onclick="openModal('create')" class="bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded-md">
                        <i class="fas fa-plus mr-2"></i>Add Movie
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Poster</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rating</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Genres</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($movies as $movie): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <img src="<?= getImagePath($movie['img']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>" class="h-16 w-12 object-cover rounded">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($movie['title']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $movie['duration_minutes'] ?> min</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= number_format($movie['rating'], 1) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-1">
                                        <?php foreach ($movie['genres'] as $genre): ?>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                <?= htmlspecialchars($genre) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <?php 
                                    $movieData = $movie;
                                    $movieData['genres'] = array_map(fn($g) => ['genre' => $g], $movie['genres']);
                                    ?>
                                    <button onclick='openModal("update", <?= htmlspecialchars(json_encode($movieData)) ?>)' class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteMovie(<?= $movie['id'] ?>)" class="text-red-600 hover:text-red-900">
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
                <form method="POST" id="movieForm">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" id="formAction">
                    <input type="hidden" name="id" id="movieId">
                    
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Add Movie</h3>
                    </div>
                    
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                            <input type="text" id="title" name="title" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <div>
                            <label for="img" class="block text-sm font-medium text-gray-700">Movie Poster *</label>
                            <div class="mt-1 flex space-x-4">
                                <input type="text" id="img" name="img" required class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500" placeholder="./assets/img/movie.jpg">
                                <button type="button" onclick="openFileUpload()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md text-sm">
                                    <i class="fas fa-upload mr-1"></i>Upload
                                </button>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="duration_minutes" class="block text-sm font-medium text-gray-700">Duration (min) *</label>
                                <input type="number" id="duration_minutes" name="duration_minutes" required min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label for="rating" class="block text-sm font-medium text-gray-700">Rating</label>
                                <input type="number" id="rating" name="rating" step="0.1" min="0" max="5" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Genres</label>
                            <div id="genresContainer"></div>
                            <button type="button" onclick="addGenre()" class="text-purple-600 hover:text-purple-800 text-sm">
                                <i class="fas fa-plus mr-1"></i> Add Genre
                            </button>
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
                            <label for="imageFile" class="block text-sm font-medium text-gray-700 mb-2">Select Image</label>
                            <input type="file" id="imageFile" name="image" accept="image/*" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
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
        function openModal(action, movie = null) {
            document.getElementById('formAction').value = action;
            
            if (action === 'create') {
                document.getElementById('modalTitle').textContent = 'Add Movie';
                document.getElementById('movieForm').reset();
                document.getElementById('movieId').value = '';
                document.getElementById('genresContainer').innerHTML = '';
                addGenre();
            } else if (action === 'update' && movie) {
                document.getElementById('modalTitle').textContent = 'Edit Movie';
                document.getElementById('movieId').value = movie.id;
                document.getElementById('title').value = movie.title;
                document.getElementById('img').value = movie.img;
                document.getElementById('duration_minutes').value = movie.duration_minutes;
                document.getElementById('rating').value = movie.rating;
                
                document.getElementById('genresContainer').innerHTML = '';
                movie.genres.forEach(genre => addGenre(genre.genre));
                if (movie.genres.length === 0) addGenre();
            }
            
            document.getElementById('modal').classList.remove('hidden');
        }
        
        function closeModal() { document.getElementById('modal').classList.add('hidden'); }
        
        function addGenre(value = '') {
            const container = document.getElementById('genresContainer');
            const div = document.createElement('div');
            div.className = 'flex items-center space-x-2 mb-2';
            div.innerHTML = `
                <input type="text" name="genres[]" placeholder="Action" value="${value}" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                <button type="button" onclick="this.parentElement.remove()" class="text-red-600"><i class="fas fa-trash"></i></button>
            `;
            container.appendChild(div);
        }
        
        function deleteMovie(id) {
            if (confirm('Are you sure you want to delete this movie?')) {
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
                    document.getElementById('img').value = data.relative_path || data.path;
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
