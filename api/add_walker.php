<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include_once '../config/database.php';
include_once '../models/Walker.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
    
    // Check if email column exists in walkers table
    $check_query = "SHOW COLUMNS FROM walkers LIKE 'email'";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->execute();
    
    if ($check_stmt->rowCount() === 0) {
        // Add email column if it doesn't exist
        $alter_query = "ALTER TABLE walkers ADD COLUMN email VARCHAR(255) NOT NULL DEFAULT '' AFTER name";
        $db->exec($alter_query);
        
        // Update existing walkers with placeholder emails
        $update_query = "UPDATE walkers SET email = CONCAT('walker', id, '@example.com') WHERE email = ''";
        $db->exec($update_query);
    }
    
    $walker = new Walker($db);
    
    // Validate required fields
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['price'])) {
        throw new Exception('Name, email, and price are required fields');
    }
    
    // Set walker properties
    $walker->name = trim($_POST['name']);
    $walker->email = trim($_POST['email']);
    $walker->price = (int)$_POST['price'];
    $walker->image = !empty($_POST['image']) ? trim($_POST['image']) : null;
    $walker->distance = !empty($_POST['distance']) ? trim($_POST['distance']) : null;
    $walker->description = !empty($_POST['description']) ? trim($_POST['description']) : null;
    $walker->availability = !empty($_POST['availability']) ? trim($_POST['availability']) : null;
    
    // Handle rating (convert to integer for database storage)
    $rating = !empty($_POST['rating']) ? (float)$_POST['rating'] : 0;
    $walker->rating = (int)($rating * 10); // Convert 4.8 to 48
    
    $walker->review_count = !empty($_POST['review_count']) ? (int)$_POST['review_count'] : 0;
    
    // Handle service badges
    $badges = [];
    if (isset($_POST['badges']) && is_array($_POST['badges'])) {
        $badges = $_POST['badges'];
    }
    $walker->badges = json_encode($badges);
    
    // Handle certifications
    $walker->background_check = isset($_POST['background_check']) ? 1 : 0;
    $walker->insured = isset($_POST['insured']) ? 1 : 0;
    $walker->certified = isset($_POST['certified']) ? 1 : 0;
    
    // Validate price
    if ($walker->price < 1) {
        throw new Exception('Price must be at least $1');
    }
    
    // Validate rating
    if ($rating < 0 || $rating > 5) {
        throw new Exception('Rating must be between 0 and 5');
    }
    
    // Validate email format
    if (!filter_var($walker->email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }
    
    // Validate image URL if provided
    if ($walker->image && !filter_var($walker->image, FILTER_VALIDATE_URL)) {
        throw new Exception('Invalid image URL format');
    }
    
    // Create the walker
    if ($walker->create()) {
        echo json_encode([
            'success' => true,
            'message' => 'Walker added successfully',
            'walker_name' => $walker->name,
            'walker_id' => $walker->id ?? null
        ]);
    } else {
        throw new Exception('Failed to create walker in database');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>