<?php
require_once 'config.php';

setJsonHeaders();
startSession();

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please log in to create a booking'
    ]);
    exit;
}

try {
    $walker_id = $_POST['walker_id'] ?? '';
    $dog_name = $_POST['dog_name'] ?? '';
    $dog_size = $_POST['dog_size'] ?? '';
    $booking_date = $_POST['booking_date'] ?? '';
    $booking_time = $_POST['booking_time'] ?? '';
    $duration = $_POST['duration'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $special_notes = $_POST['special_notes'] ?? '';
    
    // Validate required fields
    if (empty($walker_id) || empty($dog_name) || empty($dog_size) || 
        empty($booking_date) || empty($booking_time) || empty($duration) || 
        empty($phone) || empty($address)) {
        throw new Exception('All required fields must be filled');
    }
    
    // Get database connection
    $db = getDatabaseConnection();
    
    // Get walker price to calculate total
    $stmt = $db->prepare("SELECT price_per_hour FROM users WHERE id = ? AND role = 'walker'");
    $stmt->execute([$walker_id]);
    $walker = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$walker) {
        throw new Exception('Walker not found');
    }
    
    // Calculate total price based on duration
    $hourlyRate = $walker['price_per_hour'];
    $hours = $duration / 60;
    $total_price = $hourlyRate * $hours;
    
    // Insert booking with proper SQLite syntax
    $sql = "INSERT INTO bookings (walker_id, customer_id, dog_name, dog_size, booking_date, booking_time, duration, phone, address, special_notes, total_price, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', datetime('now'), datetime('now'))";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        $walker_id,
        $_SESSION['user_id'],
        $dog_name,
        $dog_size,
        $booking_date,
        $booking_time,
        $duration,
        $phone,
        $address,
        $special_notes,
        $total_price
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Booking created successfully',
        'booking_id' => $db->lastInsertId()
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>