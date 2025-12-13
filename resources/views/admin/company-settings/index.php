<?php
require_once __DIR__ . '/../_helpers.php';
View::partial('admin_header');
?>
<div class="flex">
<?php
View::partial('admin_sidebar', ['currentPage' => 'settings']);
?>
<div class="flex-1 p-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-900">Company Settings</h1>
    
    <?php if (isset($success)): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-6">
            <i class="fas fa-check-circle mr-2"></i>
            <?= View::e($success) ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <?= View::e($error) ?>
        </div>
    <?php endif; ?>
    
    <div class="bg-white rounded-lg shadow p-8 max-w-4xl">
        <form method="POST" action="<?= adminUrl('/admin/settings') ?>" class="space-y-6">
            <?= Csrf::field() ?>
            <input type="hidden" name="id" value="<?= (int)($settings['id'] ?? 0) ?>">
            
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Company Title <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="title" 
                       name="title" 
                       value="<?= View::e($settings['title'] ?? '') ?>" 
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
            </div>
            
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Company Description <span class="text-red-500">*</span>
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="5" 
                          required
                          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"><?= View::e($settings['description'] ?? '') ?></textarea>
            </div>
            
            <div>
                <label for="features" class="block text-sm font-medium text-gray-700 mb-2">
                    Features (comma-separated)
                </label>
                <input type="text" 
                       id="features" 
                       name="features" 
                       value="<?= View::e(is_array($settings['features'] ?? []) ? implode(', ', $settings['features']) : ($settings['features'] ?? '')) ?>" 
                       placeholder="Feature 1, Feature 2, Feature 3"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                <p class="text-sm text-gray-500 mt-1">Separate multiple features with commas</p>
            </div>
            
            <div>
                <label for="opening_hours" class="block text-sm font-medium text-gray-700 mb-2">
                    Opening Hours
                </label>
                <textarea id="opening_hours" 
                          name="opening_hours" 
                          rows="4" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
                          placeholder="Monday-Friday: 10:00 AM - 11:00 PM&#10;Saturday-Sunday: 9:00 AM - 12:00 AM"><?= View::e($settings['opening_hours'] ?? '') ?></textarea>
            </div>
            
            <div class="flex gap-4">
                <button type="submit" 
                        class="bg-purple-700 hover:bg-purple-800 text-white px-6 py-2 rounded-md">
                    <i class="fas fa-save mr-2"></i>Save Settings
                </button>
                <a href="<?= adminUrl('/admin') ?>" 
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

