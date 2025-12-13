<?php
require_once __DIR__ . '/../_helpers.php';
View::partial('admin_header');
?>
<div class="flex">
<?php
View::partial('admin_sidebar', ['currentPage' => 'movies']);
?>
<div class="flex-1 p-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-900"><?= $movie ? 'Edit' : 'Create' ?> Movie</h1>
    
    <?php if (isset($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">
            <?= View::e($error) ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-6">
            <?= View::e($success) ?>
        </div>
    <?php endif; ?>
    
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="<?= $movie ? adminUrl('/admin/movies/update') : adminUrl('/admin/movies') ?>" enctype="multipart/form-data">
            <?= Csrf::field() ?>
            <?php if ($movie): ?>
                <input type="hidden" name="id" value="<?= (int)$movie['id'] ?>">
                <input type="hidden" name="current_img" value="<?= View::e($movie['img'] ?? '') ?>">
            <?php endif; ?>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                    <input type="text" name="title" value="<?= View::e($movie['title'] ?? '') ?>" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image *</label>
                    <?php if ($movie && !empty($movie['img'])): ?>
                        <div class="mb-2">
                            <img src="<?= getImagePath($movie['img']) ?>" alt="Current image" class="max-w-xs h-32 object-cover rounded border">
                            <p class="text-xs text-gray-500 mt-1">Current image</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                    <p class="text-xs text-gray-500 mt-1">Upload a new image (JPG, PNG, GIF, WebP - max 5MB). Leave empty to keep current image.</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes) *</label>
                        <input type="number" name="duration_minutes" value="<?= (int)($movie['duration_minutes'] ?? 0) ?>" required min="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rating (0-10) *</label>
                        <input type="number" name="rating" value="<?= number_format((float)($movie['rating'] ?? 0), 1) ?>" required min="0" max="10" step="0.1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Genres (comma-separated)</label>
                    <input type="text" name="genres" value="<?= View::e(implode(', ', $movie['genres'] ?? [])) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
                           placeholder="Action, Drama, Comedy">
                    <p class="text-xs text-gray-500 mt-1">Separate multiple genres with commas</p>
                </div>
            </div>
            
            <div class="mt-6 flex gap-4">
                <button type="submit" class="bg-purple-700 hover:bg-purple-800 text-white px-6 py-2 rounded-md">
                    <?= $movie ? 'Update' : 'Create' ?> Movie
                </button>
                <a href="<?= adminUrl('/admin/movies') ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-md">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
</div>
<?php
View::partial('admin_footer');
?>

