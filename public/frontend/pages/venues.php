<?php
require_once __DIR__ . '/../../../app/classes/autoload.php';
require_once __DIR__ . '/../../../app/core/database.php';

// Include header only when accessed directly (not via index.php)
if (!defined('LOADED_VIA_INDEX')) {
    include dirname(__DIR__) . '/partials/header.php';
}

try {
    $venues = Venue::getAll();
} catch (Exception $e) {
    $venues = [];
    $error = "Unable to load venues. Please try again later.";
}
?>

<main class="max-w-7xl mx-auto px-4 py-10 space-y-12">
  <section class="text-center">
    <h1 class="text-4xl font-extrabold text-purple-700 mb-6">Our Cinemas</h1>
    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
      Discover our premium cinema locations across the city, each offering a unique movie-going experience
    </p>
  </section>

  <?php if (isset($error)): ?>
    <div class="text-center text-red-600 bg-red-50 p-6 rounded-lg">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php else: ?>
    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($venues as $venue): ?>
        <div class="bg-white rounded-xl border shadow-sm overflow-hidden hover:shadow-md transition-shadow">
          <div class="aspect-[16/10] overflow-hidden">
            <img src="<?= getImagePath($venue['image']) ?>" 
                 alt="<?= htmlspecialchars($venue['name']) ?>" 
                 class="w-full h-full object-cover">
          </div>
          <div class="p-6">
            <h3 class="text-2xl font-bold mb-4"><?= htmlspecialchars($venue['name']) ?></h3>
            <div class="space-y-3">
              <div class="flex items-start gap-3">
                <i class="fas fa-map-marker-alt text-purple-700 mt-1"></i>
                <span class="text-gray-600"><?= htmlspecialchars($venue['address']) ?></span>
              </div>
              <div class="flex items-center gap-3">
                <i class="fas fa-phone text-purple-700"></i>
                <a href="tel:<?= htmlspecialchars($venue['phone']) ?>" 
                   class="text-gray-600 hover:text-purple-700">
                  <?= htmlspecialchars($venue['phone']) ?>
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>

<?php if (!defined('LOADED_VIA_INDEX')) { include dirname(__DIR__) . '/partials/footer.php'; } ?>
