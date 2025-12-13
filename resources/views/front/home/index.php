<?php
View::partial('header');
?>

<main class="max-w-7xl mx-auto px-4 py-10 space-y-16">
  <section class="text-center">
    <h1 class="text-4xl md:text-6xl font-extrabold text-purple-700 mb-6">
      <?= View::e($companyPresentation['title'] ?? 'Welcome to CinemaBook') ?>
    </h1>
    <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
      <?= View::e($companyPresentation['description'] ?? 'Discover amazing movies, book your seats, and enjoy the ultimate cinema experience') ?>
    </p>
    <a href="<?= url('/movies') ?>" 
       class="inline-block bg-purple-700 hover:bg-purple-800 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors">
      Explore Movies Now
    </a>
  </section>

  <?php if (isset($companyPresentation['features']) && !empty($companyPresentation['features'])): ?>
    <section class="bg-purple-50 rounded-xl p-8">
      <h2 class="text-2xl font-bold text-center mb-8 text-purple-700">Why Choose CinemaBook?</h2>
      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        <?php foreach ($companyPresentation['features'] as $feature): ?>
          <div class="text-center">
            <div class="bg-purple-700 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
              <i class="fas fa-check text-2xl"></i>
            </div>
            <p class="text-gray-700 font-medium"><?= View::e($feature) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>
  
  <?php if (!empty($companyPresentation['opening_hours'])): ?>
    <section class="bg-white rounded-xl border shadow-sm p-8 text-center">
      <h2 class="text-2xl font-bold mb-4 text-purple-700">Opening Hours</h2>
      <div class="text-gray-700 whitespace-pre-line"><?= View::e($companyPresentation['opening_hours']) ?></div>
    </section>
  <?php endif; ?>

  <section>
    <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Latest News</h2>
    <?php if (isset($error)): ?>
      <div class="text-center text-red-600 bg-red-50 p-4 rounded-lg">
        <?= View::e($error) ?>
      </div>
    <?php else: ?>
      <div class="grid gap-8 md:grid-cols-3">
        <?php foreach ($news as $n): ?>
          <article class="bg-white rounded-xl border shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            <div class="aspect-[16/10] overflow-hidden">
              <img src="<?= getImagePath($n['img']) ?>" 
                   alt="<?= View::e($n['title']) ?>" 
                   class="w-full h-full object-cover">
            </div>
            <div class="p-6">
              <h3 class="font-semibold text-lg mb-2"><?= View::e($n['title']) ?></h3>
              <p class="text-gray-700 text-sm"><?= View::e($n['excerpt']) ?></p>
              <a href="<?= url('/news') ?>" 
                 class="inline-block mt-4 text-purple-700 hover:text-purple-800 font-medium">
                Read more →
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <?php if (!empty($shows)): ?>
    <section>
      <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Daily Showings</h2>
      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($shows as $show): ?>
          <div class="bg-white rounded-xl border shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            <div class="aspect-[16/10] overflow-hidden relative">
              <img src="<?= getImagePath($show['img']) ?>" 
                   alt="<?= View::e($show['title']) ?>" 
                   class="w-full h-full object-cover">
              <div class="absolute top-2 right-2 px-3 py-1 rounded-full text-white text-sm font-semibold" style="background-color: <?= View::e($show['tag_color']) ?>">
                <?= View::e($show['tag_text']) ?>
              </div>
            </div>
            <div class="p-4">
              <h3 class="font-semibold text-lg mb-2"><?= View::e($show['title']) ?></h3>
              <?php if (!empty($show['showtimes'])): ?>
                <div class="flex flex-wrap gap-2 mb-3">
                  <?php foreach ($show['showtimes'] as $time): ?>
                    <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded">
                      <?= View::e($time) ?>
                    </span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <section>
    <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Now Showing</h2>
    <?php if (!isset($error)): ?>
      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        <?php foreach ($movies as $m): ?>
          <div class="bg-white rounded-xl border shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            <div class="aspect-[3/4] overflow-hidden">
              <img src="<?= getImagePath($m['img']) ?>" 
                   alt="<?= View::e($m['title']) ?>" 
                   class="w-full h-full object-cover">
            </div>
            <div class="p-4">
              <h3 class="font-semibold mb-2"><?= View::e($m['title']) ?></h3>
              <div class="flex items-center justify-between text-sm text-gray-600">
                <span>★ <?= number_format((float)$m['rating'], 1) ?></span>
                <span><?= (int)$m['duration_minutes'] ?> min</span>
              </div>
              <a href="<?= url('/booking?movie_id=' . (int)$m['id']) ?>" 
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

<?php
View::partial('footer');
?>

