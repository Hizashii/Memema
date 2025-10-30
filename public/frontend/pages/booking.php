<?php include dirname(__DIR__) . '/partials/header.php'; ?>

<?php
require_once __DIR__ . '/../../../app/auth/user_auth.php';
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/security.php';
require_once __DIR__ . '/../../../app/core/database.php';

requireUserLogin();

$movie_id = validateInt($_GET['movie_id'] ?? 0, 1);
$selected_venue = validateInt($_GET['venue_id'] ?? 0, 1);
$selected_screen = validateInt($_GET['screen_id'] ?? 0, 1);
$seats = validateInt($_GET['seats'] ?? 1, 1, 6);

$movie = null;
$venues = [];
$screens = [];

if ($movie_id) {
    try {
        $movie = getMovie($movie_id)[0] ?? null;
        
        if ($movie) {
            $venues = executeQuery("SELECT id, name, address FROM venues ORDER BY name");
            
            if ($selected_venue) {
                $screens = executePreparedQuery(
                    "SELECT id, screen_name, screen_type, base_price, capacity FROM screens WHERE venue_id = ? ORDER BY screen_name", 
                    [$selected_venue], 
                    'i'
                );
            }
        }
    } catch (Exception $e) {
        $error = "Unable to load booking information. Please try again.";
    }
}
?>

<main class="max-w-6xl mx-auto px-4 py-10 space-y-8">
  <?php if ($movie): ?>
    <div class="bg-white rounded-xl border shadow-sm p-6">
      <div class="flex gap-6">
        <div class="w-32 h-48 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
          <img src="<?= getImagePath($movie['img']) ?>" 
               alt="<?= htmlspecialchars($movie['title']) ?>" 
               class="w-full h-full object-cover">
        </div>
        <div class="flex-1">
          <h1 class="text-2xl font-bold mb-2"><?= htmlspecialchars($movie['title']) ?></h1>
          <div class="flex items-center gap-4 text-sm text-gray-600">
            <span>Duration: <?= (int)$movie['duration_minutes'] ?> min</span>
            <span>Rating: <?= number_format((float)$movie['rating'], 1) ?></span>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl border shadow-sm p-6">
      <h2 class="text-xl font-bold mb-4">Select Venue</h2>
      <div class="grid gap-4 md:grid-cols-2">
        <?php foreach ($venues as $venue): ?>
          <a href="/Cinema/public/frontend/pages/booking.php?movie_id=<?= $movie_id ?>&venue_id=<?= (int)$venue['id'] ?>" 
             class="block p-4 border rounded-lg hover:bg-purple-50 hover:border-purple-300 <?= $selected_venue == $venue['id'] ? 'bg-purple-50 border-purple-300' : '' ?>">
            <div class="font-semibold"><?= htmlspecialchars($venue['name']) ?></div>
            <div class="text-sm text-gray-600"><?= htmlspecialchars($venue['address']) ?></div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

    <?php if ($selected_venue && !empty($screens)): ?>
      <div class="bg-white rounded-xl border shadow-sm p-6">
        <h2 class="text-xl font-bold mb-4">Select Screen</h2>
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          <?php foreach ($screens as $screen): ?>
            <a href="/Cinema/public/frontend/pages/booking.php?movie_id=<?= $movie_id ?>&venue_id=<?= $selected_venue ?>&screen_id=<?= (int)$screen['id'] ?>&seats=<?= $seats ?>" 
               class="block p-4 border rounded-lg hover:bg-purple-50 hover:border-purple-300 <?= $selected_screen == $screen['id'] ? 'bg-purple-50 border-purple-300' : '' ?>">
              <div class="font-semibold"><?= htmlspecialchars($screen['screen_name']) ?></div>
              <div class="text-sm text-gray-600 capitalize"><?= htmlspecialchars($screen['screen_type']) ?></div>
              <div class="text-sm font-medium text-purple-700">$<?= number_format((float)$screen['base_price'], 2) ?></div>
              <div class="text-xs text-gray-500">Capacity: <?= (int)$screen['capacity'] ?> seats</div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($selected_venue && $selected_screen): ?>
          <?php for ($seat_count = 1; $seat_count <= 5; $seat_count++): ?>
          <?php endfor; ?>
        </div>
      </div>

      <div class="bg-white rounded-xl border shadow-sm p-6">
        <h2 class="text-xl font-bold mb-4">Select Show Time</h2>
        <div class="grid gap-4 md:grid-cols-3">
          <?php
          $show_times = ['10:00', '13:30', '16:00', '19:00', '22:30'];
          foreach ($show_times as $time):
          ?>
            <a href="/Cinema/public/frontend/pages/seat-selection.php?movie_id=<?= $movie_id ?>&venue_id=<?= $selected_venue ?>&screen_id=<?= $selected_screen ?>&date=<?= date('Y-m-d') ?>&time=<?= $time ?>" 
               class="block p-4 border rounded-lg hover:bg-purple-50 hover:border-purple-300 text-center">
              <div class="text-lg font-semibold"><?= date('g:i A', strtotime($time)) ?></div>
              <div class="text-sm text-gray-600"><?= date('M j', strtotime('today')) ?></div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

  <?php else: ?>
    <div class="text-center py-12">
      <h1 class="text-2xl font-bold mb-4">Movie Not Found</h1>
      <p class="text-gray-600 mb-6">The movie you're looking for doesn't exist or has been removed.</p>
      <a href="/Cinema/public/frontend/pages/movies.php" 
         class="inline-block bg-purple-700 hover:bg-purple-800 text-white px-6 py-3 rounded-md">
        Browse Movies
      </a>
    </div>
  <?php endif; ?>
</main>

<?php include dirname(__DIR__) . '/partials/footer.php'; ?>