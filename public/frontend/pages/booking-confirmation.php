<?php include dirname(__DIR__) . '/partials/header.php'; ?>

<?php
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/config/security.php';
require_once __DIR__ . '/../../../app/core/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie_id = validateInt($_POST['movie_id'] ?? 0, 1);
    $venue_id = validateInt($_POST['venue_id'] ?? 0, 1);
    $screen_id = validateInt($_POST['screen_id'] ?? 0, 1);
    $show_date = sanitizeInput($_POST['show_date'] ?? '');
    $show_time = sanitizeInput($_POST['show_time'] ?? '');
    $seats = sanitizeInput($_POST['seats'] ?? '');
    $total_price = (float)($_POST['total_price'] ?? 0);
    $full_name = sanitizeInput($_POST['full_name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $payment_method = sanitizeInput($_POST['payment_method'] ?? '');
    $terms = isset($_POST['terms']);

    if (!$movie_id || !$venue_id || !$screen_id || !$show_date || !$show_time || !$seats || 
        !$full_name || !$email || !$phone || !$terms) {
        $error = "Please fill in all required fields and accept the terms.";
    } else {
        try {
            $user_id = 1;
            
            $booking_id = insertBooking($user_id, $movie_id, $venue_id, $screen_id, $show_date, $show_time, count(explode(',', $seats)), $total_price);
            
            $seat_array = explode(',', $seats);
            foreach ($seat_array as $seat) {
                $seat = trim($seat);
                if ($seat) {
                    $row = substr($seat, 0, 1);
                    $number = (int)substr($seat, 1);
                    $is_wheelchair = ($row === 'J' && $number === 1) || ($row === 'I' && $number === 16);
                    
                    insertSeatReservation($booking_id, $screen_id, $row, $number, $is_wheelchair ? 1 : 0);
                }
            }
            
            $success = true;
            $booking_number = 'BK' . str_pad($booking_id, 6, '0', STR_PAD_LEFT);
            
        } catch (Exception $e) {
            $error = "Booking failed. Please try again.";
        }
    }
} else {
    header('Location: /Cinema/public/frontend/pages/movies.php');
    exit;
}
?>

<main class="max-w-4xl mx-auto px-4 py-10 space-y-8">
  <?php if (isset($success) && $success): ?>
    <div class="text-center">
      <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <i class="fas fa-check text-green-600 text-3xl"></i>
      </div>
      <h1 class="text-3xl font-bold text-green-700 mb-4">Booking Confirmed!</h1>
      <p class="text-xl text-gray-600 mb-6">Your tickets have been successfully booked.</p>
      <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-8">
        <p class="text-lg font-semibold text-green-800">Booking Number: <?= htmlspecialchars($booking_number) ?></p>
      </div>
    </div>

    <div class="bg-white rounded-xl border shadow-sm p-6">
      <h2 class="text-xl font-bold mb-6">Booking Details</h2>
      <div class="space-y-4">
        <div class="flex justify-between">
          <span class="text-gray-600">Movie:</span>
          <span class="font-semibold"><?= htmlspecialchars($full_name) ?></span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-600">Email:</span>
          <span class="font-semibold"><?= htmlspecialchars($email) ?></span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-600">Phone:</span>
          <span class="font-semibold"><?= htmlspecialchars($phone) ?></span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-600">Show Date:</span>
          <span class="font-semibold"><?= date('M j, Y', strtotime($show_date)) ?></span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-600">Show Time:</span>
          <span class="font-semibold"><?= date('g:i A', strtotime($show_time)) ?></span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-600">Seats:</span>
          <span class="font-semibold"><?= htmlspecialchars($seats) ?></span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-600">Payment Method:</span>
          <span class="font-semibold"><?= ucfirst($payment_method) ?></span>
        </div>
        <div class="border-t pt-4">
          <div class="flex justify-between text-lg">
            <span class="font-bold">Total Paid:</span>
            <span class="font-bold text-purple-700">$<?= number_format($total_price, 2) ?></span>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
      <h3 class="text-lg font-semibold text-blue-800 mb-3">What's Next?</h3>
      <ul class="space-y-2 text-blue-700">
        <li>• You will receive a confirmation email at <?= htmlspecialchars($email) ?></li>
        <li>• Arrive at the cinema 15 minutes before showtime</li>
        <li>• Present your booking number at the ticket counter</li>
        <li>• Enjoy your movie!</li>
      </ul>
    </div>

    <div class="flex justify-center gap-4">
      <a href="/Cinema/public/frontend/pages/movies.php" 
         class="bg-purple-700 hover:bg-purple-800 text-white px-6 py-3 rounded-md">
        Book Another Movie
      </a>
      <a href="/Cinema/public/frontend/pages/profile.php" 
         class="border border-purple-700 text-purple-700 hover:bg-purple-50 px-6 py-3 rounded-md">
        View My Bookings
      </a>
    </div>

  <?php else: ?>
    <div class="text-center">
      <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <i class="fas fa-times text-red-600 text-3xl"></i>
      </div>
      <h1 class="text-3xl font-bold text-red-700 mb-4">Booking Failed</h1>
      <p class="text-xl text-gray-600 mb-6"><?= htmlspecialchars($error ?? 'An error occurred. Please try again.') ?></p>
      <a href="/Cinema/public/frontend/pages/movies.php" 
         class="inline-block bg-purple-700 hover:bg-purple-800 text-white px-6 py-3 rounded-md">
        Try Again
      </a>
    </div>
  <?php endif; ?>
</main>

<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
