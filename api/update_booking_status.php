<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Check if walker is logged in
if (!isset($_SESSION['walker_id']) || !isset($_SESSION['is_admin'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $walker_id = $_SESSION['walker_id'];
    
    // Initialize database connection
    $database = new Database();
    $pdo = $database->getConnection();
    
    if (!$pdo) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['booking_id']) || !isset($input['status'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields: booking_id and status']);
        exit;
    }
    
    $bookingId = (int)$input['booking_id'];
    $newStatus = trim($input['status']);
    
    // Validate status
    $validStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
    if (!in_array($newStatus, $validStatuses)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit;
    }
    
    // Check if booking exists and belongs to this walker
    $checkStmt = $pdo->prepare("SELECT id, status, walker_id FROM bookings WHERE id = ?");
    $checkStmt->execute([$bookingId]);
    $booking = $checkStmt->fetch();
    
    if (!$booking) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        exit;
    }
    
    // Verify that this booking belongs to the logged-in walker
    if ($booking['walker_id'] != $walker_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'You can only update your own bookings']);
        exit;
    }
    
    // Update booking status
    $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $result = $stmt->execute([$newStatus, $bookingId]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Booking status updated successfully',
            'booking_id' => $bookingId,
            'old_status' => $booking['status'],
            'new_status' => $newStatus
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update booking status']);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>