<?php
require_once 'config/database.php';

// Test creating a new booking to verify data storage
try {
    $db = getDatabaseConnection();
    
    // Test data for a new booking
    $testBooking = [
        'walker_id' => 1,
        'customer_id' => 4, // Will create customer if needed
        'customer_name' => 'Test Customer',
        'customer_email' => 'test@example.com',
        'customer_phone' => '555-0123',
        'dog_name' => 'Test Dog',
        'dog_size' => 'medium',
        'booking_date' => '2025-01-25',
        'booking_time' => '14:00',
        'duration' => 1,
        'address' => '123 Test Street',
        'special_notes' => 'Test booking for data storage',
        'total_price' => 25.00,
        'status' => 'pending'
    ];
    
    echo "Testing booking creation...\n";
    
    // First, ensure we have a customer
    $stmt = $db->prepare("
        INSERT OR IGNORE INTO users (
            first_name, last_name, email, password, phone, role, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, 'customer', datetime('now'), datetime('now'))
    ");
    
    $stmt->execute([
        'Test', 'Customer', $testBooking['customer_email'], 
        password_hash('testpass', PASSWORD_DEFAULT), $testBooking['customer_phone']
    ]);
    
    // Get customer ID
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$testBooking['customer_email']]);
    $customer = $stmt->fetch();
    $customerId = $customer['id'];
    
    // Create booking
    $stmt = $db->prepare("
        INSERT INTO bookings (
            walker_id, customer_id, customer_name, customer_email, customer_phone,
            dog_name, dog_size, booking_date, booking_time, duration,
            address, special_notes, total_price, status, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, datetime('now'), datetime('now'))
    ");
    
    $result = $stmt->execute([
        $testBooking['walker_id'], $customerId, $testBooking['customer_name'],
        $testBooking['customer_email'], $testBooking['customer_phone'],
        $testBooking['dog_name'], $testBooking['dog_size'], $testBooking['booking_date'],
        $testBooking['booking_time'], $testBooking['duration'], $testBooking['address'],
        $testBooking['special_notes'], $testBooking['total_price'], $testBooking['status']
    ]);
    
    if ($result) {
        $bookingId = $db->lastInsertId();
        echo "✓ Booking created successfully! ID: $bookingId\n";
        
        // Verify booking was stored
        $stmt = $db->prepare("SELECT * FROM bookings WHERE id = ?");
        $stmt->execute([$bookingId]);
        $savedBooking = $stmt->fetch();
        
        if ($savedBooking) {
            echo "✓ Booking verified in database:\n";
            echo "  - Dog: {$savedBooking['dog_name']} ({$savedBooking['dog_size']})\n";
            echo "  - Date: {$savedBooking['booking_date']} at {$savedBooking['booking_time']}\n";
            echo "  - Price: \${$savedBooking['total_price']}\n";
            echo "  - Status: {$savedBooking['status']}\n";
        }
        
        // Show total bookings count
        $stmt = $db->query("SELECT COUNT(*) as count FROM bookings");
        $totalBookings = $stmt->fetch()['count'];
        echo "\nTotal bookings in database: $totalBookings\n";
        
    } else {
        echo "✗ Failed to create booking\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>