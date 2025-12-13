<?php
View::partial('header');
?>

<main class="max-w-4xl mx-auto px-4 py-10">
  <div class="mb-8">
    <a href="javascript:history.back()" class="text-purple-700 hover:text-purple-800 mb-4 inline-block">
      <i class="fas fa-arrow-left mr-2"></i>Back to Seat Selection
    </a>
    
    <h1 class="text-4xl font-bold text-purple-700 mb-4">Checkout</h1>
  </div>
  
  <?php if (isset($error)): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">
      <i class="fas fa-exclamation-circle mr-2"></i>
      <?= View::e($error) ?>
    </div>
  <?php endif; ?>
  
  <!-- Booking Summary -->
  <div class="bg-white rounded-xl border shadow-sm p-8 max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold mb-6 text-purple-700">Booking Summary</h2>
    
    <div class="space-y-4 mb-8">
      <div>
        <p class="text-sm text-gray-600">Movie</p>
        <p class="font-semibold text-lg"><?= View::e($movie['title']) ?></p>
      </div>
      
      <div>
        <p class="text-sm text-gray-600">Venue</p>
        <p class="font-semibold"><?= View::e($venue['name']) ?></p>
        <p class="text-sm text-gray-500"><?= View::e($venue['address']) ?></p>
      </div>
      
      <div>
        <p class="text-sm text-gray-600">Screen</p>
        <p class="font-semibold"><?= View::e($screen['screen_name']) ?></p>
      </div>
      
      <div>
        <p class="text-sm text-gray-600">Date & Time</p>
        <p class="font-semibold"><?= date('F j, Y', strtotime($showDate)) ?> at <?= View::e($showTime) ?></p>
      </div>
      
      <div>
        <p class="text-sm text-gray-600">Seats</p>
        <div class="flex flex-wrap gap-2 mt-1">
          <?php foreach ($seats as $seat): ?>
            <span class="bg-purple-200 text-purple-800 px-3 py-1 rounded text-sm font-semibold">
              <?= View::e($seat) ?>
            </span>
          <?php endforeach; ?>
        </div>
      </div>
      
      <hr class="my-4">
      
      <div class="flex justify-between items-center">
        <span class="text-lg font-semibold">Total</span>
        <span class="text-2xl font-bold text-purple-700">$<?= number_format((float)$totalPrice, 2) ?></span>
      </div>
    </div>
    
    <!-- Complete Purchase Button -->
    <form method="POST" action="<?= url('/booking/process') ?>">
      <?= Csrf::field() ?>
      <input type="hidden" name="movie_id" value="<?= (int)$movie['id'] ?>">
      <input type="hidden" name="venue_id" value="<?= (int)$venue['id'] ?>">
      <input type="hidden" name="screen_id" value="<?= (int)$screen['id'] ?>">
      <input type="hidden" name="show_date" value="<?= View::e($showDate) ?>">
      <input type="hidden" name="show_time" value="<?= View::e($showTime) ?>">
      <?php foreach ($seats as $seat): ?>
        <input type="hidden" name="seats[]" value="<?= View::e($seat) ?>">
      <?php endforeach; ?>
      
      <button type="submit" 
              class="w-full bg-purple-700 hover:bg-purple-800 text-white font-semibold py-4 px-6 rounded-md transition-colors text-lg">
        <i class="fas fa-check-circle mr-2"></i>Complete Purchase
      </button>
    </form>
  </div>
</main>


<?php
View::partial('footer');
?>

