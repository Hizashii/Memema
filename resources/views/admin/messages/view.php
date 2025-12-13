<?php
require_once __DIR__ . '/../_helpers.php';
View::partial('admin_header');
?>
<div class="flex">
<?php
View::partial('admin_sidebar', ['currentPage' => 'messages']);
?>
<div class="flex-1 p-8">
    <div class="mb-6">
        <a href="<?= adminUrl('/admin/messages') ?>" class="text-purple-700 hover:text-purple-800 mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Messages
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Message Details</h1>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="space-y-4">
            <div class="border-b pb-4">
                <label class="block text-sm font-medium text-gray-500 mb-1">From</label>
                <p class="text-lg font-semibold text-gray-900"><?= View::e($message['name']) ?></p>
                <p class="text-sm text-gray-600"><?= View::e($message['email']) ?></p>
            </div>
            
            <div class="border-b pb-4">
                <label class="block text-sm font-medium text-gray-500 mb-1">Subject</label>
                <p class="text-lg text-gray-900"><?= View::e($message['subject']) ?></p>
            </div>
            
            <div class="border-b pb-4">
                <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                <span class="px-3 py-1 text-sm font-semibold rounded-full <?= $message['status'] === 'new' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' ?>">
                    <?= View::e(ucfirst($message['status'])) ?>
                </span>
            </div>
            
            <div class="border-b pb-4">
                <label class="block text-sm font-medium text-gray-500 mb-1">Date</label>
                <p class="text-gray-900"><?= date('F j, Y g:i A', strtotime($message['created_at'])) ?></p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-2">Message</label>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <p class="text-gray-900 whitespace-pre-wrap"><?= View::e($message['message']) ?></p>
                </div>
            </div>
        </div>
        
        <div class="mt-6 flex gap-4">
            <a href="mailto:<?= View::e($message['email']) ?>?subject=Re: <?= urlencode($message['subject']) ?>" 
               class="bg-purple-700 hover:bg-purple-800 text-white px-6 py-2 rounded-md">
                <i class="fas fa-reply mr-2"></i>Reply
            </a>
            <a href="<?= adminUrl('/admin/messages') ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-md">
                Back to Messages
            </a>
        </div>
    </div>
</div>
</div>
<?php
View::partial('admin_footer');
?>

