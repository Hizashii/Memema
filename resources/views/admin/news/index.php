<?php
require_once __DIR__ . '/../_helpers.php';
View::partial('admin_header');
?>
<div class="flex">
<?php
View::partial('admin_sidebar', ['currentPage' => 'news']);
?>
<div class="flex-1 p-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">News Management</h1>
        <a href="<?= adminUrl('/admin/news/create') ?>" class="bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded-md">
            <i class="fas fa-plus mr-2"></i>Add New News
        </a>
    </div>
    
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
    
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($news)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No news found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($news as $item): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= (int)$item['id'] ?></td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= View::e($item['title']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('Y-m-d', strtotime($item['created_at'])) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="<?= adminUrl('/admin/news/edit?id=' . (int)$item['id']) ?>" class="text-purple-600 hover:text-purple-900">Edit</a>
                                <a href="<?= adminUrl('/admin/news/delete?id=' . (int)$item['id']) ?>" 
                                   class="text-red-600 hover:text-red-900 ml-4"
                                   onclick="return confirm('Are you sure you want to delete this news?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>
<?php
View::partial('admin_footer');
?>

