<?php
View::partial('header');
?>

<main class="max-w-md mx-auto px-4 py-10">
  <div class="bg-white rounded-xl border shadow-sm p-8">
    <h1 class="text-3xl font-bold text-purple-700 mb-6 text-center">Create Account</h1>
    
    <?php if (!empty($error)): ?>
      <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <?= View::e($error) ?>
      </div>
    <?php endif; ?>
    
    <form method="POST" action="<?= url('/register') ?>" id="register-form">
      <?= Csrf::field() ?>
      <input type="hidden" name="redirect" value="<?= View::e($redirect) ?>">
      
      <div class="space-y-4">
        <!-- Full Name -->
        <div>
          <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
          <input type="text" 
                 id="full_name" 
                 name="full_name" 
                 required
                 class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
                 placeholder="John Doe">
        </div>
        
        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
          <input type="email" 
                 id="email" 
                 name="email" 
                 required
                 class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
                 placeholder="your@email.com">
        </div>
        
        <!-- Phone -->
        <div>
          <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone (Optional)</label>
          <input type="tel" 
                 id="phone" 
                 name="phone"
                 class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
                 placeholder="+1 234 567 8900">
        </div>
        
        <!-- Password -->
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
          <input type="password" 
                 id="password" 
                 name="password" 
                 required
                 minlength="6"
                 class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
                 placeholder="At least 6 characters">
          <p class="text-xs text-gray-500 mt-1">Must be at least 6 characters long</p>
        </div>
        
        <!-- Confirm Password -->
        <div>
          <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
          <input type="password" 
                 id="confirm_password" 
                 name="confirm_password" 
                 required
                 minlength="6"
                 class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
                 placeholder="Re-enter your password">
        </div>
        
        <!-- Submit Button -->
        <button type="submit" 
                class="w-full bg-purple-700 hover:bg-purple-800 text-white font-semibold py-3 px-6 rounded-md transition-colors">
          <i class="fas fa-user-plus mr-2"></i>Create Account
        </button>
      </div>
    </form>
    
    <div class="mt-6 text-center">
      <p class="text-gray-600">
        Already have an account? 
        <a href="<?= url('/login?redirect=' . urlencode($redirect)) ?>" class="text-purple-700 hover:text-purple-800 font-semibold">
          Login here
        </a>
      </p>
    </div>
    
    <div class="mt-4 text-center">
      <a href="<?= url('/') ?>" class="text-gray-600 hover:text-gray-800 text-sm">
        <i class="fas fa-arrow-left mr-1"></i>Back to Home
      </a>
    </div>
  </div>
</main>

<script>
// Client-side password match validation
document.getElementById('register-form').addEventListener('submit', function(e) {
  const password = document.getElementById('password').value;
  const confirmPassword = document.getElementById('confirm_password').value;
  
  if (password !== confirmPassword) {
    e.preventDefault();
    alert('Passwords do not match!');
    return false;
  }
});
</script>

<?php
View::partial('footer');
?>

