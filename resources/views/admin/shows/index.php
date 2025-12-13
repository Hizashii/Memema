<?php
require_once __DIR__ . '/../_helpers.php';
View::partial('admin_header');
?>
<div class="flex">
<?php
View::partial('admin_sidebar', ['currentPage' => 'shows']);
?>
<div class="flex-1 p-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Daily Showings</h1>
        <a href="<?= adminUrl('/admin/shows/create') ?>" 
           class="bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded-md">
            <i class="fas fa-plus mr-2"></i>Add New Show
        </a>
    </div>
    
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
    
    <?php if (empty($shows)): ?>
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-600">No shows found. <a href="<?= adminUrl('/admin/shows/create') ?>" class="text-purple-700 hover:text-purple-800">Create your first show</a></p>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Image</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tag</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Showtimes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($shows as $show): ?>
                        <tr>
                            <td class="px-6 py-4">
                                <img src="<?= getImagePath($show['img']) ?>" 
                                     alt="<?= View::e($show['title']) ?>" 
                                     class="w-20 h-12 object-cover rounded">
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= View::e($show['title']) ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full text-white" style="background-color: <?= View::e($show['tag_color']) ?>">
                                    <?= View::e($show['tag_text']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?php if (!empty($show['showtimes'])): ?>
                                    <?= View::e(implode(', ', $show['showtimes'])) ?>
                                <?php else: ?>
                                    <span class="text-gray-400">No showtimes</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="<?= adminUrl('/admin/shows/edit?id=' . (int)$show['id']) ?>" 
                                   class="text-purple-700 hover:text-purple-800 mr-4">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <a href="<?= adminUrl('/admin/shows/delete?id=' . (int)$show['id']) ?>" 
                                   onclick="return confirm('Are you sure you want to delete this show?')"
                                   class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
</div>
<?php
View::partial('admin_footer');
?>

