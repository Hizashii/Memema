<?php
View::partial('header');
?>

<main class="max-w-4xl mx-auto px-4 py-10">
  <div class="text-center mb-12">
    <h1 class="text-4xl font-extrabold text-purple-700 mb-4">Contact Us</h1>
    <p class="text-xl text-gray-600">
      Have a question? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
    </p>
  </div>

  <div class="grid md:grid-cols-2 gap-8">
    <!-- Contact Information -->
    <div class="bg-white rounded-xl border shadow-sm p-8">
      <h2 class="text-2xl font-bold mb-6 text-purple-700">Get in Touch</h2>
      
      <div class="space-y-6">
        <?php if (!empty($contactInfo['phone'])): ?>
          <div class="flex items-start gap-4">
            <div class="bg-purple-100 rounded-full p-3">
              <i class="fas fa-phone text-purple-700"></i>
            </div>
            <div>
              <h3 class="font-semibold text-gray-800">Phone</h3>
              <a href="tel:<?= View::e($contactInfo['phone']) ?>" class="text-gray-600 hover:text-purple-700">
                <?= View::e($contactInfo['phone']) ?>
              </a>
            </div>
          </div>
        <?php endif; ?>
        
        <?php if (!empty($contactInfo['email'])): ?>
          <div class="flex items-start gap-4">
            <div class="bg-purple-100 rounded-full p-3">
              <i class="fas fa-envelope text-purple-700"></i>
            </div>
            <div>
              <h3 class="font-semibold text-gray-800">Email</h3>
              <a href="mailto:<?= View::e($contactInfo['email']) ?>" class="text-gray-600 hover:text-purple-700">
                <?= View::e($contactInfo['email']) ?>
              </a>
            </div>
          </div>
        <?php endif; ?>
        
        <?php if (!empty($contactInfo['address'])): ?>
          <div class="flex items-start gap-4">
            <div class="bg-purple-100 rounded-full p-3">
              <i class="fas fa-map-marker-alt text-purple-700"></i>
            </div>
            <div>
              <h3 class="font-semibold text-gray-800">Address</h3>
              <p class="text-gray-600"><?= View::e($contactInfo['address']) ?></p>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Contact Form -->
    <div class="bg-white rounded-xl border shadow-sm p-8">
      <h2 class="text-2xl font-bold mb-6 text-purple-700">Send us a Message</h2>
      
      <?php if (isset($success)): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-6">
          <i class="fas fa-check-circle mr-2"></i>
          <?= View::e($success) ?>
        </div>
      <?php endif; ?>
      
      <?php if (isset($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">
          <i class="fas fa-exclamation-circle mr-2"></i>
          <?= View::e($error) ?>
        </div>
      <?php endif; ?>
      
      <form method="POST" action="<?= url('/contact') ?>" class="space-y-6">
        <?= Csrf::field() ?>
        
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
            Your Name <span class="text-red-500">*</span>
          </label>
          <input type="text" 
                 id="name" 
                 name="name" 
                 required
                 class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
                 placeholder="John Doe">
        </div>
        
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
            Your Email <span class="text-red-500">*</span>
          </label>
          <input type="email" 
                 id="email" 
                 name="email" 
                 required
                 class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
                 placeholder="john@example.com">
        </div>
        
        <div>
          <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
            Subject <span class="text-red-500">*</span>
          </label>
          <input type="text" 
                 id="subject" 
                 name="subject" 
                 required
                 class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
                 placeholder="How can we help?">
        </div>
        
        <div>
          <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
            Message <span class="text-red-500">*</span>
          </label>
          <textarea id="message" 
                    name="message" 
                    rows="6" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
                    placeholder="Your message here..."></textarea>
        </div>
        
        <button type="submit" 
                class="w-full bg-purple-700 hover:bg-purple-800 text-white font-semibold py-3 px-6 rounded-md transition-colors">
          <i class="fas fa-paper-plane mr-2"></i>
          Send Message
        </button>
      </form>
    </div>
  </div>
</main>

<?php
View::partial('footer');
?>

