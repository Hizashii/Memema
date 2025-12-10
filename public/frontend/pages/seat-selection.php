<?php
require_once __DIR__ . '/../../../app/classes/autoload.php';
require_once __DIR__ . '/../../../app/core/database.php';
require_once __DIR__ . '/../../../app/config/security.php';

// Include header only when accessed directly (not via index.php)
if (!defined('LOADED_VIA_INDEX')) {
    include dirname(__DIR__) . '/partials/header.php';
}

$movie_id = validateInt($_GET['movie_id'] ?? 0, 1);
$venue_id = validateInt($_GET['venue_id'] ?? 0, 1);
$screen_id = validateInt($_GET['screen_id'] ?? 0, 1);
$show_date = sanitizeInput($_GET['date'] ?? date('Y-m-d'));
$show_time = sanitizeInput($_GET['time'] ?? '19:00');

$movie = null;
$venue = null;
$screen = null;
$occupied_seats = [];

if ($movie_id && $venue_id && $screen_id) {
    try {
        $movie = Movie::getById($movie_id);
        $venue = Venue::getById($venue_id);
        $screen = Screen::getById($screen_id);
        
        if ($movie && $venue && $screen) {
            $booked_seats = Booking::getBookedSeats($screen_id, $show_date, $show_time);
            foreach ($booked_seats as $seat) {
                $occupied_seats[$seat['seat_row'] . $seat['seat_number']] = true;
            }
        }
    } catch (Exception $e) {
        $error = "Unable to load seat information. Please try again.";
    }
}

$rows = ['J', 'I', 'H', 'G', 'F', 'E', 'D', 'C', 'B', 'A'];
$seats_per_row = 16;
?>

<main class="max-w-6xl mx-auto px-4 py-10 space-y-8">
  <?php if ($movie && $venue && $screen): ?>
    <div class="bg-white rounded-xl border shadow-sm p-6">
      <div class="flex gap-6">
        <div class="w-32 h-48 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
          <img src="<?= getImagePath($movie['img']) ?>" 
               alt="<?= htmlspecialchars($movie['title']) ?>" 
               class="w-full h-full object-cover">
        </div>
        <div class="flex-1">
          <h1 class="text-2xl font-bold mb-2"><?= htmlspecialchars($movie['title']) ?></h1>
          <div class="space-y-1 text-gray-600">
            <div><strong>Venue:</strong> <?= htmlspecialchars($venue['name']) ?></div>
            <div><strong>Screen:</strong> <?= htmlspecialchars($screen['screen_name']) ?> (<?= htmlspecialchars($screen['screen_type']) ?>)</div>
            <div><strong>Date:</strong> <?= date('M j, Y', strtotime($show_date)) ?></div>
            <div><strong>Time:</strong> <?= date('g:i A', strtotime($show_time)) ?></div>
            <div><strong>Price per seat:</strong> $<?= number_format((float)$screen['base_price'], 2) ?></div>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl border shadow-sm p-6">
      <h2 class="text-2xl font-bold mb-6 text-center">Choose Your Seats</h2>
      
      <div class="text-center mb-8">
        <div class="inline-block bg-gray-800 text-white px-8 py-2 rounded-lg text-sm font-semibold">
          SCREEN
        </div>
      </div>

      <div class="flex justify-center gap-6 mb-8 text-sm">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-white border-2 border-gray-300 rounded-lg"></div>
          <span>Available</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-purple-600 border-2 border-purple-700 rounded-lg"></div>
          <span>Selected</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-gray-300 border-2 border-gray-400 rounded-lg"></div>
          <span>Occupied</span>
        </div>
      </div>

      <div class="max-w-4xl mx-auto">
        <div class="grid grid-cols-17 gap-1 mb-4">
          <div class="col-span-1"></div>
          <?php for ($i = 1; $i <= $seats_per_row; $i++): ?>
            <div class="text-center text-sm font-bold text-gray-700 py-2"><?= $i ?></div>
          <?php endfor; ?>
        </div>

        <?php foreach ($rows as $row): ?>
          <div class="grid grid-cols-17 gap-1 mb-2">
            <div class="text-center text-lg font-bold text-gray-700 flex items-center justify-center py-3"><?= $row ?></div>
            
            <?php for ($seat_num = 1; $seat_num <= $seats_per_row; $seat_num++): ?>
              <?php 
              $seat_id = $row . $seat_num;
              $is_occupied = isset($occupied_seats[$seat_id]);
              $is_wheelchair = ($row === 'J' && $seat_num === 1) || ($row === 'I' && $seat_num === 16);
              ?>
              <button 
                class="w-12 h-12 rounded-lg text-sm font-bold transition-all duration-200 border-2 <?php
                  if ($is_occupied) {
                    echo 'bg-gray-300 border-gray-400 cursor-not-allowed text-gray-500';
                  } elseif ($is_wheelchair) {
                    echo 'bg-blue-50 border-blue-400 text-blue-600 hover:bg-blue-100';
                  } else {
                    echo 'bg-white border-gray-300 text-gray-800 hover:bg-purple-50 hover:border-purple-500';
                  }
                ?>"
                data-seat="<?= $seat_id ?>"
                data-price="<?= $screen['base_price'] ?>"
                <?= $is_occupied ? 'disabled' : '' ?>
                onclick="toggleSeat('<?= $seat_id ?>')"
              >
                <?php if ($is_wheelchair): ?>
                  <i class="fas fa-wheelchair"></i>
                <?php else: ?>
                  <?= $seat_num ?>
                <?php endif; ?>
              </button>
            <?php endfor; ?>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="mt-8 text-center">
        <div class="inline-block bg-gray-100 rounded-lg p-4">
          <div class="text-sm text-gray-600 mb-2">Selected Seats:</div>
          <div id="selected-seats-display" class="font-bold text-purple-700 text-lg mb-3">
            None selected
          </div>
          <div class="text-lg font-bold text-gray-800">
            Total: <span id="total-price">$0.00</span>
          </div>
        </div>
      </div>

      <div id="error-message" class="hidden mt-4 text-center">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
          <strong>Error:</strong> <span id="error-text"></span>
        </div>
      </div>

      <div class="mt-8 flex justify-center gap-4">
        <a href="/Cinema/public/frontend/pages/booking.php?movie_id=<?= $movie_id ?>&venue_id=<?= $venue_id ?>&screen_id=<?= $screen_id ?>" 
           class="px-6 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
          Back to Screen Selection
        </a>
        <button id="proceed-btn" 
                class="px-6 py-2 bg-purple-700 text-white rounded-md hover:bg-purple-800 disabled:bg-gray-400 disabled:cursor-not-allowed"
                disabled>
          Proceed to Checkout
        </button>
      </div>
    </div>

  <?php else: ?>
    <div class="text-center py-12">
      <h1 class="text-2xl font-bold mb-4">Invalid Selection</h1>
      <p class="text-gray-600 mb-6">Please select a movie, venue, and screen first.</p>
      <a href="/Cinema/public/frontend/pages/movies.php" 
         class="inline-block bg-purple-700 hover:bg-purple-800 text-white px-6 py-3 rounded-md">
        Browse Movies
      </a>
    </div>
  <?php endif; ?>
