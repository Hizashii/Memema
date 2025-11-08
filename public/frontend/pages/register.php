<?php include dirname(__DIR__) . '/partials/header.php'; ?>

<?php
require_once __DIR__ . '/../../../app/auth/user_auth.php';

if (isUserLoggedIn()) {
    require_once __DIR__ . '/../../../app/core/database.php';
    $base = getBasePath();
    header('Location: ' . $base . '/public/frontend/pages/profile.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    
    if (empty($fullName) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = 'Please fill in all required fields';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } else {
        $result = userRegister($fullName, $email, $password, $phone);
        if ($result['success']) {
            $success = $result['message'];
            $loginResult = userLogin($email, $password);
            if ($loginResult['success']) {
                require_once __DIR__ . '/../../../app/core/database.php';
                $base = getBasePath();
                header('Location: ' . $base . '/public/frontend/pages/profile.php');
                exit;
            }
        } else {
            $error = $result['error'];
        }
    }
}
?>

<main class="max-w-md mx-auto px-4 py-10">
  <div class="bg-white rounded-xl border shadow-sm p-8">
    <div class="text-center mb-8">
      <h1 class="text-3xl font-bold text-purple-700 mb-2">Create Account</h1>
      <p class="text-gray-600">Join us and start booking movies</p>
    </div>

    <?php if ($error): ?>
      <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
        <i class="fas fa-check-circle mr-2"></i>
        <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-6">
      <div>
        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
        <input type="text" id="full_name" name="full_name" required
               value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
      </div>
      
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
        <input type="email" id="email" name="email" required
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
      </div>
      
      <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
        <input type="tel" id="phone" name="phone"
               value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
      </div>
      
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
        <input type="password" id="password" name="password" required
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
      </div>
      
      <div>
        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
        <input type="password" id="confirm_password" name="confirm_password" required
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
      </div>
      
      <button type="submit" 
              class="w-full bg-purple-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-purple-700 transition-colors duration-200">
        Create Account
      </button>
    </form>

    <div class="mt-6 text-center">
      <p class="text-gray-600">
        Already have an account? 
        <a href="login.php" class="text-purple-600 hover:text-purple-700 font-semibold">Sign in here</a>
      </p>
    </div>
  </div>
</main>

<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
