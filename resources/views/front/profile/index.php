<?php
View::partial('header');
?>

<main class="max-w-7xl mx-auto px-4 py-10">
  <h1 class="text-4xl font-bold text-center mb-12 text-purple-700">My Profile</h1>
  
  <?php if (isset($error)): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">
      <i class="fas fa-exclamation-circle mr-2"></i>
      <?= View::e($error) ?>
    </div>
  <?php endif; ?>
  
  <div class="grid md:grid-cols-2 gap-8">
    <div class="bg-white rounded-xl border shadow-sm p-8">
      <h2 class="text-2xl font-bold mb-6 text-purple-700">Account Information</h2>
      
      <div class="space-y-4">
        <div>
          <p class="text-sm text-gray-600">Full Name</p>
          <p class="text-lg font-semibold text-gray-900"><?= View::e($user['name'] ?? $user['full_name'] ?? 'N/A') ?></p>
        </div>
        
        <div>
          <p class="text-sm text-gray-600">Email</p>
          <p class="text-lg font-semibold text-gray-900"><?= View::e($user['email']) ?></p>
        </div>
        
        <?php if (!empty($user['phone'])): ?>
          <div>
            <p class="text-sm text-gray-600">Phone</p>
            <p class="text-lg font-semibold text-gray-900"><?= View::e($user['phone']) ?></p>
          </div>
        <?php endif; ?>
      </div>
    </div>
    
    <div class="bg-white rounded-xl border shadow-sm p-8">
      <h2 class="text-2xl font-bold mb-6 text-purple-700">My Bookings</h2>
      
      <?php if (empty($bookings)): ?>
        <div class="text-center py-8">
          <i class="fas fa-ticket-alt text-gray-400 text-4xl mb-4"></i>
          <p class="text-gray-600">You haven't made any bookings yet.</p>
          <a href="<?= url('/booking') ?>" 
             class="inline-block mt-4 bg-purple-700 hover:bg-purple-800 text-white px-6 py-2 rounded-md">
            Book Tickets Now
          </a>
        </div>
      <?php else: ?>
        <div class="space-y-4">
          <?php foreach ($bookings as $booking): ?>
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
              <div class="flex justify-between items-start mb-2">
                <div>
                  <h3 class="font-semibold text-lg text-gray-900"><?= View::e($booking['movie_title'] ?? 'N/A') ?></h3>
                  <p class="text-sm text-gray-600"><?= View::e($booking['venue_name'] ?? 'N/A') ?> - <?= View::e($booking['screen_name'] ?? 'N/A') ?></p>
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $booking['status'] === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                  <?= View::e(ucfirst($booking['status'])) ?>
                </span>
              </div>
              
              <div class="text-sm text-gray-600 space-y-1">
                <p><i class="fas fa-calendar mr-2"></i><?= date('F j, Y', strtotime($booking['show_date'])) ?></p>
                <p><i class="fas fa-clock mr-2"></i><?= View::e($booking['show_time']) ?></p>
                <p><i class="fas fa-chair mr-2"></i><?= (int)$booking['seats_count'] ?> seat(s)</p>
                <p class="text-lg font-semibold text-purple-700 mt-2">$<?= number_format((float)$booking['total_price'], 2) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php
View::partial('footer');
?>

