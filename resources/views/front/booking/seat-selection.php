<?php
View::partial('header');
$bookedSeats = $bookedSeats ?? [];
?>

<main class="max-w-7xl mx-auto px-4 py-10">
  <div class="mb-8">
    <a href="<?= url('/booking') ?>" class="text-purple-700 hover:text-purple-800 mb-4 inline-block">
      <i class="fas fa-arrow-left mr-2"></i>Back to Movies
    </a>
    
    <h1 class="text-4xl font-bold text-purple-700 mb-4"><?= View::e($movie['title']) ?></h1>
    <div class="flex items-center gap-4 text-gray-600">
      <span>★ <?= number_format((float)$movie['rating'], 1) ?></span>
      <span><?= (int)$movie['duration_minutes'] ?> minutes</span>
    </div>
  </div>
  
  <?php if (isset($error)): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">
      <i class="fas fa-exclamation-circle mr-2"></i>
      <?= View::e($error) ?>
    </div>
  <?php endif; ?>
  
  <form method="POST" action="<?= url('/booking') ?>" id="booking-form" class="space-y-8" onsubmit="return false;">
    <?= Csrf::field() ?>
    <input type="hidden" name="movie_id" value="<?= (int)$movie['id'] ?>">
    
    <!-- Venue Selection -->
    <div class="bg-white rounded-xl border shadow-sm p-6">
      <h2 class="text-2xl font-bold mb-4 text-purple-700">Select Venue</h2>
      <div class="grid gap-4 md:grid-cols-2">
        <?php foreach ($venues as $venue): ?>
          <label class="border-2 rounded-lg p-4 cursor-pointer hover:border-purple-500 transition-colors">
            <input type="radio" name="venue_id" value="<?= (int)$venue['id'] ?>" required class="mr-2" onchange="handleVenueChange()">
            <span class="font-semibold"><?= View::e($venue['name']) ?></span>
            <p class="text-sm text-gray-600 mt-1"><?= View::e($venue['address']) ?></p>
          </label>
        <?php endforeach; ?>
      </div>
    </div>
    
    <!-- Screen Selection -->
    <div class="bg-white rounded-xl border shadow-sm p-6" id="screen-selection" style="display: none;">
      <h2 class="text-2xl font-bold mb-4 text-purple-700">Select Screen</h2>
      <div id="screens-container" class="grid gap-4 md:grid-cols-3">
        <?php foreach ($venues as $venue): ?>
          <?php if (!empty($venue['screens'])): ?>
            <?php foreach ($venue['screens'] as $screen): ?>
              <label class="border-2 rounded-lg p-4 cursor-pointer hover:border-purple-500 transition-colors venue-<?= (int)$venue['id'] ?>" style="display: none;">
                <input type="radio" name="screen_id" value="<?= (int)$screen['id'] ?>" required class="mr-2" data-venue-id="<?= (int)$venue['id'] ?>" onchange="handleScreenChange()">
                <div>
                  <span class="font-semibold"><?= View::e($screen['screen_name']) ?></span>
                  <p class="text-sm text-gray-600 mt-1">
                    <?= View::e(ucfirst($screen['screen_type'])) ?> - 
                    $<?= number_format((float)$screen['base_price'], 2) ?> per seat
                  </p>
                  <p class="text-xs text-gray-500">Capacity: <?= (int)$screen['capacity'] ?> seats</p>
                </div>
              </label>
            <?php endforeach; ?>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
    
    <!-- Date Selection -->
    <div class="bg-white rounded-xl border shadow-sm p-6">
      <h2 class="text-2xl font-bold mb-4 text-purple-700">Select Date</h2>
      <input type="date" 
             name="show_date" 
             id="show_date"
             required
             min="<?= date('Y-m-d') ?>"
             max="<?= date('Y-m-d', strtotime('+30 days')) ?>"
             class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
             onchange="checkSeatSelectionVisibility()">
    </div>
    
    <!-- Time Selection -->
    <div class="bg-white rounded-xl border shadow-sm p-6">
      <h2 class="text-2xl font-bold mb-4 text-purple-700">Select Time</h2>
      <div class="grid gap-3 md:grid-cols-5">
        <?php foreach ($showtimes as $time): ?>
          <label class="border-2 rounded-lg p-3 text-center cursor-pointer hover:border-purple-500 transition-colors">
            <input type="radio" name="show_time" value="<?= View::e($time) ?>" required class="mr-2" onchange="checkSeatSelectionVisibility()">
            <span><?= View::e($time) ?></span>
          </label>
        <?php endforeach; ?>
      </div>
    </div>
    
    <!-- Visual Seat Selection -->
    <div class="bg-white rounded-xl border shadow-sm p-6" id="seat-selection-section" style="display: none;">
      <h2 class="text-2xl font-bold mb-4 text-purple-700">Select Seats (Max 5)</h2>
      
      <!-- Screen Display -->
      <div class="mb-6 text-center">
        <div class="bg-red-600 text-white py-4 rounded-t-lg font-bold text-lg">
          Screen is this way
        </div>
      </div>
      
      <!-- Seat Map -->
      <div class="overflow-x-auto mb-6 bg-gray-100 p-4 rounded-lg">
        <div id="seat-map" class="flex flex-col items-center gap-2 min-h-[400px]">
          <!-- Seats will be generated by JavaScript -->
          <p class="text-gray-500 text-sm">Select venue, screen, date, and time to view seats</p>
        </div>
      </div>
      
      <!-- Legend -->
      <div class="flex justify-center gap-6 mb-4 text-sm">
        <div class="flex items-center gap-2">
          <div class="w-6 h-6 bg-green-200 border border-green-400 rounded"></div>
          <span>Available</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-6 h-6 bg-purple-500 border border-purple-700 rounded"></div>
          <span>Selected</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-6 h-6 bg-red-300 border border-red-500 rounded"></div>
          <span>Booked</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-6 h-6 bg-gray-300 border border-gray-400 rounded"></div>
          <span>Unavailable</span>
        </div>
      </div>
      
      <!-- Selected Seats Display -->
      <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-4">
        <p class="font-semibold text-purple-700 mb-2">Selected Seats: <span id="selected-seats-count">0</span> / 5</p>
        <div id="selected-seats-list" class="flex flex-wrap gap-2">
          <span class="text-gray-500 text-sm">No seats selected</span>
        </div>
      </div>
      
      <!-- Hidden inputs for selected seats -->
      <div id="seat-inputs"></div>
    </div>
    
    <button type="button" 
            id="continue-btn"
            onclick="goToCheckout()"
            class="w-full bg-purple-700 hover:bg-purple-800 text-white font-semibold py-4 px-6 rounded-md transition-colors text-lg"
            disabled>
      Continue to Checkout
    </button>
  </form>
