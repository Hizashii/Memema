<?php
require_once __DIR__ . '/../_helpers.php';
View::partial('admin_header');
?>
<div class="flex">
<?php
View::partial('admin_sidebar', ['currentPage' => 'bookings']);
?>
<div class="flex-1 p-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-900">Bookings Overview</h1>
    
    <?php if (isset($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">
            <?= View::e($error) ?>
        </div>
    <?php endif; ?>
    
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Movie</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date/Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seats</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($bookings)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No bookings found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= (int)$booking['id'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= View::e($booking['user_name'] ?? 'N/A') ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?= View::e($booking['movie_title'] ?? 'N/A') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= View::e($booking['show_date']) ?><br>
                                <?= View::e($booking['show_time']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= (int)$booking['seats_count'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">$<?= number_format((float)$booking['total_price'], 2) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $booking['status'] === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                    <?= View::e(ucfirst($booking['status'])) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="<?= adminUrl('/admin/bookings/invoice?id=' . (int)$booking['id']) ?>" 
                                   class="text-purple-700 hover:text-purple-800">
                                    <i class="fas fa-file-invoice mr-1"></i>Invoice
                                </a>
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

