<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['firstName']) || !isset($input['lastName']) || 
        !isset($input['email']) || !isset($input['password']) || !isset($input['phone'])) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    $firstName = trim($input['firstName']);
    $lastName = trim($input['lastName']);
    $email = trim($input['email']);
    $password = $input['password'];
    $phone = trim($input['phone']);
    $address = trim($input['address'] ?? '');
    
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($phone)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long']);
        exit;
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Check if email already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email address already exists']);
        exit;
    }
    
    // Create user account
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("INSERT INTO users (first_name, last_name, email, phone, address, password) VALUES (?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute([$firstName, $lastName, $email, $phone, $address, $hashedPassword]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Account created successfully',
            'user_id' => $db->lastInsertId()
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create account']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()]);
}
?>