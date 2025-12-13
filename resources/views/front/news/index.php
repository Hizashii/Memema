<?php
View::partial('header');
?>

<main class="max-w-6xl mx-auto px-4 py-10 space-y-12">
  <h1 class="text-3xl md:text-4xl font-extrabold text-center text-purple-700">
    Latest Cinematic News & Updates
  </h1>

  <?php if (isset($error)): ?>
    <div class="text-center text-red-600 bg-red-50 p-6 rounded-lg">
      <?= View::e($error) ?>
    </div>
  <?php else: ?>
    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($news as $article): ?>
        <article class="bg-white rounded-xl border shadow-sm overflow-hidden hover:shadow-md transition-shadow">
          <div class="aspect-[16/10] overflow-hidden">
            <img src="<?= getImagePath($article['img']) ?>" 
                 alt="<?= View::e($article['title']) ?>" 
                 class="w-full h-full object-cover">
          </div>
          <div class="p-6">
            <div class="text-sm text-gray-500 mb-2">
              <?= date('M j, Y', strtotime($article['created_at'])) ?>
            </div>
            <h3 class="font-semibold text-lg mb-3"><?= View::e($article['title']) ?></h3>
            <p class="text-sm text-gray-700"><?= View::e($article['excerpt']) ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>

<?php
View::partial('footer');
?>

