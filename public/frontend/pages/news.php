<?php
require_once __DIR__ . '/../../../app/classes/autoload.php';
require_once __DIR__ . '/../../../app/core/database.php';

// Include header only when accessed directly (not via index.php)
if (!defined('LOADED_VIA_INDEX')) {
    include dirname(__DIR__) . '/partials/header.php';
}

try {
    $news = News::getAll();
} catch (Exception $e) {
    $news = [];
    $error = "Unable to load news. Please try again later.";
}
?>

<main class="max-w-6xl mx-auto px-4 py-10 space-y-12">
  <h1 class="text-3xl md:text-4xl font-extrabold text-center text-purple-700">
    Latest Cinematic News & Updates
  </h1>

  <?php if (isset($error)): ?>
    <div class="text-center text-red-600 bg-red-50 p-6 rounded-lg">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php else: ?>
    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($news as $article): ?>
        <article class="bg-white rounded-xl border shadow-sm overflow-hidden hover:shadow-md transition-shadow">
          <div class="aspect-[16/10] overflow-hidden">
            <img src="<?= getImagePath($article['img']) ?>" 
                 alt="<?= htmlspecialchars($article['title']) ?>" 
                 class="w-full h-full object-cover">
          </div>
          <div class="p-6">
            <div class="text-sm text-gray-500 mb-2">
              <?= date('M j, Y', strtotime($article['created_at'])) ?>
            </div>
            <h3 class="font-semibold text-lg mb-3"><?= htmlspecialchars($article['title']) ?></h3>
            <p class="text-sm text-gray-700"><?= htmlspecialchars($article['excerpt']) ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>

<?php if (!defined('LOADED_VIA_INDEX')) { include dirname(__DIR__) . '/partials/footer.php'; } ?>
