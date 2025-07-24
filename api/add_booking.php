<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// require_once '../config/database.php';
session_start();

try {
     // Get POST data from environment variable
    $inputData = getenv('POST_DATA') ?: getenv('HTTP_RAW_POST_DATA') ?: '';

    // If no environment data, try to read from stdin
    if (empty($inputData)) {
        $inputData = file_get_contents('php://stdin');
    }
    $input = json_decode($inputData, true);
    
    if (!$input) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        exit;
    }
    
    // Validate required fields
    $required_fields = ['walker_id', 'dog_name', 'dog_size', 'booking_date', 'booking_time', 
                       'duration', 'customer_name', 'customer_email', 'phone', 'address', 'total_price'];
    
    foreach ($required_fields as $field) {
        if (!isset($input[$field]) || empty(trim($input[$field]))) {
            echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
            exit;
        }
    }
    
    // $database = new Database();
    // $db = $database->getConnection();
    // Connect to MySQL database
    $host = getenv('DB_HOST') ?: 'localhost';
    $port = getenv('DB_PORT') ?: '3306';
    $dbname = getenv('DB_DATABASE') ?: 'dog_walker_app';
    $user = getenv('DB_USERNAME') ?: 'root';
    $password = getenv('DB_PASSWORD') ?: '';
    
    if (!$db) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    // Check if walker exists
    $stmt = $db->prepare("SELECT id FROM walkers WHERE id = ?");
    $stmt->execute([$input['walker_id']]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Walker not found']);
        exit;
    }
    
    // Handle user creation/lookup
    $user_id = null;
    
    // Check if user with this email already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$input['customer_email']]);
    $existing_user = $stmt->fetch();
    
    // Check if user is authenticated
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_email'])) {
        $user_id = $_SESSION['user_id'];
        $booking_email = $_SESSION['user_email'];
    } else {
        // Create new user record for guest booking
        $name_parts = explode(' ', trim($input['customer_name']), 2);
        $first_name = $name_parts[0];
        $last_name = isset($name_parts[1]) ? $name_parts[1] : '';
        
        $stmt = $db->prepare("INSERT INTO users (first_name, last_name, email, phone, address, password) VALUES (?, ?, ?, ?, ?, ?)");
        $temp_password = password_hash('temp_' . uniqid(), PASSWORD_DEFAULT); // Temporary password
        $result = $stmt->execute([$first_name, $last_name, $input['customer_email'], $input['phone'], $input['address'], $temp_password]);
        
        if ($result) {
            $user_id = $db->lastInsertId();
        }
    }
    
    // Create booking
    $stmt = $db->prepare("
        INSERT INTO bookings (walker_id, user_id, dog_name, dog_size, booking_date, booking_time, 
                             duration, phone, email, address, special_notes, total_price, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    
    $result = $stmt->execute([
        $input['walker_id'],
        $user_id,
        $input['dog_name'],
        $input['dog_size'],
        $input['booking_date'],
        $input['booking_time'],
        $input['duration'],
        $input['phone'],
        $booking_email,
        $input['address'],
        $input['special_notes'] ?? '',
        $input['total_price']
    ]);
    
    if ($result) {
        $booking_id = $db->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'Booking created successfully',
            'booking_id' => $booking_id,
            'status' => 'pending'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create booking']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>