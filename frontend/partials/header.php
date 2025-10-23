<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cinema</title>
</head>
  <link rel="stylesheet" href="/Cinema/frontend/assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<body class="bg-white text-gray-700">

<header class="flex items-center justify-between border-b border-purple-200 px-6 py-3">
  <div class="flex items-center space-x-2">
    <span class="text-purple-700 font-bold text-xl">CinemaBook</span>
  </div>

  <nav class="flex space-x-6 text-sm">
    <a href="/Cinema/frontend/index.php" class="text-purple-700 font-semibold">Home</a>
    <a href="/Cinema/frontend/pages/movies.php" class="text-gray-700 hover:text-purple-700">Movies</a>
    <a href="/Cinema/frontend/pages/news.php" class="text-gray-700 hover:text-purple-700">News</a>
    <a href="/Cinema/frontend/pages/profile.php" class="text-gray-700 hover:text-purple-700">Profile</a>
    <a href="/Cinema/frontend/pages/contact.php" class="text-gray-700 hover:text-purple-700">Contact</a>
  </nav>

  <div class="flex items-center space-x-4">
    <button type="button" class="text-gray-600 hover:text-purple-700" aria-label="Search">
      <i class="fas fa-search"></i>
    </button>
    <button type="button" class="text-gray-700 hover:text-purple-700">Login</button>
    <button type="button" class="bg-purple-700 text-white px-4 py-1 rounded-md hover:bg-purple-800">Sign Up</button>
  </div>
</header>
