<?php
require_once __DIR__ . '/../_helpers.php';
View::partial('admin_header');
?>
<div class="flex">
<?php
View::partial('admin_sidebar', ['currentPage' => 'bookings']);
?>
<div class="flex-1 p-8">
    <div class="mb-6">
        <a href="<?= adminUrl('/admin/bookings') ?>" class="text-purple-700 hover:text-purple-800 mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Bookings
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Invoice #<?= View::e($invoice['booking_number']) ?></h1>
    </div>
    
    <div class="bg-white rounded-lg shadow p-8 max-w-4xl">
        <div class="border-b border-gray-200 pb-6 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">CinemaBook</h2>
                    <p class="text-gray-600">Movie Theater Invoice</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Invoice #</p>
                    <p class="text-lg font-semibold"><?= View::e($invoice['booking_number']) ?></p>
                    <p class="text-sm text-gray-600 mt-2">Date: <?= date('F j, Y', strtotime($invoice['created_at'])) ?></p>
                </div>
            </div>
        </div>
        
        <div class="grid md:grid-cols-2 gap-8 mb-8">
            <div>
                <h3 class="font-semibold text-gray-900 mb-2">Customer Information</h3>
                <p class="text-gray-700"><?= View::e($invoice['user_name']) ?></p>
                <p class="text-gray-600 text-sm"><?= View::e($invoice['user_email']) ?></p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 mb-2">Booking Details</h3>
                <p class="text-gray-700">Movie: <?= View::e($invoice['movie_title']) ?></p>
                <p class="text-gray-700">Venue: <?= View::e($invoice['venue_name']) ?></p>
                <p class="text-gray-700">Screen: <?= View::e($invoice['screen_name']) ?></p>
                <p class="text-gray-700">Date: <?= date('F j, Y', strtotime($invoice['show_date'])) ?></p>
                <p class="text-gray-700">Time: <?= View::e($invoice['show_time']) ?></p>
            </div>
        </div>
        
        <div class="border-t border-gray-200 pt-6 mb-6">
            <h3 class="font-semibold text-gray-900 mb-4">Seats</h3>
            <div class="flex flex-wrap gap-2">
                <?php foreach ($invoice['seats'] as $seat): ?>
                    <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded text-sm font-semibold">
                        <?= View::e($seat['seat_row']) ?><?= View::e($seat['seat_number']) ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="border-t border-gray-200 pt-6">
            <div class="flex justify-between items-center">
                <span class="text-lg font-semibold text-gray-900">Total Amount</span>
                <span class="text-2xl font-bold text-purple-700">$<?= number_format((float)$invoice['total_price'], 2) ?></span>
            </div>
            <p class="text-sm text-gray-600 mt-2 text-right"><?= (int)$invoice['seats_count'] ?> seat(s) × $<?= number_format((float)$invoice['total_price'] / (int)$invoice['seats_count'], 2) ?></p>
        </div>
        
        <div class="mt-8 pt-6 border-t border-gray-200">
            <p class="text-sm text-gray-600 text-center">Thank you for your booking!</p>
        </div>
    </div>
    
    <div class="mt-6">
        <button onclick="window.print()" class="bg-purple-700 hover:bg-purple-800 text-white px-6 py-2 rounded-md">
            <i class="fas fa-print mr-2"></i>Print Invoice
        </button>
    </div>
</div>
</div>
<?php
View::partial('admin_footer');
?>

