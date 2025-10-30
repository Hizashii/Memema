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
            $title = trim($_POST['title'] ?? '');
            $img = trim($_POST['img'] ?? '');
            $duration_minutes = (int)($_POST['duration_minutes'] ?? 0);
            $rating = (float)($_POST['rating'] ?? 0);
            $genres = $_POST['genres'] ?? [];
            
            if (empty($title) || empty($img) || $duration_minutes <= 0) {
                throw new Exception('Please fill in all required fields.');
            }
            
            // Insert movie
            $movie_id = executePreparedQuery(
                "INSERT INTO movies (title, img, duration_minutes, rating) VALUES (?, ?, ?, ?)",
                [$title, $img, $duration_minutes, $rating],
                'ssid'
            );
            
            // Insert genres
            foreach ($genres as $genre) {
                if (!empty(trim($genre))) {
                    executePreparedQuery(
                        "INSERT INTO movie_genres (movie_id, genre) VALUES (?, ?)",
                        [$movie_id, trim($genre)],
                        'is'
                    );
                }
            }
            
            $message = 'Movie created successfully!';
            
        } elseif ($action === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $img = trim($_POST['img'] ?? '');
            $duration_minutes = (int)($_POST['duration_minutes'] ?? 0);
            $rating = (float)($_POST['rating'] ?? 0);
            $genres = $_POST['genres'] ?? [];
            
            if ($id <= 0 || empty($title) || empty($img) || $duration_minutes <= 0) {
                throw new Exception('Please fill in all required fields.');
            }
            
            // Update movie
            executePreparedQuery(
                "UPDATE movies SET title = ?, img = ?, duration_minutes = ?, rating = ? WHERE id = ?",
                [$title, $img, $duration_minutes, $rating, $id],
                'ssidi'
            );
            
            // Delete existing genres
            executePreparedQuery("DELETE FROM movie_genres WHERE movie_id = ?", [$id], 'i');
            
            // Insert new genres
            foreach ($genres as $genre) {
                if (!empty(trim($genre))) {
                    executePreparedQuery(
                        "INSERT INTO movie_genres (movie_id, genre) VALUES (?, ?)",
                        [$id, trim($genre)],
                        'is'
                    );
                }
            }
            
            $message = 'Movie updated successfully!';
            
        } elseif ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            
            if ($id <= 0) {
                throw new Exception('Invalid movie ID.');
            }
            
            // Delete movie (cascades to genres)
            executePreparedQuery("DELETE FROM movies WHERE id = ?", [$id], 'i');
            
            $message = 'Movie deleted successfully!';
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get all movies with their genres
try {
    $movies = executeQuery("SELECT * FROM movies ORDER BY id DESC");
    $all_genres = executeQuery("SELECT DISTINCT genre FROM movie_genres ORDER BY genre");
    
    // Get genres for each movie
    foreach ($movies as &$movie) {
        $movie['genres'] = executePreparedQuery(
            "SELECT genre FROM movie_genres WHERE movie_id = ?",
            [$movie['id']],
            'i'
        );
    }
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
                       class="flex items-center px-4 py-2 text-sm font-medium text-white bg-purple-700 rounded-md">
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
                       class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">
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
                        <h1 class="text-3xl font-bold text-gray-900">Movies Management</h1>
                        <p class="text-gray-600">Manage your movie catalog</p>
                    </div>
                    <button onclick="openModal('create')" 
                            class="bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded-md">
                        <i class="fas fa-plus mr-2"></i>
                        Add Movie
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

            <!-- Movies Table -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Poster</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Genres</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($movies as $movie): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <img src="<?= getImagePath($movie['img']) ?>" 
                                         alt="<?= htmlspecialchars($movie['title']) ?>" 
                                         class="h-16 w-12 object-cover rounded">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($movie['title']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= $movie['duration_minutes'] ?> min
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= number_format($movie['rating'], 1) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-1">
                                        <?php foreach ($movie['genres'] as $genre): ?>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                <?= htmlspecialchars($genre['genre']) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="openModal('update', <?= htmlspecialchars(json_encode($movie)) ?>)" 
                                            class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteMovie(<?= $movie['id'] ?>)" 
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
                <form method="POST" id="movieForm">
                    <input type="hidden" name="action" id="formAction">
                    <input type="hidden" name="id" id="movieId">
                    
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Add Movie</h3>
                    </div>
                    
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                            <input type="text" id="title" name="title" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <div>
                            <label for="img" class="block text-sm font-medium text-gray-700">Movie Poster *</label>
                            <div class="mt-1 flex space-x-4">
                                <input type="text" id="img" name="img" required
                                       class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"
                                       placeholder="./assets/img/movie.jpg">
                                <button type="button" onclick="openFileUpload()" 
                                        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md text-sm">
                                    <i class="fas fa-upload mr-1"></i>
                                    Upload
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Or upload an image file (JPG, PNG, GIF, WebP)</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="duration_minutes" class="block text-sm font-medium text-gray-700">Duration (min) *</label>
                                <input type="number" id="duration_minutes" name="duration_minutes" required min="1"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            
                            <div>
                                <label for="rating" class="block text-sm font-medium text-gray-700">Rating</label>
                                <input type="number" id="rating" name="rating" step="0.1" min="0" max="5"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Genres</label>
                            <div id="genresContainer">
                                <div class="flex items-center space-x-2 mb-2">
                                    <input type="text" name="genres[]" placeholder="Action"
                                           class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                    <button type="button" onclick="removeGenre(this)" class="text-red-600">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" onclick="addGenre()" 
                                    class="text-purple-600 hover:text-purple-800 text-sm">
                                <i class="fas fa-plus mr-1"></i> Add Genre
                            </button>
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
                            <input type="file" id="imageFile" name="image" accept="image/*" required
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeUploadModal()" 
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-purple-700 text-white rounded-md hover:bg-purple-800">
                                Upload
                            </button>
                        </div>
                    </form>
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
        function openModal(action, movie = null) {
            const modal = document.getElementById('modal');
            const form = document.getElementById('movieForm');
            const formAction = document.getElementById('formAction');
            const movieId = document.getElementById('movieId');
            const modalTitle = document.getElementById('modalTitle');
            
            formAction.value = action;
            
            if (action === 'create') {
                modalTitle.textContent = 'Add Movie';
                form.reset();
                movieId.value = '';
                // Reset genres to one empty field
                document.getElementById('genresContainer').innerHTML = `
                    <div class="flex items-center space-x-2 mb-2">
                        <input type="text" name="genres[]" placeholder="Action"
                               class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                        <button type="button" onclick="removeGenre(this)" class="text-red-600">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
            } else if (action === 'update' && movie) {
                modalTitle.textContent = 'Edit Movie';
                movieId.value = movie.id;
                document.getElementById('title').value = movie.title;
                document.getElementById('img').value = movie.img;
                document.getElementById('duration_minutes').value = movie.duration_minutes;
                document.getElementById('rating').value = movie.rating;
                
                // Set genres
                const genresContainer = document.getElementById('genresContainer');
                genresContainer.innerHTML = '';
                movie.genres.forEach(genre => {
                    addGenre(genre.genre);
                });
                if (movie.genres.length === 0) {
                    addGenre();
                }
            }
            
            modal.classList.remove('hidden');
        }
        
        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
        }
        
        function addGenre(value = '') {
            const container = document.getElementById('genresContainer');
            const div = document.createElement('div');
            div.className = 'flex items-center space-x-2 mb-2';
            div.innerHTML = `
                <input type="text" name="genres[]" placeholder="Action" value="${value}"
                       class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                <button type="button" onclick="removeGenre(this)" class="text-red-600">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            container.appendChild(div);
        }
        
        function removeGenre(button) {
            button.parentElement.remove();
        }
        
        function deleteMovie(id) {
            if (confirm('Are you sure you want to delete this movie?')) {
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
        
        // File upload functions
        function openFileUpload() {
            document.getElementById('uploadModal').classList.remove('hidden');
        }
        
        function closeUploadModal() {
            document.getElementById('uploadModal').classList.add('hidden');
            document.getElementById('uploadForm').reset();
        }
        
        // Handle file upload
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const uploadButton = this.querySelector('button[type="submit"]');
            const originalText = uploadButton.textContent;
            
            uploadButton.textContent = 'Uploading...';
            uploadButton.disabled = true;
            
            fetch('/Cinema/admin/upload.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('img').value = data.path;
                    closeUploadModal();
                    alert('Image uploaded successfully!');
                } else {
                    alert('Upload failed: ' + data.error);
                }
            })
            .catch(error => {
                alert('Upload failed: ' + error);
            })
            .finally(() => {
                uploadButton.textContent = originalText;
                uploadButton.disabled = false;
            });
        });
        
        // Close upload modal on outside click
        document.getElementById('uploadModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeUploadModal();
            }
        });
    </script>
</body>
</html>
