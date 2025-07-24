<?php
try {
    $db = new PDO('sqlite:database/dog_walker_app.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DATABASE SCHEMA CHECK ===\n\n";
    
    // Check users table structure
    $result = $db->query("PRAGMA table_info(users)");
    echo "USERS TABLE COLUMNS:\n";
    while ($column = $result->fetch()) {
        echo "- {$column['name']} ({$column['type']})\n";
    }
    
    echo "\n";
    
    // Check bookings table structure
    $result = $db->query("PRAGMA table_info(bookings)");
    echo "BOOKINGS TABLE COLUMNS:\n";
    while ($column = $result->fetch()) {
        echo "- {$column['name']} ({$column['type']})\n";
    }
    
    echo "\n=== SAMPLE DATA ===\n";
    
    // Show sample walker
    $stmt = $db->query("SELECT id, first_name, last_name, role, price_per_hour FROM users WHERE role='walker' LIMIT 1");
    $walker = $stmt->fetch();
    if ($walker) {
        echo "Sample Walker: {$walker['first_name']} {$walker['last_name']} (\${$walker['price_per_hour']}/hour)\n";
    }
    
    // Show sample booking structure
    $stmt = $db->query("SELECT * FROM bookings LIMIT 1");
    $booking = $stmt->fetch();
    if ($booking) {
        echo "\nSample Booking columns:\n";
        foreach ($booking as $key => $value) {
            if (!is_numeric($key)) {
                echo "- $key: $value\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>