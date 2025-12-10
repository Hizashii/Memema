<?php
require_once __DIR__ . '/../../../app/classes/autoload.php';
require_once __DIR__ . '/../../../app/core/database.php';
require_once __DIR__ . '/../../../app/auth/user_auth.php';

// Include header only when accessed directly (not via index.php)
if (!defined('LOADED_VIA_INDEX')) {
    include dirname(__DIR__) . '/partials/header.php';
}

requireUserLogin();

$userId = $_SESSION['user_id'] ?? null;
$user = null;
$history = [];

try {
    if ($userId) {
        $user = User::getById($userId);
        $history = Booking::getByUserId($userId);
    }
} catch (Exception $e) {
    $user = ['full_name' => 'Guest', 'email' => ''];
    $history = [];
    $error = "Unable to load profile information. Please try again later.";
}

if (!$user) {
    $user = ['full_name' => 'Guest', 'email' => ''];
}
?>

<main class="max-w-4xl mx-auto px-4 py-10 space-y-8">
  <h1 class="text-3xl font-bold">My Profile</h1>

  <div class="grid gap-8 md:grid-cols-2">
    <!-- Profile Details -->
    <div class="bg-white rounded-xl border shadow-sm p-6">
      <h2 class="text-xl font-bold mb-4">Profile Details</h2>
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
          <div class="text-lg font-semibold"><?= htmlspecialchars($user['full_name']) ?></div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <div class="text-lg"><?= htmlspecialchars($user['email']) ?></div>
        </div>
      </div>
    </div>

    <!-- Booking History -->
    <div class="bg-white rounded-xl border shadow-sm p-6">
      <h2 class="text-xl font-bold mb-4">Recent Bookings</h2>
      <?php if (isset($error)): ?>
        <div class="text-red-600 text-sm"><?= htmlspecialchars($error) ?></div>
      <?php elseif (empty($history)): ?>
        <div class="text-gray-600 text-sm">No bookings found</div>
      <?php else: ?>
        <div class="space-y-3">
          <?php foreach ($history as $booking): ?>
            <div class="border-b border-gray-200 pb-3 last:border-b-0">
              <div class="font-semibold"><?= htmlspecialchars($booking['movie_title']) ?></div>
              <div class="text-sm text-gray-600">
                <?= date('M j, Y', strtotime($booking['show_date'])) ?> • 
                <?= (int)$booking['seats_count'] ?> seat<?= $booking['seats_count'] > 1 ? 's' : '' ?> • 
                $<?= number_format((float)$booking['total_price'], 2) ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php if (!defined('LOADED_VIA_INDEX')) { include dirname(__DIR__) . '/partials/footer.php'; } ?>
