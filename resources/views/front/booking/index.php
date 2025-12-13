<?php
View::partial('header');
?>

<main class="max-w-7xl mx-auto px-4 py-10">
  <h1 class="text-4xl font-bold text-center mb-12 text-purple-700">Book Your Tickets</h1>
  
  <?php if (isset($error)): ?>
    <div class="text-center text-red-600 bg-red-50 p-6 rounded-lg mb-8">
      <?= View::e($error) ?>
    </div>
  <?php endif; ?>
  
  <?php if (empty($movies)): ?>
    <div class="text-center text-gray-600 p-8">
      <p>No movies available for booking at this time.</p>
    </div>
  <?php else: ?>
    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($movies as $movie): ?>
        <div class="bg-white rounded-xl border shadow-sm overflow-hidden hover:shadow-md transition-shadow">
          <div class="aspect-[3/4] overflow-hidden">
            <img src="<?= getImagePath($movie['img']) ?>" 
                 alt="<?= View::e($movie['title']) ?>" 
                 class="w-full h-full object-cover">
          </div>
          <div class="p-6">
            <h2 class="text-xl font-bold mb-3"><?= View::e($movie['title']) ?></h2>
            
            <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
              <span>★ <?= number_format((float)$movie['rating'], 1) ?></span>
              <span><?= (int)$movie['duration_minutes'] ?> minutes</span>
            </div>
            
            <a href="<?= url('/booking?movie_id=' . (int)$movie['id']) ?>" 
               class="block w-full bg-purple-700 hover:bg-purple-800 text-white text-center py-3 rounded-md transition-colors">
              Select Seats
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>

<?php
View::partial('footer');
?>