</main>

<script>
const totalRows = 8;
const leftSectionSeats = [3, 4, 5, 6, 6, 7, 7, 7];
const middleSectionSeats = 10;
const rightSectionSeats = [3, 4, 5, 6, 6, 7, 7, 7];
const aisleWidth = 2;

let selectedSeats = [];
let bookedSeats = <?= json_encode($bookedSeats) ?>;
let maxSeats = 5;

function generateSeatMap() {
  const seatMap = document.getElementById('seat-map');
  if (!seatMap) {
    console.error('Seat map element not found');
    return;
  }
  
  seatMap.innerHTML = '';
  
  const container = document.createElement('div');
  container.className = 'flex flex-col items-center gap-2';
  
  for (let row = 0; row < totalRows; row++) {
    const rowDiv = document.createElement('div');
    rowDiv.className = 'flex items-center gap-1';
    
    const rowLabel = document.createElement('div');
    rowLabel.className = 'w-8 text-sm font-semibold text-gray-700 text-right pr-2';
    rowLabel.textContent = getRowLabel(row);
    rowDiv.appendChild(rowLabel);
    
    const leftSeats = leftSectionSeats[row];
    for (let i = 1; i <= leftSeats; i++) {
      const seatLabel = getRowLabel(row) + 'L' + i;
      rowDiv.appendChild(createSeatButton(seatLabel, getRowLabel(row), 'L' + i));
    }
    
    const leftAisle = document.createElement('div');
    leftAisle.className = 'w-8';
    rowDiv.appendChild(leftAisle);
    
    for (let i = 1; i <= middleSectionSeats; i++) {
      const seatLabel = getRowLabel(row) + 'M' + i;
      rowDiv.appendChild(createSeatButton(seatLabel, getRowLabel(row), 'M' + i));
    }
    
    const rightAisle = document.createElement('div');
    rightAisle.className = 'w-8';
    rowDiv.appendChild(rightAisle);
    
    const rightSeats = rightSectionSeats[row];
    for (let i = 1; i <= rightSeats; i++) {
      const seatLabel = getRowLabel(row) + 'R' + i;
      rowDiv.appendChild(createSeatButton(seatLabel, getRowLabel(row), 'R' + i));
    }
    
    container.appendChild(rowDiv);
  }
  
  seatMap.appendChild(container);
  
  selectedSeats.forEach(seat => {
    const button = document.querySelector(`button[data-seat="${seat.seat}"]`);
    if (button && !bookedSeats[seat.seat]) {
      button.className = 'w-10 h-10 text-xs rounded border-2 bg-purple-500 border-purple-700 text-white cursor-pointer scale-110';
    }
  });
}

