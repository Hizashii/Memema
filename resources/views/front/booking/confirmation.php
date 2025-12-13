<?php
View::partial('header');
?>

<main class="max-w-4xl mx-auto px-4 py-10">
  <div class="bg-green-50 border-2 border-green-200 rounded-xl p-8 text-center mb-8">
    <div class="mb-4">
      <i class="fas fa-check-circle text-green-600 text-6xl"></i>
    </div>
    <h1 class="text-3xl font-bold text-green-700 mb-4">Thank You For Your Purchase!</h1>
    <p class="text-lg text-gray-700 mb-2">
      Your booking has been successfully confirmed.
    </p>
    <?php if (isset($booking) && $booking): ?>
      <p class="text-sm text-gray-600 mt-2">
        Booking ID: <strong>BK<?= str_pad((int)$booking['id'], 6, '0', STR_PAD_LEFT) ?></strong>
      </p>
    <?php endif; ?>
  </div>
  
  <?php if (isset($booking) && $booking): ?>
    <div class="bg-white rounded-xl border shadow-sm p-8 mb-8">
      <h2 class="text-2xl font-bold mb-6 text-purple-700">Booking Details</h2>
      
      <div class="space-y-4">
        <div class="flex justify-between py-2 border-b">
          <span class="font-semibold text-gray-700">Movie:</span>
          <span class="text-gray-900"><?= View::e($booking['movie_title'] ?? 'N/A') ?></span>
        </div>
        
        <div class="flex justify-between py-2 border-b">
          <span class="font-semibold text-gray-700">Venue:</span>
          <span class="text-gray-900"><?= View::e($booking['venue_name'] ?? 'N/A') ?></span>
        </div>
        
        <div class="flex justify-between py-2 border-b">
          <span class="font-semibold text-gray-700">Screen:</span>
          <span class="text-gray-900"><?= View::e($booking['screen_name'] ?? 'N/A') ?></span>
        </div>
        
        <div class="flex justify-between py-2 border-b">
          <span class="font-semibold text-gray-700">Date:</span>
          <span class="text-gray-900"><?= View::e($booking['show_date']) ?></span>
        </div>
        
        <div class="flex justify-between py-2 border-b">
          <span class="font-semibold text-gray-700">Time:</span>
          <span class="text-gray-900"><?= View::e($booking['show_time']) ?></span>
        </div>
        
        <div class="flex justify-between py-2 border-b">
          <span class="font-semibold text-gray-700">Seats:</span>
          <span class="text-gray-900"><?= (int)$booking['seats_count'] ?> seat(s)</span>
        </div>
        
        <div class="flex justify-between py-2 border-b">
          <span class="font-semibold text-gray-700">Total Price:</span>
          <span class="text-purple-700 font-bold text-xl">$<?= number_format((float)$booking['total_price'], 2) ?></span>
        </div>
      </div>
    </div>
    
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
      <h3 class="font-semibold text-blue-800 mb-2">
        <i class="fas fa-info-circle mr-2"></i>What's Next?
      </h3>
      <ul class="text-blue-700 space-y-2 text-sm">
        <li>✓ You will receive a confirmation email shortly</li>
        <li>✓ The admin has been notified of your booking</li>
        <li>✓ Please arrive 15 minutes before the show time</li>
        <li>✓ Bring a valid ID for verification</li>
      </ul>
    </div>
  <?php endif; ?>
  
  <div class="flex gap-4 justify-center">
    <a href="<?= url('/') ?>" class="bg-purple-700 hover:bg-purple-800 text-white px-6 py-3 rounded-md transition-colors">
      <i class="fas fa-home mr-2"></i>Back to Home
    </a>
    <a href="<?= url('/movies') ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-md transition-colors">
      <i class="fas fa-film mr-2"></i>Browse More Movies
    </a>
  </div>
</main>

<?php
View::partial('footer');
?>

