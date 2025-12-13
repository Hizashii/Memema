<?php
require_once __DIR__ . '/../_helpers.php';
View::partial('admin_header');
?>
<div class="flex">
<?php
View::partial('admin_sidebar', ['currentPage' => 'users']);
?>
<div class="flex-1 p-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-900">Create User</h1>
    
    <?php if (isset($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">
            <?= View::e($error) ?>
        </div>
    <?php endif; ?>
    
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="<?= adminUrl('/admin/users') ?>">
            <?= Csrf::field() ?>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="full_name" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                    <input type="password" name="password" required minlength="6"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                    <p class="text-xs text-gray-500 mt-1">Minimum 6 characters</p>
                </div>
            </div>
            
            <div class="mt-6 flex gap-4">
                <button type="submit" class="bg-purple-700 hover:bg-purple-800 text-white px-6 py-2 rounded-md">
                    Create User
                </button>
                <a href="<?= adminUrl('/admin/users') ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-md">
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