function createSeatButton(seatLabel, row, number) {
  const isBooked = bookedSeats[seatLabel] === true;
  const isSelected = selectedSeats.some(s => s.seat === seatLabel);
  
  const button = document.createElement('button');
  button.type = 'button';
  
  let buttonClass = 'w-10 h-10 text-xs rounded border-2 transition-all ';
  if (isBooked) {
    buttonClass += 'bg-red-300 border-red-500 cursor-not-allowed opacity-50';
  } else if (isSelected) {
    buttonClass += 'bg-purple-500 border-purple-700 text-white cursor-pointer scale-110';
  } else {
    buttonClass += 'bg-green-200 border-green-400 hover:bg-green-300 hover:scale-110 cursor-pointer';
  }
  
  button.className = buttonClass;
  button.textContent = number;
  button.dataset.seat = seatLabel;
  button.dataset.row = row;
  button.dataset.number = number;
  
  if (!isBooked) {
    button.onclick = () => toggleSeat(seatLabel, row, number, button);
  }
  
  return button;
}

function getRowLabel(index) {
  if (index < 26) {
    return String.fromCharCode(65 + index);
  } else {
    const first = String.fromCharCode(65 + Math.floor((index - 26) / 26));
    const second = String.fromCharCode(65 + ((index - 26) % 26));
    return first + second;
  }
}

function toggleSeat(seatLabel, row, number, button) {
  const index = selectedSeats.findIndex(s => s.seat === seatLabel);
  
  if (index > -1) {
    selectedSeats.splice(index, 1);
    button.className = 'w-10 h-10 text-xs rounded border-2 bg-green-200 border-green-400 hover:bg-green-300 hover:scale-110 cursor-pointer';
    updateSelectedSeats();
  } else {
    if (selectedSeats.length >= maxSeats) {
      alert('Maximum ' + maxSeats + ' seats per booking');
      return;
    }
    
    selectedSeats.push({ seat: seatLabel, row: row, number: number });
    button.className = 'w-10 h-10 text-xs rounded border-2 bg-purple-500 border-purple-700 text-white cursor-pointer scale-110';
    updateSelectedSeats();
  }
}

function updateSelectedSeats() {
  const count = selectedSeats.length;
  document.getElementById('selected-seats-count').textContent = count;
  
  const list = document.getElementById('selected-seats-list');
  
  if (count === 0) {
    list.innerHTML = '<span class="text-gray-500 text-sm">No seats selected</span>';
    document.getElementById('continue-btn').disabled = true;
  } else {
    list.innerHTML = selectedSeats.map(s => 
      `<span class="bg-purple-200 text-purple-800 px-2 py-1 rounded text-sm">${s.seat}</span>`
    ).join('');
    
    document.getElementById('continue-btn').disabled = false;
  }
}

