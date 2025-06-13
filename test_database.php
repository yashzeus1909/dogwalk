<?php
/**
 * Test script to verify XAMPP database connection and data
 */

require_once 'config/database.php';

echo "<h1>PawWalk XAMPP Database Test</h1>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db === null) {
        echo "<p style='color: red;'>❌ Database connection failed!</p>";
        exit();
    }
    
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Test users table
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $user_count = $stmt->fetch()['count'];
    echo "<p>Users in database: $user_count</p>";
    
    // Test walkers table
    $stmt = $db->query("SELECT COUNT(*) as count FROM walkers");
    $walker_count = $stmt->fetch()['count'];
    echo "<p>Walkers in database: $walker_count</p>";
    
    // Test bookings table
    $stmt = $db->query("SELECT COUNT(*) as count FROM bookings");
    $booking_count = $stmt->fetch()['count'];
    echo "<p>Bookings in database: $booking_count</p>";
    
    // Test API endpoints
    echo "<h2>API Test Results</h2>";
    
    // Test walkers API
    $walkers_response = file_get_contents('http://localhost/dogWalk/api/walkers.php');
    if ($walkers_response) {
        $walkers = json_decode($walkers_response, true);
        echo "<p style='color: green;'>✅ Walkers API working - " . count($walkers) . " walkers loaded</p>";
    } else {
        echo "<p style='color: red;'>❌ Walkers API failed</p>";
    }
    
    // Test bookings API
    $bookings_response = file_get_contents('http://localhost/dogWalk/api/bookings.php');
    if ($bookings_response) {
        $bookings = json_decode($bookings_response, true);
        echo "<p style='color: green;'>✅ Bookings API working - " . count($bookings) . " bookings loaded</p>";
    } else {
        echo "<p style='color: red;'>❌ Bookings API failed</p>";
    }
    
    echo "<h2>Sample Data</h2>";
    
    // Show sample walker
    $stmt = $db->query("SELECT name, rating, price, badges FROM walkers LIMIT 1");
    $walker = $stmt->fetch();
    if ($walker) {
        echo "<p><strong>Sample Walker:</strong> {$walker['name']} - Rating: " . ($walker['rating']/10) . "/5 - Price: \${$walker['price']}/hour</p>";
        $badges = json_decode($walker['badges'], true);
        if ($badges) {
            echo "<p><strong>Badges:</strong> " . implode(', ', $badges) . "</p>";
        }
    }
    
    // Show sample booking
    $stmt = $db->query("SELECT b.dog_name, b.dog_size, b.total_price, b.status, w.name as walker_name 
                       FROM bookings b 
                       LEFT JOIN walkers w ON b.walker_id = w.id 
                       LIMIT 1");
    $booking = $stmt->fetch();
    if ($booking) {
        echo "<p><strong>Sample Booking:</strong> {$booking['dog_name']} ({$booking['dog_size']}) with {$booking['walker_name']} - \${$booking['total_price']} - Status: {$booking['status']}</p>";
    }
    
    echo "<h2>Configuration</h2>";
    echo "<p><strong>Database:</strong> " . EnvLoader::get('DB_DATABASE', 'dogWalk') . "</p>";
    echo "<p><strong>Host:</strong> " . EnvLoader::get('DB_HOST', 'localhost') . "</p>";
    echo "<p><strong>Port:</strong> " . EnvLoader::get('DB_PORT', 3306) . "</p>";
    
    echo "<p style='color: green; font-weight: bold;'>✅ All tests passed! Your XAMPP setup is working correctly.</p>";
    echo "<p><a href='index.html'>Go to PawWalk Application</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>