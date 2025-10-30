<?php include dirname(__DIR__) . '/partials/header.php'; ?>

<?php
require_once __DIR__ . '/../../../app/auth/user_auth.php';

if (isUserLoggedIn()) {
    header('Location: profile.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $result = userLogin($email, $password);
        if ($result['success']) {
            header('Location: profile.php');
            exit;
        } else {
            $error = $result['error'];
        }
    }
}
?>

<main class="max-w-md mx-auto px-4 py-10">
  <div class="bg-white rounded-xl border shadow-sm p-8">
    <div class="text-center mb-8">
      <h1 class="text-3xl font-bold text-purple-700 mb-2">Welcome Back</h1>
      <p class="text-gray-600">Sign in to your account</p>
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
        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
        <input type="email" id="email" name="email" required
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
      </div>
      
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
        <input type="password" id="password" name="password" required
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
      </div>
      
      <button type="submit" 
              class="w-full bg-purple-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-purple-700 transition-colors duration-200">
        Sign In
      </button>
    </form>

    <div class="mt-6 text-center">
      <p class="text-gray-600">
        Don't have an account? 
        <a href="register.php" class="text-purple-600 hover:text-purple-700 font-semibold">Sign up here</a>
      </p>
    </div>
  </div>
</main>

<?php include dirname(__DIR__) . '/partials/footer.php'; ?>
