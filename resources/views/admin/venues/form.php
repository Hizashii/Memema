<?php
require_once __DIR__ . '/../_helpers.php';
View::partial('admin_header');
?>
<div class="flex">
<?php
View::partial('admin_sidebar', ['currentPage' => 'venues']);
?>
<div class="flex-1 p-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-900"><?= $venue ? 'Edit' : 'Create' ?> Venue</h1>
    
    <?php if (isset($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">
            <?= View::e($error) ?>
        </div>
    <?php endif; ?>
    
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="<?= $venue ? adminUrl('/admin/venues/update') : adminUrl('/admin/venues') ?>" enctype="multipart/form-data">
            <?= Csrf::field() ?>
            <?php if ($venue): ?>
                <input type="hidden" name="id" value="<?= (int)$venue['id'] ?>">
                <input type="hidden" name="current_image" value="<?= View::e($venue['image'] ?? '') ?>">
            <?php endif; ?>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" name="name" value="<?= View::e($venue['name'] ?? '') ?>" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" name="address" value="<?= View::e($venue['address'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="<?= View::e($venue['phone'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                    <?php if ($venue && !empty($venue['image'])): ?>
                        <div class="mb-2">
                            <img src="<?= getImagePath($venue['image']) ?>" alt="Current image" class="max-w-xs h-32 object-cover rounded border">
                            <p class="text-xs text-gray-500 mt-1">Current image</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                    <p class="text-xs text-gray-500 mt-1">Upload a new image (JPG, PNG, GIF, WebP - max 5MB). Leave empty to keep current image.</p>
                </div>
            </div>
            
            <div class="mt-6 flex gap-4">
                <button type="submit" class="bg-purple-700 hover:bg-purple-800 text-white px-6 py-2 rounded-md">
                    <?= $venue ? 'Update' : 'Create' ?> Venue
                </button>
                <a href="<?= adminUrl('/admin/venues') ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-md">
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