</main>

<script>
let selectedSeats = [];
const maxSeats = 5;
let pricePerSeat = <?= $screen ? (float)$screen['base_price'] : 0 ?>;

function toggleSeat(seatId) {
  const seatBtn = document.querySelector(`[data-seat="${seatId}"]`);
  if (!seatBtn || seatBtn.disabled) return;

  if (selectedSeats.includes(seatId)) {
    selectedSeats = selectedSeats.filter(seat => seat !== seatId);
    seatBtn.classList.remove('bg-purple-600', 'border-purple-700', 'text-white');
    seatBtn.classList.add('bg-white', 'border-gray-300', 'text-gray-800');
  } else {
    if (selectedSeats.length >= maxSeats) {
      showError(`You can only select up to ${maxSeats} seats`);
      return;
    }
    selectedSeats.push(seatId);
    seatBtn.classList.add('bg-purple-600', 'border-purple-700', 'text-white');
    seatBtn.classList.remove('bg-white', 'border-gray-300', 'text-gray-800');
  }
  
  updateSummary();
  hideError();
}

function updateSummary() {
  const display = document.getElementById('selected-seats-display');
  const totalPrice = document.getElementById('total-price');
  const proceedBtn = document.getElementById('proceed-btn');
  
  if (selectedSeats.length === 0) {
    display.textContent = 'None selected';
    totalPrice.textContent = '$0.00';
    proceedBtn.disabled = true;
  } else {
    display.textContent = selectedSeats.sort().join(', ');
    totalPrice.textContent = '$' + (selectedSeats.length * pricePerSeat).toFixed(2);
    proceedBtn.disabled = false;
  }
}

function showError(message) {
  const errorDiv = document.getElementById('error-message');
  const errorText = document.getElementById('error-text');
  errorText.textContent = message;
  errorDiv.classList.remove('hidden');
}

function hideError() {
  document.getElementById('error-message').classList.add('hidden');
}

document.getElementById('proceed-btn').addEventListener('click', function() {
  if (selectedSeats.length === 0) {
    showError('Please select at least one seat');
    return;
  }
  
  const params = new URLSearchParams(window.location.search);
  params.set('seats', selectedSeats.join(','));
  window.location.href = '/Cinema/public/frontend/pages/checkout.php?' + params.toString();
});
</script>

<style>
.grid-cols-17 {
  grid-template-columns: 3rem repeat(16, 3rem);
}
</style>

<?php if (!defined('LOADED_VIA_INDEX')) { include dirname(__DIR__) . '/partials/footer.php'; } ?>
