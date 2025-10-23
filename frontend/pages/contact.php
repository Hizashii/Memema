<?php include dirname(__DIR__) . '/partials/header.php'; ?>

<?php
// Sample contact information
$contactInfo = [
  'phone' => '+1 (555) 123-CINE',
  'email' => 'info@cinemabook.com',
  'address' => '123 Cinema Street, Movie City, MC 12345',
  'hours' => [
    'Monday - Thursday' => '10:00 AM - 11:00 PM',
    'Friday - Saturday' => '10:00 AM - 12:00 AM',
    'Sunday' => '12:00 PM - 10:00 PM'
  ]
];

$departments = [
  [
    'name' => 'General Inquiries',
    'email' => 'info@cinemabook.com',
    'phone' => '+1 (555) 123-CINE',
    'description' => 'For general questions about movies, showtimes, and venue information.'
  ],
  [
    'name' => 'Technical Support',
    'email' => 'support@cinemabook.com',
    'phone' => '+1 (555) 123-TECH',
    'description' => 'Help with website issues, booking problems, and account support.'
  ],
  [
    'name' => 'Business Inquiries',
    'email' => 'business@cinemabook.com',
    'phone' => '+1 (555) 123-BIZ',
    'description' => 'Partnerships, advertising, and corporate bookings.'
  ],
  [
    'name' => 'Feedback & Complaints',
    'email' => 'feedback@cinemabook.com',
    'phone' => '+1 (555) 123-FEED',
    'description' => 'Share your experience, suggestions, or report issues.'
  ]
];

$faqs = [
  [
    'question' => 'How do I book tickets online?',
    'answer' => 'Simply browse our movies, select your preferred showtime, choose your seats, and complete the checkout process. You\'ll receive a confirmation email with your ticket details.'
  ],
  [
    'question' => 'Can I cancel or modify my booking?',
    'answer' => 'Yes, you can cancel your booking up to 2 hours before the showtime through your account or by contacting us. Modifications depend on availability.'
  ],
  [
    'question' => 'What payment methods do you accept?',
    'answer' => 'We accept all major credit cards, PayPal, Apple Pay, and Google Pay for online bookings.'
  ],
  [
    'question' => 'Do you offer group discounts?',
    'answer' => 'Yes, we offer special rates for groups of 10 or more. Contact our business department for more information.'
  ],
  [
    'question' => 'Are there age restrictions for movies?',
    'answer' => 'Yes, we follow standard movie rating guidelines. Valid ID may be required for age verification at the venue.'
  ]
];
?>

