<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

include_once '../config/database.php';
include_once '../models/Booking.php';
include_once '../models/Walker.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Create database connection
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }

    $booking = new Booking($db);
    $walker = new Walker($db);

    // Get walker ID from query parameters
    $walker_id = isset($_GET['walker_id']) ? (int)$_GET['walker_id'] : 0;
    $status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    
    if (!$walker_id) {
        throw new Exception('Walker ID is required');
    }

    // Verify walker exists
    $walker->id = $walker_id;
    if (!$walker->readOne()) {
        throw new Exception('Walker not found');
    }

    // Build query with optional status filter
    $query = "SELECT b.*, w.name as walker_name, w.image as walker_image,
                     u.first_name, u.last_name, u.email as customer_email
              FROM bookings b
              LEFT JOIN walkers w ON b.walker_id = w.id
              LEFT JOIN users u ON b.user_id = u.id
              WHERE b.walker_id = ?";
    
    $params = [$walker_id];
    
    if ($status_filter) {
        $query .= " AND b.status = ?";
        $params[] = $status_filter;
    }
    
    $query .= " ORDER BY b.booking_date DESC, b.booking_time DESC";
    $query .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    
    $bookings = [];
    $stats = [
        'pending' => 0,
        'confirmed' => 0,
        'in_progress' => 0,
        'completed' => 0,
        'cancelled' => 0,
        'total_earnings' => 0
    ];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $booking_data = [
            'id' => $row['id'],
            'walker_id' => $row['walker_id'],
            'user_id' => $row['user_id'],
            'dog_name' => $row['dog_name'],
            'dog_size' => $row['dog_size'],
            'booking_date' => $row['booking_date'],
            'booking_time' => $row['booking_time'],
            'duration' => $row['duration'],
            'phone' => $row['phone'],
            'email' => $row['email'],
            'address' => $row['address'],
            'special_notes' => $row['special_notes'],
            'total_price' => $row['total_price'],
            'status' => $row['status'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
            'customer_name' => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')),
            'customer_email' => $row['customer_email'] ?? $row['email'],
            'walker_name' => $row['walker_name'],
            'walker_image' => $row['walker_image']
        ];
        
        $bookings[] = $booking_data;
        
        // Update stats
        if (isset($stats[$row['status']])) {
            $stats[$row['status']]++;
        }
        
        if ($row['status'] === 'completed') {
            $stats['total_earnings'] += floatval($row['total_price']);
        }
    }

    // Get total count for pagination
    $count_query = "SELECT COUNT(*) as total FROM bookings WHERE walker_id = ?";
    $count_params = [$walker_id];
    
    if ($status_filter) {
        $count_query .= " AND status = ?";
        $count_params[] = $status_filter;
    }
    
    $count_stmt = $db->prepare($count_query);
    $count_stmt->execute($count_params);
    $total_count = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];

    echo json_encode([
        'success' => true,
        'bookings' => $bookings,
        'walker_info' => [
            'id' => $walker->id,
            'name' => $walker->name,
            'email' => $walker->email,
            'image' => $walker->image
        ],
        'stats' => $stats,
        'pagination' => [
            'total' => (int)$total_count,
            'limit' => $limit,
            'offset' => $offset,
            'has_more' => ($offset + $limit) < $total_count
        ],
        'filters' => [
            'status' => $status_filter ?: 'all'
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>