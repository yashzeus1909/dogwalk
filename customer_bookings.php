<?php
session_start();
header('Content-Type: application/json');

// Check if customer is authenticated
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_customer'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

try {
    // Connect to PostgreSQL database
    $host = getenv('DB_HOST') ?: getenv('PGHOST') ?: 'localhost';
    $port = getenv('DB_PORT') ?: getenv('PGPORT') ?: '5432';
    $dbname = getenv('DB_DATABASE') ?: getenv('PGDATABASE') ?: 'dog_walker_app';
    $user = getenv('DB_USERNAME') ?: getenv('PGUSER') ?: 'postgres';
    $password = getenv('DB_PASSWORD') ?: getenv('PGPASSWORD') ?: '';
    
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $db = new PDO($dsn, $user, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $userId = $_SESSION['user_id'];
    
    // Get customer's bookings with walker information
    $stmt = $db->prepare("
        SELECT 
            b.*,
            w.name as walker_name,
            w.phone as walker_phone,
            w.profile_image as walker_image
        FROM bookings b
        LEFT JOIN walkers w ON b.walker_id = w.id
        WHERE b.user_id = ?
        ORDER BY b.created_at DESC
    ");
    
    $stmt->execute([$userId]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'bookings' => $bookings
    ]);
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>