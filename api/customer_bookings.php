<?php
require_once 'config.php';

setJsonHeaders();
startSession();

// Check if user is authenticated
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

try {
    $db = getDatabaseConnection();
    $userId = $_SESSION['user_id'];
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get user's bookings - direct database query from unified users table
        $stmt = $db->prepare("
            SELECT b.*, u.first_name || ' ' || u.last_name as walker_name, u.image as walker_image, u.phone as walker_phone
            FROM bookings b 
            JOIN users u ON b.walker_id = u.id AND u.role = 'walker'
            WHERE b.customer_id = ? 
            ORDER BY b.created_at DESC
        ");
        $stmt->execute([$userId]);
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format bookings
        $formattedBookings = array_map(function($booking) {
            return [
                'id' => (int)$booking['id'],
                'walker_id' => (int)$booking['walker_id'],
                'walker_name' => $booking['walker_name'],
                'walker_image' => $booking['walker_image'],
                'walker_phone' => $booking['walker_phone'],
                'date' => $booking['booking_date'],
                'time' => $booking['booking_time'],
                'duration' => (int)$booking['duration'],
                'total_price' => (float)$booking['total_price'],
                'special_instructions' => $booking['special_instructions'],
                'status' => $booking['status'],
                'created_at' => $booking['created_at']
            ];
        }, $bookings);
        
        echo json_encode($formattedBookings);
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Create new booking
        $input = getJsonInput();
        
        $requiredFields = ['walker_id', 'date', 'time', 'duration', 'total_price'];
        foreach ($requiredFields as $field) {
            if (!isset($input[$field])) {
                echo json_encode(['success' => false, 'message' => ucfirst($field) . ' is required']);
                exit;
            }
        }
        
        // Direct database insert - use proper column names
        $stmt = $db->prepare("INSERT INTO bookings (customer_id, walker_id, booking_date, booking_time, duration, total_price, special_notes, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', datetime('now'), datetime('now'))");
        
        $stmt->execute([
            $userId,
            $input['walker_id'],
            $input['date'],
            $input['time'],
            $input['duration'],
            $input['total_price'],
            $input['special_instructions'] ?? ''
        ]);
        
        $bookingId = $db->lastInsertId();
        echo json_encode(['success' => true, 'message' => 'Booking created successfully', 'booking_id' => $bookingId]);
    }

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log("Booking error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>