function handleVenueChange() {
  const venueId = document.querySelector('input[name="venue_id"]:checked')?.value;
  if (!venueId) return;
  
  document.querySelectorAll('.venue-' + venueId).forEach(el => {
    el.style.display = 'block';
  });
  
  document.querySelectorAll('[class*="venue-"]').forEach(el => {
    if (!el.classList.contains('venue-' + venueId)) {
      el.style.display = 'none';
    }
  });
  
  document.getElementById('screen-selection').style.display = 'block';
  checkSeatSelectionVisibility();
}

function handleScreenChange() {
  checkSeatSelectionVisibility();
}

function checkSeatSelectionVisibility() {
  const hasVenue = document.querySelector('input[name="venue_id"]:checked');
  const hasScreen = document.querySelector('input[name="screen_id"]:checked');
  const hasDate = document.getElementById('show_date').value;
  const hasTime = document.querySelector('input[name="show_time"]:checked');
  
  if (hasVenue && hasScreen && hasDate && hasTime) {
    document.getElementById('seat-selection-section').style.display = 'block';
    generateSeatMap();
  } else {
    document.getElementById('seat-selection-section').style.display = 'none';
  }
}

function goToCheckout() {
  if (selectedSeats.length === 0) {
    alert('Please select at least one seat');
    return;
  }
  
  const form = document.getElementById('booking-form');
  const formData = new FormData(form);
  
  const currentUrl = window.location.href;
  const urlObj = new URL(currentUrl);
  
  const pathname = urlObj.pathname;
  let baseUrl = urlObj.origin;
  
  if (pathname.includes('/public/index.php')) {
    const indexPos = pathname.indexOf('/public/index.php');
    baseUrl += pathname.substring(0, indexPos + '/public/index.php'.length);
  } else {
    const match = pathname.match(/^(\/[^\/]+)/);
    if (match) {
      baseUrl += match[1] + '/public/index.php';
    } else {
      baseUrl += '/Cinema/public/index.php';
    }
  }
  
  let checkoutUrl = baseUrl + '?route=/booking/checkout';
  checkoutUrl += '&movie_id=' + encodeURIComponent(formData.get('movie_id') || '');
  checkoutUrl += '&venue_id=' + encodeURIComponent(formData.get('venue_id') || '');
  checkoutUrl += '&screen_id=' + encodeURIComponent(formData.get('screen_id') || '');
  checkoutUrl += '&show_date=' + encodeURIComponent(formData.get('show_date') || '');
  checkoutUrl += '&show_time=' + encodeURIComponent(formData.get('show_time') || '');
  
  const seats = selectedSeats.map(s => s.seat).join(',');
  checkoutUrl += '&seats=' + encodeURIComponent(seats);
  
  window.location.href = checkoutUrl;
}

document.addEventListener('DOMContentLoaded', function() {
  const hasVenue = document.querySelector('input[name="venue_id"]:checked');
  const hasScreen = document.querySelector('input[name="screen_id"]:checked');
  const hasDate = document.getElementById('show_date').value;
  const hasTime = document.querySelector('input[name="show_time"]:checked');
  
  if (hasVenue && hasScreen && hasDate && hasTime) {
    checkSeatSelectionVisibility();
  }
  
  const dateInput = document.getElementById('show_date');
  if (dateInput) {
    dateInput.addEventListener('change', checkSeatSelectionVisibility);
  }
  
  document.querySelectorAll('input[name="show_time"]').forEach(radio => {
    radio.addEventListener('change', checkSeatSelectionVisibility);
  });
  
  document.querySelectorAll('input[name="venue_id"]').forEach(radio => {
    radio.addEventListener('change', handleVenueChange);
  });
  
  document.querySelectorAll('input[name="screen_id"]').forEach(radio => {
    radio.addEventListener('change', handleScreenChange);
  });
});
</script>

<?php
View::partial('footer');
?>
