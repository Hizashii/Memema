<?php
// Simple test page to verify the system is working
echo "<h1>Cinema System Test</h1>";
echo "<p>If you can see this page, your server is working!</p>";

// Test database connection
try {
    require_once 'app/config/database.php';
    require_once 'app/core/database.php';
    
    $result = executeQuery("SELECT COUNT(*) as count FROM movies");
    echo "<p>✅ Database connection successful! Movies in database: " . $result[0]['count'] . "</p>";
} catch (Exception $e) {
    echo "<p>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

echo "<h2>Available Pages:</h2>";
echo "<ul>";
echo "<li><a href='public/frontend/index.php'>Frontend Home</a></li>";
echo "<li><a href='public/frontend/pages/contact.php'>Contact Page</a></li>";
echo "<li><a href='public/frontend/pages/login.php'>User Login</a></li>";
echo "<li><a href='public/frontend/pages/register.php'>User Registration</a></li>";
echo "<li><a href='admin/index.php'>Admin Dashboard</a></li>";
echo "<li><a href='admin/login.php'>Admin Login</a></li>";
echo "</ul>";

echo "<h2>Test Credentials:</h2>";
echo "<p><strong>Admin Login:</strong> username: admin, password: admin123</p>";
echo "<p><strong>Test User:</strong> email: john@example.com, password: password</p>";
?>
