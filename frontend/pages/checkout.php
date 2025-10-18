<?php include dirname(__DIR__) . '/partials/header.php'; ?>

<?php
// Get booking data from POST (from booking.php)
$movieTitle = $_POST['movie'] ?? 'The Midnight Heist';
$showDate = $_POST['date'] ?? date('Y-m-d');
$seats = $_POST['seats'] ?? [];
$pricePerSeat = floatval($_POST['price'] ?? 12.50);
$totalPrice = count($seats) * $pricePerSeat;

// Sample payment methods
$paymentMethods = [
  ['id' => 'card', 'name' => 'Credit/Debit Card', 'icon' => '<i class="fas fa-credit-card text-blue-600"></i>'],
  ['id' => 'paypal', 'name' => 'PayPal', 'icon' => '<i class="fab fa-paypal text-blue-500"></i>'],
  ['id' => 'apple', 'name' => 'Apple Pay', 'icon' => '<i class="fab fa-apple text-gray-800"></i>'],
  ['id' => 'google', 'name' => 'Google Pay', 'icon' => '<i class="fab fa-google-pay text-blue-600"></i>'],
];

// Sample user data (would come from session/DB)
$user = [
  'name' => 'John Doe',
  'email' => 'john.doe@example.com',
  'phone' => '+1 (555) 123-4567',
];
?>

