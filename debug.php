<?php
echo "<h1>Debug Information</h1>";

echo "<h2>Server Information:</h2>";
echo "<p><strong>REQUEST_URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p><strong>DOCUMENT_ROOT:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Current Directory:</strong> " . __DIR__ . "</p>";

echo "<h2>File Structure Test:</h2>";
$files = [
    'public/frontend/index.php',
    'public/frontend/pages/contact.php',
    'public/frontend/pages/login.php',
    'public/frontend/pages/register.php',
    'admin/index.php',
    'app/config/database.php'
];

foreach ($files as $file) {
    $exists = file_exists($file);
    echo "<p>" . ($exists ? "✅" : "❌") . " $file</p>";
}

echo "<h2>Direct Links Test:</h2>";
echo "<ul>";
echo "<li><a href='public/frontend/index.php'>Frontend Home</a></li>";
echo "<li><a href='public/frontend/pages/contact.php'>Contact Page</a></li>";
echo "<li><a href='public/frontend/pages/login.php'>Login Page</a></li>";
echo "<li><a href='public/frontend/pages/register.php'>Register Page</a></li>";
echo "<li><a href='admin/index.php'>Dashboard</a></li>";
echo "</ul>";
?>
