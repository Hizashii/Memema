<?php
View::partial('header');
?>

<main class="max-w-md mx-auto px-4 py-10">
  <div class="bg-white rounded-xl border shadow-sm p-8">
    <h1 class="text-3xl font-bold text-purple-700 mb-6 text-center">Login</h1>
    
    <?php if (!empty($error)): ?>
      <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <?= View::e($error) ?>
      </div>
    <?php endif; ?>
    
    <form method="POST" action="<?= url('/login') ?>">
      <?= Csrf::field() ?>
      <input type="hidden" name="redirect" value="<?= View::e($redirect) ?>">
      
      <div class="space-y-4">
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
        
        <!-- Password -->
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
          <input type="password" 
                 id="password" 
                 name="password" 
                 required
                 class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
                 placeholder="Enter your password">
        </div>
        
        <!-- Submit Button -->
        <button type="submit" 
                class="w-full bg-purple-700 hover:bg-purple-800 text-white font-semibold py-3 px-6 rounded-md transition-colors">
          <i class="fas fa-sign-in-alt mr-2"></i>Login
        </button>
      </div>
    </form>
    
    <div class="mt-6 text-center">
      <p class="text-gray-600">
        Don't have an account? 
        <a href="<?= url('/register?redirect=' . urlencode($redirect)) ?>" class="text-purple-700 hover:text-purple-800 font-semibold">
          Sign up here
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

<?php
View::partial('footer');
?>