<main class="max-w-4xl mx-auto px-4 py-10 space-y-8">

  <!-- Booking Summary -->
  <section class="rounded-xl border bg-white shadow-sm p-6">
    <h2 class="text-2xl font-extrabold mb-4">Booking Summary</h2>
    
    <div class="space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="font-semibold text-lg"><?= htmlspecialchars($movieTitle) ?></h3>
          <p class="text-gray-600"><?= date('l, F j, Y', strtotime($showDate)) ?></p>
        </div>
        <div class="text-right">
          <p class="text-sm text-gray-600"><?= count($seats) ?> seat<?= count($seats) !== 1 ? 's' : '' ?></p>
          <p class="font-semibold">$<?= number_format($totalPrice, 2) ?></p>
        </div>
      </div>
      
      <div class="border-t pt-4">
        <div class="flex justify-between items-center">
          <span class="text-sm text-gray-600">Seats: <?= htmlspecialchars(implode(', ', $seats)) ?></span>
          <span class="text-sm">$<?= number_format($pricePerSeat, 2) ?> each</span>
        </div>
        <div class="flex justify-between items-center mt-2 font-semibold text-lg">
          <span>Total:</span>
          <span>$<?= number_format($totalPrice, 2) ?></span>
        </div>
      </div>
    </div>
  </section>

  <!-- Customer Information -->
  <section class="rounded-xl border bg-white shadow-sm p-6">
    <h2 class="text-2xl font-extrabold mb-4">Customer Information</h2>
    
    <form class="space-y-4">
      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <label class="block text-sm font-medium mb-1">Full Name</label>
          <input type="text" name="customer_name" value="<?= htmlspecialchars($user['name']) ?>" 
                 class="w-full rounded-md border px-3 py-2" required>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Email Address</label>
          <input type="email" name="customer_email" value="<?= htmlspecialchars($user['email']) ?>" 
                 class="w-full rounded-md border px-3 py-2" required>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Phone Number</label>
          <input type="tel" name="customer_phone" value="<?= htmlspecialchars($user['phone']) ?>" 
                 class="w-full rounded-md border px-3 py-2" required>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Confirmation Email</label>
          <input type="email" name="confirmation_email" value="<?= htmlspecialchars($user['email']) ?>" 
                 class="w-full rounded-md border px-3 py-2" required>
        </div>
      </div>
    </form>
  </section>

  <!-- Payment Method -->
  <section class="rounded-xl border bg-white shadow-sm p-6">
    <h2 class="text-2xl font-extrabold mb-4">Payment Method</h2>
    
    <div class="space-y-3">
      <?php foreach ($paymentMethods as $method): ?>
        <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
          <input type="radio" name="payment_method" value="<?= htmlspecialchars($method['id']) ?>" 
                 class="mr-3" <?= $method['id'] === 'card' ? 'checked' : '' ?>>
          <span class="text-2xl mr-3"><?= $method['icon'] ?></span>
          <span class="font-medium"><?= htmlspecialchars($method['name']) ?></span>
        </label>
      <?php endforeach; ?>
    </div>

    <!-- Credit Card Form (shown when card is selected) -->
    <div id="cardForm" class="mt-6 space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1">Card Number</label>
        <input type="text" name="card_number" placeholder="1234 5678 9012 3456" 
               class="w-full rounded-md border px-3 py-2">
      </div>
      <div class="grid gap-4 md:grid-cols-3">
        <div>
          <label class="block text-sm font-medium mb-1">Expiry Date</label>
          <input type="text" name="card_expiry" placeholder="MM/YY" 
                 class="w-full rounded-md border px-3 py-2">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">CVV</label>
          <input type="text" name="card_cvv" placeholder="123" 
                 class="w-full rounded-md border px-3 py-2">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Cardholder Name</label>
          <input type="text" name="cardholder_name" placeholder="John Doe" 
                 class="w-full rounded-md border px-3 py-2">
        </div>
      </div>
    </div>
  </section>

  <!-- Terms and Conditions -->
  <section class="rounded-xl border bg-white shadow-sm p-6">
    <h2 class="text-2xl font-extrabold mb-4">Terms & Conditions</h2>
    
    <div class="space-y-4 text-sm text-gray-600">
      <div>
        <h3 class="font-semibold text-gray-800">Cancellation Policy</h3>
        <p>You can cancel your booking up to 2 hours before the showtime for a full refund. Cancellations within 2 hours of showtime will receive a 50% refund.</p>
      </div>
      <div>
        <h3 class="font-semibold text-gray-800">Seat Selection</h3>
        <p>Seats are allocated on a first-come, first-served basis. We reserve the right to reassign seats if necessary.</p>
      </div>
      <div>
        <h3 class="font-semibold text-gray-800">Age Restrictions</h3>
        <p>Please ensure you meet the age requirements for the movie. Valid ID may be required for age verification.</p>
      </div>
    </div>
    
    <div class="mt-6">
      <label class="flex items-start space-x-3">
        <input type="checkbox" name="terms_accepted" class="mt-1" required>
        <span class="text-sm">I agree to the terms and conditions and privacy policy.</span>
      </label>
    </div>
  </section>

  <!-- Action Buttons -->
  <div class="flex gap-4 justify-end">
    <a href="/Cinema/frontend/pages/booking.php" 
       class="px-6 py-3 border rounded-md hover:bg-gray-50">
      Back to Seats
    </a>
    <button type="button" id="confirmBooking" 
            class="px-8 py-3 bg-purple-700 hover:bg-purple-800 text-white rounded-md font-semibold">
      Complete Booking - $<?= number_format($totalPrice, 2) ?>
    </button>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
  const cardForm = document.getElementById('cardForm');
  const confirmBtn = document.getElementById('confirmBooking');
  
  // Show/hide card form based on payment method
  paymentMethods.forEach(method => {
    method.addEventListener('change', function() {
      if (this.value === 'card') {
        cardForm.style.display = 'block';
      } else {
        cardForm.style.display = 'none';
      }
    });
  });
  
  // Confirm booking
  confirmBtn.addEventListener('click', function() {
    if (confirm('Are you sure you want to complete this booking?')) {
      // Here you would typically process the payment and redirect to confirmation
      alert('Booking confirmed! You will receive a confirmation email shortly.');
      // window.location.href = '/Cinema/frontend/pages/confirmation.php';
    }
  });
});
</script>

<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
