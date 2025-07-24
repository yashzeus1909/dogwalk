<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please log in to view bookings'
    ]);
    exit;
}

try {
    $sql = "SELECT b.*, CONCAT(u.first_name, ' ', u.last_name) as walker_name 
            FROM bookings b 
            JOIN users u ON b.walker_id = u.id 
            WHERE b.customer_id = ? 
            ORDER BY b.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'bookings' => $bookings
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching bookings: ' . $e->getMessage()
    ]);
}
?>