<main class="max-w-6xl mx-auto px-4 py-10 space-y-12">

  <!-- Hero Section -->
  <section class="text-center">
    <h1 class="text-4xl font-extrabold mb-4">Contact Us</h1>
    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
      We're here to help! Get in touch with our team for any questions, support, or feedback about your cinema experience.
    </p>
  </section>

  <!-- Contact Information -->
  <section class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
    <div class="text-center p-6 rounded-xl border bg-white shadow-sm">
      <div class="text-3xl mb-3 text-green-600">
        <i class="fas fa-phone"></i>
      </div>
      <h3 class="font-semibold mb-2">Phone</h3>
      <p class="text-gray-600"><?= htmlspecialchars($contactInfo['phone']) ?></p>
    </div>
    
    <div class="text-center p-6 rounded-xl border bg-white shadow-sm">
      <div class="text-3xl mb-3 text-blue-600">
        <i class="fas fa-envelope"></i>
      </div>
      <h3 class="font-semibold mb-2">Email</h3>
      <p class="text-gray-600"><?= htmlspecialchars($contactInfo['email']) ?></p>
    </div>
    
    <div class="text-center p-6 rounded-xl border bg-white shadow-sm">
      <div class="text-3xl mb-3 text-red-600">
        <i class="fas fa-map-marker-alt"></i>
      </div>
      <h3 class="font-semibold mb-2">Address</h3>
      <p class="text-gray-600"><?= htmlspecialchars($contactInfo['address']) ?></p>
    </div>
    
    <div class="text-center p-6 rounded-xl border bg-white shadow-sm">
      <div class="text-3xl mb-3 text-purple-600">
        <i class="fas fa-clock"></i>
      </div>
      <h3 class="font-semibold mb-2">Hours</h3>
      <p class="text-gray-600">See below</p>
    </div>
  </section>

  <!-- Contact Form -->
  <section class="rounded-xl border bg-white shadow-sm p-8">
    <h2 class="text-2xl font-extrabold mb-6">Send us a Message</h2>
    
    <form class="space-y-6" method="post" action="#">
      <div class="grid gap-6 md:grid-cols-2">
        <div>
          <label class="block text-sm font-medium mb-1">Full Name *</label>
          <input type="text" name="name" required 
                 class="w-full rounded-md border px-3 py-2">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Email Address *</label>
          <input type="email" name="email" required 
                 class="w-full rounded-md border px-3 py-2">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Phone Number</label>
          <input type="tel" name="phone" 
                 class="w-full rounded-md border px-3 py-2">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Subject *</label>
          <select name="subject" required class="w-full rounded-md border px-3 py-2">
            <option value="">Select a subject</option>
            <option value="general">General Inquiry</option>
            <option value="booking">Booking Support</option>
            <option value="technical">Technical Issue</option>
            <option value="feedback">Feedback</option>
            <option value="business">Business Inquiry</option>
            <option value="other">Other</option>
          </select>
        </div>
      </div>
      
      <div>
        <label class="block text-sm font-medium mb-1">Message *</label>
        <textarea name="message" rows="6" required 
                  class="w-full rounded-md border px-3 py-2" 
                  placeholder="Please describe your inquiry in detail..."></textarea>
      </div>
      
      <div class="flex items-start space-x-3">
        <input type="checkbox" name="newsletter" class="mt-1">
        <span class="text-sm text-gray-600">Subscribe to our newsletter for movie updates and special offers</span>
      </div>
      
      <button type="submit" 
              class="w-full bg-purple-700 hover:bg-purple-800 text-white px-6 py-3 rounded-md font-semibold">
        Send Message
      </button>
    </form>
  </section>

  <!-- Departments -->
  <section>
    <h2 class="text-2xl font-extrabold mb-6">Contact by Department</h2>
    
    <div class="grid gap-6 md:grid-cols-2">
      <?php foreach ($departments as $dept): ?>
        <div class="rounded-xl border bg-white shadow-sm p-6">
          <h3 class="font-semibold text-lg mb-2"><?= htmlspecialchars($dept['name']) ?></h3>
          <p class="text-gray-600 mb-4"><?= htmlspecialchars($dept['description']) ?></p>
          <div class="space-y-2">
            <div class="flex items-center space-x-2">
              <i class="fas fa-envelope text-blue-500 text-sm"></i>
              <a href="mailto:<?= htmlspecialchars($dept['email']) ?>" 
                 class="text-purple-700 hover:text-purple-800 text-sm">
                <?= htmlspecialchars($dept['email']) ?>
              </a>
            </div>
            <div class="flex items-center space-x-2">
              <i class="fas fa-phone text-green-500 text-sm"></i>
              <a href="tel:<?= htmlspecialchars($dept['phone']) ?>" 
                 class="text-purple-700 hover:text-purple-800 text-sm">
                <?= htmlspecialchars($dept['phone']) ?>
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Operating Hours -->
  <section class="rounded-xl border bg-white shadow-sm p-8">
    <h2 class="text-2xl font-extrabold mb-6">Operating Hours</h2>
    
    <div class="grid gap-4 md:grid-cols-3">
      <?php foreach ($contactInfo['hours'] as $day => $hours): ?>
        <div class="flex justify-between items-center p-3 rounded-lg bg-gray-50">
          <span class="font-medium"><?= htmlspecialchars($day) ?></span>
          <span class="text-gray-600"><?= htmlspecialchars($hours) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
    
    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
      <p class="text-sm text-blue-800">
        <strong>Note:</strong> Our customer service team is available 24/7 via email. 
        Phone support is available during operating hours.
      </p>
    </div>
  </section>

  <!-- FAQ Section -->
  <section>
    <h2 class="text-2xl font-extrabold mb-6">Frequently Asked Questions</h2>
    
    <div class="space-y-4">
      <?php foreach ($faqs as $index => $faq): ?>
        <div class="rounded-xl border bg-white shadow-sm">
          <button class="faq-toggle w-full p-6 text-left flex justify-between items-center hover:bg-gray-50" 
                  data-target="faq-<?= $index ?>">
            <span class="font-semibold"><?= htmlspecialchars($faq['question']) ?></span>
            <span class="text-purple-700">+</span>
          </button>
          <div id="faq-<?= $index ?>" class="faq-content hidden px-6 pb-6">
            <p class="text-gray-600"><?= htmlspecialchars($faq['answer']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Map Section -->
  <section class="rounded-xl border bg-white shadow-sm p-8">
    <h2 class="text-2xl font-extrabold mb-6">Find Us</h2>
    
    <div class="grid gap-8 md:grid-cols-2">
      <div>
        <h3 class="font-semibold mb-4">Our Location</h3>
        <p class="text-gray-600 mb-4"><?= htmlspecialchars($contactInfo['address']) ?></p>
        
        <div class="space-y-2">
          <div class="flex items-center space-x-2">
            <i class="fas fa-car text-blue-500"></i>
            <span class="text-sm">Free parking available</span>
          </div>
          <div class="flex items-center space-x-2">
            <i class="fas fa-bus text-green-500"></i>
            <span class="text-sm">Bus routes: 15, 23, 45</span>
          </div>
          <div class="flex items-center space-x-2">
            <i class="fas fa-subway text-purple-500"></i>
            <span class="text-sm">Metro: Cinema Station (5 min walk)</span>
          </div>
        </div>
      </div>
      
      <div class="bg-gray-200 rounded-lg flex items-center justify-center h-64">
        <div class="text-center text-gray-500">
          <div class="text-4xl mb-2">
            <i class="fas fa-map text-gray-400"></i>
          </div>
          <p>Interactive Map</p>
          <p class="text-sm">(Map integration would go here)</p>
        </div>
      </div>
    </div>
  </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // FAQ Toggle functionality
  document.querySelectorAll('.faq-toggle').forEach(button => {
    button.addEventListener('click', function() {
      const target = this.getAttribute('data-target');
      const content = document.getElementById(target);
      const icon = this.querySelector('span:last-child');
      
      if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.textContent = '−';
      } else {
        content.classList.add('hidden');
        icon.textContent = '+';
      }
    });
  });
});
</script>

<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
