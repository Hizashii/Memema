<?php include dirname(__DIR__) . '/partials/header.php'; ?>

<?php
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/core/database.php';

try {
    $movies = executeQuery("SELECT m.id, m.title, m.img, m.duration_minutes, m.rating FROM movies m ORDER BY m.id ASC");
    $genres = executeQuery("SELECT movie_id, genre FROM movie_genres ORDER BY genre");
    $movieGenres = [];
    foreach ($genres as $g) {
        $movieGenres[$g['movie_id']][] = $g['genre'];
    }
} catch (Exception $e) {
    $movies = [];
    $movieGenres = [];
    $error = "Unable to load movies. Please try again later.";
}
?>

<main class="max-w-7xl mx-auto px-4 py-10">
  <h1 class="text-4xl font-bold text-center mb-12 text-purple-700">All Movies</h1>
  
  <?php if (isset($error)): ?>
    <div class="text-center text-red-600 bg-red-50 p-6 rounded-lg">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php else: ?>
    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($movies as $movie): ?>
        <div class="bg-white rounded-xl border shadow-sm overflow-hidden hover:shadow-md transition-shadow">
          <div class="aspect-[3/4] overflow-hidden">
            <img src="<?= getImagePath($movie['img']) ?>" 
                 alt="<?= htmlspecialchars($movie['title']) ?>" 
                 class="w-full h-full object-cover">
          </div>
          <div class="p-6">
            <h2 class="text-xl font-bold mb-3"><?= htmlspecialchars($movie['title']) ?></h2>
            
            <div class="flex flex-wrap gap-2 mb-4">
              <?php if (isset($movieGenres[$movie['id']])): ?>
                <?php foreach ($movieGenres[$movie['id']] as $genre): ?>
                  <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full">
                    <?= htmlspecialchars($genre) ?>
                  </span>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
            
            <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
              <span><?= number_format((float)$movie['rating'], 1) ?></span>
              <span><?= (int)$movie['duration_minutes'] ?> minutes</span>
            </div>
            
            <a href="/Cinema/public/frontend/pages/booking.php?movie_id=<?= (int)$movie['id'] ?>" 
               class="block w-full bg-purple-700 hover:bg-purple-800 text-white text-center py-3 rounded-md transition-colors">
              Book Tickets
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>

<?php include dirname(__DIR__) . '/partials/footer.php'; ?>