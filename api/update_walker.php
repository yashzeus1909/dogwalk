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
    $check_email_query = "SHOW COLUMNS FROM walkers LIKE 'email'";
    $check_email_stmt = $db->prepare($check_email_query);
    $check_email_stmt->execute();
    
    if ($check_email_stmt->rowCount() === 0) {
        // Add email column if it doesn't exist
        $alter_email_query = "ALTER TABLE walkers ADD COLUMN email VARCHAR(255) NOT NULL DEFAULT '' AFTER name";
        $db->exec($alter_email_query);
        
        // Update existing walkers with placeholder emails
        $update_email_query = "UPDATE walkers SET email = CONCAT('walker', id, '@example.com') WHERE email = ''";
        $db->exec($update_email_query);
    }
    
    // Check if password column exists in walkers table
    $check_password_query = "SHOW COLUMNS FROM walkers LIKE 'password'";
    $check_password_stmt = $db->prepare($check_password_query);
    $check_password_stmt->execute();
    
    if ($check_password_stmt->rowCount() === 0) {
        // Add password column if it doesn't exist
        $alter_password_query = "ALTER TABLE walkers ADD COLUMN password VARCHAR(255) NOT NULL DEFAULT '' AFTER email";
        $db->exec($alter_password_query);
    }
    
    $walker = new Walker($db);
    
    // Get walker ID
    $walker_id = isset($_POST['walker_id']) ? (int)$_POST['walker_id'] : 0;
    
    if (!$walker_id) {
        throw new Exception('Walker ID is required');
    }
    
    // Check if walker exists
    $walker->id = $walker_id;
    if (!$walker->readOne()) {
        throw new Exception('Walker not found');
    }
    
    // Validate required fields
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['price'])) {
        throw new Exception('Name, email, and price are required fields');
    }
    
    // Set walker properties
    $walker->name = trim($_POST['name']);
    $walker->email = trim($_POST['email']);
    $walker->password = !empty($_POST['password']) ? trim($_POST['password']) : null;
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
    
    // Check if email already exists for another walker
    $temp_walker = new Walker($db);
    if ($temp_walker->emailExists($walker->email)) {
        // Get the walker with this email
        $check_query = "SELECT id FROM walkers WHERE email = ? LIMIT 1";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(1, $walker->email);
        $check_stmt->execute();
        $existing_walker = $check_stmt->fetch(PDO::FETCH_ASSOC);
        
        // If the email belongs to a different walker, it's a duplicate
        if ($existing_walker && $existing_walker['id'] != $walker_id) {
            throw new Exception('Email address is already registered to another walker');
        }
    }
    
    // Validate password length if provided
    if ($walker->password && strlen($walker->password) < 6) {
        throw new Exception('Password must be at least 6 characters long');
    }
    
    // Validate image URL if provided
    if ($walker->image && !filter_var($walker->image, FILTER_VALIDATE_URL)) {
        throw new Exception('Invalid image URL format');
    }
    
    // Update the walker
    if ($walker->update()) {
        echo json_encode([
            'success' => true,
            'message' => 'Walker updated successfully',
            'walker_name' => $walker->name,
            'walker_id' => $walker->id
        ]);
    } else {
        throw new Exception('Failed to update walker in database');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>