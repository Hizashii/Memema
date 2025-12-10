<?php
require_once __DIR__ . '/../../../app/classes/autoload.php';
require_once __DIR__ . '/../../../app/core/database.php';
require_once __DIR__ . '/../../../app/config/security.php';

// Include header only when accessed directly (not via index.php)
if (!defined('LOADED_VIA_INDEX')) {
    include dirname(__DIR__) . '/partials/header.php';
}

$movieId = validateInt($_GET['movie_id'] ?? 0, 1);
$venueId = validateInt($_GET['venue_id'] ?? 0, 1);
$screenId = validateInt($_GET['screen_id'] ?? 0, 1);
$showDate = sanitizeInput($_GET['date'] ?? date('Y-m-d'));
$showTime = sanitizeInput($_GET['time'] ?? '19:00');
$selectedSeats = array_filter(explode(',', sanitizeInput($_GET['seats'] ?? '')), 'strlen');

$movie = null;
$venue = null;
$screen = null;

try {
    $movie = $movieId ? Movie::getById($movieId) : null;
    $venue = $venueId ? Venue::getById($venueId) : null;
    $screen = $screenId ? Screen::getById($screenId) : null;
} catch (Exception $e) {
    $error = "Unable to load booking information. Please try again.";
}

$pricePerSeat = $screen ? (float)$screen['base_price'] : 12.50;
$total = count($selectedSeats) * $pricePerSeat;
?>

<main class="max-w-4xl mx-auto px-4 py-10 space-y-8">
  <h1 class="text-3xl font-bold text-center">Complete Your Booking</h1>

  <?php if ($movie && $venue && $screen && !empty($selectedSeats)): ?>
    <div class="bg-white rounded-xl border shadow-sm p-6">
      <div class="flex gap-6">
        <div class="w-32 h-48 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
        <img src="<?= getImagePath($movie['img']) ?>" 
             alt="<?= htmlspecialchars($movie['title']) ?>" 
             class="w-full h-full object-cover">
        </div>
        <div class="flex-1">
          <h2 class="text-2xl font-bold mb-4"><?= htmlspecialchars($movie['title']) ?></h2>

          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-gray-600">Venue:</span>
              <span class="font-semibold"><?= htmlspecialchars($venue['name']) ?></span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Screen:</span>
              <span class="font-semibold"><?= htmlspecialchars($screen['screen_name']) ?> (<?= htmlspecialchars($screen['screen_type']) ?>)</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Date & Time:</span>
              <span class="font-semibold"><?= date('M j, Y', strtotime($showDate)) ?> at <?= date('g:i A', strtotime($showTime)) ?></span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Selected Seats:</span>
              <span class="font-semibold"><?= implode(', ', $selectedSeats) ?></span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Number of Seats:</span>
              <span class="font-semibold"><?= count($selectedSeats) ?></span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Price per Seat:</span>
              <span class="font-semibold">$<?= number_format($pricePerSeat, 2) ?></span>
            </div>
            <div class="border-t pt-3">
              <div class="flex justify-between text-lg">
                <span class="font-bold">Total:</span>
                <span class="font-bold text-purple-700">$<?= number_format($total, 2) ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl border shadow-sm p-6">
      <h2 class="text-xl font-bold mb-6">Payment Information</h2>
      
      <form method="POST" action="/Cinema/public/frontend/pages/booking-confirmation.php" class="space-y-6">
        <input type="hidden" name="movie_id" value="<?= $movieId ?>">
        <input type="hidden" name="venue_id" value="<?= $venueId ?>">
        <input type="hidden" name="screen_id" value="<?= $screenId ?>">
        <input type="hidden" name="show_date" value="<?= htmlspecialchars($showDate) ?>">
        <input type="hidden" name="show_time" value="<?= htmlspecialchars($showTime) ?>">
        <input type="hidden" name="seats" value="<?= htmlspecialchars(implode(',', $selectedSeats)) ?>">
        <input type="hidden" name="total_price" value="<?= $total ?>">
        
        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
            <input type="text" id="full_name" name="full_name" required 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
          </div>
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input type="email" id="email" name="email" required 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
          </div>
        </div>

        <div>
          <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
          <input type="tel" id="phone" name="phone" required 
                 class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
          <div class="space-y-2">
            <label class="flex items-center">
              <input type="radio" name="payment_method" value="card" checked class="mr-2">
              <span>Credit/Debit Card</span>
            </label>
            <label class="flex items-center">
              <input type="radio" name="payment_method" value="cash" class="mr-2">
              <span>Pay at Cinema</span>
            </label>
          </div>
        </div>

        <div class="flex items-start">
          <input type="checkbox" id="terms" name="terms" required class="mt-1 mr-2">
          <label for="terms" class="text-sm text-gray-600">
            I agree to the <a href="#" class="text-purple-700 hover:underline">Terms and Conditions</a> and 
            <a href="#" class="text-purple-700 hover:underline">Privacy Policy</a>
          </label>
        </div>

        <div class="flex justify-center">
          <button type="submit" 
                  class="bg-purple-700 hover:bg-purple-800 text-white px-8 py-3 rounded-md font-semibold transition-colors">
            Complete Booking - $<?= number_format($total, 2) ?>
          </button>
        </div>
      </form>
    </div>

  <?php else: ?>
    <div class="text-center py-12">
      <h1 class="text-2xl font-bold mb-4">Invalid Booking</h1>
      <p class="text-gray-600 mb-6">Please complete your seat selection first.</p>
      <a href="/Cinema/public/frontend/pages/movies.php" 
         class="inline-block bg-purple-700 hover:bg-purple-800 text-white px-6 py-3 rounded-md">
        Browse Movies
      </a>
    </div>
  <?php endif; ?>
</main>

<?php if (!defined('LOADED_VIA_INDEX')) { include dirname(__DIR__) . '/partials/footer.php'; } ?>
