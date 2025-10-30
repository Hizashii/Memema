<?php include dirname(__DIR__) . '/partials/header.php'; ?>

<?php
require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/core/database.php';

try {
    $info = executeQuery("SELECT phone, email, address FROM contact_info LIMIT 1");
    $contact = $info[0] ?? ['phone' => '', 'email' => '', 'address' => ''];
} catch (Exception $e) {
    $contact = ['phone' => '', 'email' => '', 'address' => ''];
    $error = "Unable to load contact information. Please try again later.";
}
?>

<main class="max-w-6xl mx-auto px-4 py-10 space-y-12">
  <section class="text-center">
    <h1 class="text-4xl font-extrabold text-purple-700 mb-6">Contact Us</h1>
    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
      Get in touch with us for any questions, feedback, or support
    </p>
  </section>

  <?php if (isset($error)): ?>
    <div class="text-center text-red-600 bg-red-50 p-6 rounded-lg">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php else: ?>
    <div class="grid gap-8 lg:grid-cols-2">
      <div class="space-y-8">
        <h2 class="text-2xl font-bold text-gray-800">Get in Touch</h2>
        <div class="grid gap-6 md:grid-cols-3 lg:grid-cols-1">
          <div class="bg-white rounded-xl border shadow-sm p-6 text-center lg:text-left">
            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto lg:mx-0 mb-4">
              <i class="fas fa-phone text-purple-700 text-xl"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Phone</h3>
            <p class="text-gray-600"><?= htmlspecialchars($contact['phone']) ?></p>
          </div>

          <div class="bg-white rounded-xl border shadow-sm p-6 text-center lg:text-left">
            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto lg:mx-0 mb-4">
              <i class="fas fa-envelope text-purple-700 text-xl"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Email</h3>
            <p class="text-gray-600"><?= htmlspecialchars($contact['email']) ?></p>
          </div>

          <div class="bg-white rounded-xl border shadow-sm p-6 text-center lg:text-left">
            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto lg:mx-0 mb-4">
              <i class="fas fa-map-marker-alt text-purple-700 text-xl"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Address</h3>
            <p class="text-gray-600"><?= htmlspecialchars($contact['address']) ?></p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl border shadow-sm p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Send us a Message</h2>
        <form id="contactForm" class="space-y-6">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
            <input type="text" id="name" name="name" required
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
          </div>
          
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
            <input type="email" id="email" name="email" required
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
          </div>
          
          <div>
            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
            <input type="text" id="subject" name="subject" required
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
          </div>
          
          <div>
            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
            <textarea id="message" name="message" rows="5" required
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"></textarea>
          </div>
          
          <button type="submit" 
                  class="w-full bg-purple-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-purple-700 transition-colors duration-200">
            Send Message
          </button>
        </form>
        
        <div id="messageContainer" class="mt-4 hidden">
          <div id="successMessage" class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg hidden">
            <i class="fas fa-check-circle mr-2"></i>
            <span id="successText"></span>
          </div>
          <div id="errorMessage" class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg hidden">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span id="errorText"></span>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <script>
  document.getElementById('contactForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const messageContainer = document.getElementById('messageContainer');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');
    const successText = document.getElementById('successText');
    const errorText = document.getElementById('errorText');
    successMessage.classList.add('hidden');
    errorMessage.classList.add('hidden');
    messageContainer.classList.add('hidden');
    
    try {
      const response = await fetch('contact-handler.php', {
        method: 'POST',
        body: formData
      });
      
      const result = await response.json();
      
      if (result.success) {
        successText.textContent = result.message;
        successMessage.classList.remove('hidden');
        messageContainer.classList.remove('hidden');
        this.reset();
      } else {
        if (result.errors) {
          errorText.textContent = result.errors.join(', ');
        } else {
          errorText.textContent = result.error || 'An error occurred';
        }
        errorMessage.classList.remove('hidden');
        messageContainer.classList.remove('hidden');
      }
    } catch (error) {
      errorText.textContent = 'Network error. Please try again.';
      errorMessage.classList.remove('hidden');
      messageContainer.classList.remove('hidden');
    }
  });
  </script>
</main>

<?php include dirname(__DIR__) . '/partials/footer.php'; ?>