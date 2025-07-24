<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Check if walker is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'walker') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$walker_id = $_SESSION['user_id'];
$date = $_GET['date'] ?? 'today';

try {
      // Get database connection
    $db = getDatabaseConnection();
    if ($date === 'today') {
        $today = date('Y-m-d');
        
        // Get today's bookings count
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM bookings 
                               WHERE walker_id = ? AND booking_date = ? AND status != 'cancelled'");
        $stmt->execute([$walker_id, $today]);
        $bookings_result = $stmt->fetch(PDO::FETCH_ASSOC);
        $bookings_count = $bookings_result['count'];
        
        // Get today's earnings
        $stmt = $db->prepare("SELECT SUM(total_price) as earnings FROM bookings 
                               WHERE walker_id = ? AND booking_date = ? AND status = 'completed'");
        $stmt->execute([$walker_id, $today]);
        $earnings_result = $stmt->fetch(PDO::FETCH_ASSOC);
        $earnings = $earnings_result['earnings'] ?? 0;
        
        echo json_encode([
            'success' => true,
            'bookings_count' => $bookings_count,
            'earnings' => floatval($earnings)
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid date parameter']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>