<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Check if walker is logged in
if (!isset($_SESSION['walker_id']) || !isset($_SESSION['is_admin'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

require_once '../config/database.php';

try {
    $walker_id = $_SESSION['walker_id'];
    $status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Build the query
    $query = "SELECT b.*, u.name as customer_name, u.email as customer_email 
              FROM bookings b 
              LEFT JOIN users u ON b.user_id = u.id 
              WHERE b.walker_id = :walker_id";
    
    $params = [':walker_id' => $walker_id];
    
    if (!empty($status_filter)) {
        $query .= " AND b.status = :status";
        $params[':status'] = $status_filter;
    }
    
    $query .= " ORDER BY b.booking_date DESC, b.booking_time DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get stats
    $stats_query = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                    FROM bookings 
                    WHERE walker_id = :walker_id";
    
    $stats_stmt = $db->prepare($stats_query);
    $stats_stmt->execute([':walker_id' => $walker_id]);
    $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'bookings' => $bookings,
        'stats' => $stats
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error loading bookings: ' . $e->getMessage()]);
}
?>