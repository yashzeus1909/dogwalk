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
    
    // Check and add unique constraint on email if it doesn't exist
    $check_unique_query = "SHOW INDEX FROM walkers WHERE Key_name = 'email_unique'";
    $check_unique_stmt = $db->prepare($check_unique_query);
    $check_unique_stmt->execute();
    
    if ($check_unique_stmt->rowCount() === 0) {
        try {
            $unique_query = "ALTER TABLE walkers ADD CONSTRAINT email_unique UNIQUE (email)";
            $db->exec($unique_query);
        } catch (PDOException $e) {
            // If constraint fails due to duplicate emails, update them first
            $update_duplicates_query = "UPDATE walkers w1 JOIN (SELECT email, MIN(id) as min_id FROM walkers GROUP BY email HAVING COUNT(*) > 1) w2 ON w1.email = w2.email SET w1.email = CONCAT(w1.email, '_', w1.id) WHERE w1.id != w2.min_id";
            $db->exec($update_duplicates_query);
            
            // Try adding constraint again
            $db->exec($unique_query);
        }
    }
    
    $walker = new Walker($db);
    
    // Validate required fields
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['confirm_password']) || empty($_POST['price'])) {
        throw new Exception('Name, email, password, confirm password, and price are required fields');
    }
    
    // Check if passwords match
    if ($_POST['password'] !== $_POST['confirm_password']) {
        throw new Exception('Password and confirm password do not match');
    }
    
    // Set walker properties
    $walker->name = trim($_POST['name']);
    $walker->email = trim($_POST['email']);
    $walker->password = trim($_POST['password']);
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
    
    // Check if email already exists
    if ($walker->emailExists($walker->email)) {
        throw new Exception('Email address is already registered');
    }
    
    // Validate password length
    if (strlen($walker->password) < 6) {
        throw new Exception('Password must be at least 6 characters long');
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