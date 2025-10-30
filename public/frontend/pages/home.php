<?php
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/core/database.php';

try {
    $news = executeQuery("SELECT title, excerpt, img FROM news ORDER BY created_at DESC LIMIT 3");
    $movies = executeQuery("SELECT id, title, img, rating, duration_minutes FROM movies ORDER BY id ASC LIMIT 8");
} catch (Exception $e) {
    $news = [];
    $movies = [];
    $error = "Unable to load content. Please try again later.";
}
?>

<main class="max-w-7xl mx-auto px-4 py-10 space-y-16">
  <section class="text-center">
    <h1 class="text-4xl md:text-6xl font-extrabold text-purple-700 mb-6">
      Welcome to CinemaBook
    </h1>
    <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
      Discover amazing movies, book your seats, and enjoy the ultimate cinema experience
    </p>
    <a href="/Cinema/public/frontend/pages/movies.php" 
       class="inline-block bg-purple-700 hover:bg-purple-800 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors">
      Explore Movies Now
    </a>
  </section>

  <section>
    <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Latest News</h2>
    <?php if (isset($error)): ?>
      <div class="text-center text-red-600 bg-red-50 p-4 rounded-lg">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php else: ?>
      <div class="grid gap-8 md:grid-cols-3">
        <?php foreach ($news as $n): ?>
          <article class="bg-white rounded-xl border shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            <div class="aspect-[16/10] overflow-hidden">
              <img src="<?= getImagePath($n['img']) ?>" 
                   alt="<?= htmlspecialchars($n['title']) ?>" 
                   class="w-full h-full object-cover">
            </div>
            <div class="p-6">
              <h3 class="font-semibold text-lg mb-2"><?= htmlspecialchars($n['title']) ?></h3>
              <p class="text-gray-700 text-sm"><?= htmlspecialchars($n['excerpt']) ?></p>
              <a href="/Cinema/public/frontend/pages/news.php" 
                 class="inline-block mt-4 text-purple-700 hover:text-purple-800 font-medium">
                Read more →
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <section>
    <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Now Showing</h2>
    <?php if (isset($error)): ?>
      <div class="text-center text-red-600 bg-red-50 p-4 rounded-lg">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php else: ?>
      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        <?php foreach ($movies as $m): ?>
          <div class="bg-white rounded-xl border shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            <div class="aspect-[3/4] overflow-hidden">
              <img src="<?= getImagePath($m['img']) ?>" 
                   alt="<?= htmlspecialchars($m['title']) ?>" 
                   class="w-full h-full object-cover">
            </div>
            <div class="p-4">
              <h3 class="font-semibold mb-2"><?= htmlspecialchars($m['title']) ?></h3>
              <div class="flex items-center justify-between text-sm text-gray-600">
                <span> <?= number_format((float)$m['rating'], 1) ?></span>
                <span><?= (int)$m['duration_minutes'] ?> min</span>
              </div>
              <a href="/Cinema/public/frontend/pages/booking.php?movie_id=<?= (int)$m['id'] ?>" 
                 class="block w-full mt-3 bg-purple-700 hover:bg-purple-800 text-white text-center py-2 rounded-md transition-colors">
                Book Now
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</main>