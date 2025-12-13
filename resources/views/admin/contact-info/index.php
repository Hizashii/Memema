<?php
require_once __DIR__ . '/../_helpers.php';
View::partial('admin_header');
?>
<div class="flex">
<?php
View::partial('admin_sidebar', ['currentPage' => 'contact-info']);
?>
<div class="flex-1 p-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-900">Contact Information</h1>
    
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
        <form method="POST" action="<?= adminUrl('/admin/contact-info') ?>" class="space-y-6">
            <?= Csrf::field() ?>
            <input type="hidden" name="id" value="<?= (int)($contactInfo['id'] ?? 0) ?>">
            
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                    Phone <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="phone" 
                       name="phone" 
                       value="<?= View::e($contactInfo['phone'] ?? '') ?>" 
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
            </div>
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="<?= View::e($contactInfo['email'] ?? '') ?>" 
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
            </div>
            
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                    Address <span class="text-red-500">*</span>
                </label>
                <textarea id="address" 
                          name="address" 
                          rows="3" 
                          required
                          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"><?= View::e($contactInfo['address'] ?? '') ?></textarea>
            </div>
            
            <div class="flex gap-4">
                <button type="submit" 
                        class="bg-purple-700 hover:bg-purple-800 text-white px-6 py-2 rounded-md">
                    <i class="fas fa-save mr-2"></i>Save Contact Information
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

