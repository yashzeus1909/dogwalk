<?php
try {
    $db = new PDO('sqlite:database/dog_walker_app.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check walkers
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE role='walker'");
    $walkerCount = $stmt->fetch()['count'];
    echo "Walkers in database: $walkerCount\n";
    
    // Show first few walkers
    $stmt = $db->query("SELECT id, first_name, last_name, role, price_per_hour FROM users WHERE role='walker' LIMIT 3");
    while ($row = $stmt->fetch()) {
        echo "- {$row['first_name']} {$row['last_name']} (${$row['price_per_hour']}/hour)\n";
    }
    
    // Check customers
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE role='customer'");
    $customerCount = $stmt->fetch()['count'];
    echo "\nCustomers in database: $customerCount\n";
    
    // Show bookings
    $stmt = $db->query("SELECT COUNT(*) as count FROM bookings");
    $bookingCount = $stmt->fetch()['count'];
    echo "Bookings in database: $bookingCount\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>