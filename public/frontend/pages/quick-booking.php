<?php
require_once __DIR__ . '/../../../app/classes/autoload.php';
require_once __DIR__ . '/../../../app/core/database.php';
require_once __DIR__ . '/../../../app/auth/user_auth.php';

// Include header only when accessed directly (not via index.php)
if (!defined('LOADED_VIA_INDEX')) {
    include dirname(__DIR__) . '/partials/header.php';
}

requireUserLogin();

$user = getCurrentUser();
$message = '';
$error = '';

try {
    $movies = Movie::getAll();
    $venues = Venue::getAll();
    $screens = Screen::getAll();
} catch (Exception $e) {
    $movies = [];
    $venues = [];
    $screens = [];
    $error = "Unable to load booking information. Please try again.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movieId = (int)($_POST['movie_id'] ?? 0);
    $venueId = (int)($_POST['venue_id'] ?? 0);
    $screenId = (int)($_POST['screen_id'] ?? 0);
    $showDate = $_POST['show_date'] ?? '';
    $showTime = $_POST['show_time'] ?? '';
    $seatsCount = (int)($_POST['seats_count'] ?? 1);
    
    if ($movieId && $venueId && $screenId && $showDate && $showTime && $seatsCount > 0) {
        try {
            $screenData = Screen::getById($screenId);
            if (!$screenData) {
                throw new Exception('Screen not found');
            }
            
            $basePrice = (float)$screenData['base_price'];
            $totalPrice = $basePrice * $seatsCount;
            
            // Create booking using Booking class
            $booking = new Booking([
                'user_id' => $user['id'],
                'movie_id' => $movieId,
                'venue_id' => $venueId,
                'screen_id' => $screenId,
                'show_date' => $showDate,
                'show_time' => $showTime,
                'seats_count' => $seatsCount,
                'total_price' => $totalPrice
            ]);
            
            if ($booking->create()) {
                $message = "Booking successful! Total: $" . number_format($totalPrice, 2);
            } else {
                $error = "Failed to create booking. Please try again.";
            }
        } catch (Exception $e) {
            $error = "Booking failed: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<main class="max-w-4xl mx-auto px-4 py-10 space-y-8">
  <div class="text-center">
    <h1 class="text-3xl font-bold text-purple-700 mb-2">Quick Booking</h1>
    <p class="text-gray-600">Book your movie tickets quickly and easily</p>
  </div>

  <?php if ($message): ?>
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
      <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
      <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <div class="bg-white rounded-xl border shadow-sm p-8">
    <form method="POST" class="space-y-6">
      <div class="grid gap-6 md:grid-cols-2">
        <div>
          <label for="movie_id" class="block text-sm font-medium text-gray-700 mb-2">Select Movie *</label>
          <select id="movie_id" name="movie_id" required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            <option value="">Choose a movie...</option>
            <?php foreach ($movies as $movie): ?>
              <option value="<?= $movie['id'] ?>"><?= htmlspecialchars($movie['title']) ?> (<?= (int)$movie['duration_minutes'] ?> min)</option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label for="venue_id" class="block text-sm font-medium text-gray-700 mb-2">Select Venue *</label>
          <select id="venue_id" name="venue_id" required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            <option value="">Choose a venue...</option>
            <?php foreach ($venues as $venue): ?>
              <option value="<?= $venue['id'] ?>"><?= htmlspecialchars($venue['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label for="screen_id" class="block text-sm font-medium text-gray-700 mb-2">Select Screen *</label>
          <select id="screen_id" name="screen_id" required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            <option value="">Choose a screen...</option>
            <?php foreach ($screens as $screen): ?>
              <option value="<?= $screen['id'] ?>" data-venue="<?= $screen['venue_id'] ?>" data-price="<?= $screen['base_price'] ?>">
                <?= htmlspecialchars($screen['screen_name']) ?> (<?= ucfirst($screen['screen_type']) ?>) - $<?= number_format((float)$screen['base_price'], 2) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label for="seats_count" class="block text-sm font-medium text-gray-700 mb-2">Number of Seats *</label>
          <select id="seats_count" name="seats_count" required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            <?php for ($i = 1; $i <= 6; $i++): ?>
              <option value="<?= $i ?>"><?= $i ?> seat<?= $i > 1 ? 's' : '' ?></option>
            <?php endfor; ?>
          </select>
        </div>

        <div>
          <label for="show_date" class="block text-sm font-medium text-gray-700 mb-2">Show Date *</label>
          <input type="date" id="show_date" name="show_date" required
                 min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime('+30 days')) ?>"
                 value="<?= date('Y-m-d') ?>"
                 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
        </div>

        <div>
          <label for="show_time" class="block text-sm font-medium text-gray-700 mb-2">Show Time *</label>
          <select id="show_time" name="show_time" required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            <option value="">Choose time...</option>
            <option value="10:00">10:00 AM</option>
            <option value="13:30">1:30 PM</option>
            <option value="16:00">4:00 PM</option>
            <option value="19:00">7:00 PM</option>
            <option value="22:30">10:30 PM</option>
          </select>
        </div>
      </div>

      <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
        <div class="flex justify-between items-center">
          <span class="text-lg font-semibold text-purple-700">Total Price:</span>
          <span id="totalPrice" class="text-2xl font-bold text-purple-700">$0.00</span>
        </div>
        <div class="text-sm text-purple-600 mt-1">
          <span id="priceBreakdown">Select a screen to see pricing</span>
        </div>
      </div>

      <button type="submit" 
              class="w-full bg-purple-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-purple-700 transition-colors duration-200">
        <i class="fas fa-ticket-alt mr-2"></i>Book Now
      </button>
    </form>
  </div>
</main>

<script>
document.getElementById('venue_id').addEventListener('change', function() {
    const venueId = this.value;
    const screenSelect = document.getElementById('screen_id');
    const options = screenSelect.querySelectorAll('option');
    
    screenSelect.value = '';
    updatePrice();
    
    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
        } else {
            const screenVenueId = option.getAttribute('data-venue');
            option.style.display = screenVenueId === venueId ? 'block' : 'none';
        }
    });
});

function updatePrice() {
    const screenSelect = document.getElementById('screen_id');
    const seatsSelect = document.getElementById('seats_count');
    const totalPriceElement = document.getElementById('totalPrice');
    const priceBreakdownElement = document.getElementById('priceBreakdown');
    
    const selectedOption = screenSelect.options[screenSelect.selectedIndex];
    const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
    const seats = parseInt(seatsSelect.value) || 1;
    const total = price * seats;
    
    totalPriceElement.textContent = '$' + total.toFixed(2);
    
    if (price > 0) {
        priceBreakdownElement.textContent = `$${price.toFixed(2)} × ${seats} seat${seats > 1 ? 's' : ''}`;
    } else {
        priceBreakdownElement.textContent = 'Select a screen to see pricing';
    }
}

document.getElementById('screen_id').addEventListener('change', updatePrice);
document.getElementById('seats_count').addEventListener('change', updatePrice);
</script>

<?php if (!defined('LOADED_VIA_INDEX')) { include dirname(__DIR__) . '/partials/footer.php'; } ?>
