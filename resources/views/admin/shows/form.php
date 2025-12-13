<?php
require_once __DIR__ . '/../_helpers.php';
View::partial('admin_header');
?>
<div class="flex">
<?php
View::partial('admin_sidebar', ['currentPage' => 'shows']);
?>
<div class="flex-1 p-8">
    <div class="mb-6">
        <a href="<?= adminUrl('/admin/shows') ?>" class="text-purple-700 hover:text-purple-800 mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Shows
        </a>
        <h1 class="text-3xl font-bold text-gray-900"><?= $show ? 'Edit' : 'Create' ?> Show</h1>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <?= View::e($error) ?>
        </div>
    <?php endif; ?>
    
    <div class="bg-white rounded-lg shadow p-8 max-w-4xl">
        <form method="POST" action="<?= adminUrl($show ? '/admin/shows/update' : '/admin/shows') ?>" enctype="multipart/form-data" class="space-y-6">
            <?= Csrf::field() ?>
            <input type="hidden" name="id" value="<?= (int)($show['id'] ?? 0) ?>">
            
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Title <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="title" 
                       name="title" 
                       value="<?= View::e($show['title'] ?? '') ?>" 
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
            </div>
            
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                    Image <span class="text-red-500">*</span>
                </label>
                <?php if ($show && !empty($show['img'])): ?>
                    <div class="mb-2">
                        <img src="<?= getImagePath($show['img']) ?>" alt="Current image" class="max-w-xs h-32 object-cover rounded border">
                    </div>
                <?php endif; ?>
                <input type="file" 
                       id="image" 
                       name="image" 
                       accept="image/*"
                       <?= !$show ? 'required' : '' ?>
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
            </div>
            
            <div>
                <label for="tag_text" class="block text-sm font-medium text-gray-700 mb-2">
                    Tag Text <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="tag_text" 
                       name="tag_text" 
                       value="<?= View::e($show['tag_text'] ?? '') ?>" 
                       placeholder="e.g., NEW, PREMIERE, SPECIAL"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
            </div>
            
            <div>
                <label for="tag_color" class="block text-sm font-medium text-gray-700 mb-2">
                    Tag Color <span class="text-red-500">*</span>
                </label>
                <input type="color" 
                       id="tag_color" 
                       name="tag_color" 
                       value="<?= View::e($show['tag_color'] ?? '#FF0000') ?>" 
                       required
                       class="w-20 h-10 border border-gray-300 rounded-md">
            </div>
            
            <div>
                <label for="showtimes" class="block text-sm font-medium text-gray-700 mb-2">
                    Showtimes (comma-separated)
                </label>
                <input type="text" 
                       id="showtimes" 
                       name="showtimes" 
                       value="<?= View::e(is_array($show['showtimes'] ?? []) ? implode(', ', $show['showtimes']) : '') ?>" 
                       placeholder="10:00, 13:00, 16:00, 19:00, 22:00"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                <p class="text-sm text-gray-500 mt-1">Separate multiple showtimes with commas</p>
            </div>
            
            <div class="flex gap-4">
                <button type="submit" 
                        class="bg-purple-700 hover:bg-purple-800 text-white px-6 py-2 rounded-md">
                    <i class="fas fa-save mr-2"></i><?= $show ? 'Update' : 'Create' ?> Show
                </button>
                <a href="<?= adminUrl('/admin/shows') ?>" 
                   class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-md">
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

