<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../models/Walker.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['email']) || !isset($input['password'])) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        exit;
    }
    
    $email = trim($input['email']);
    $password = $input['password'];
    
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password cannot be empty']);
        exit;
    }
    
    $database = new Database();
    $db = $database->getConnection();
    $walker = new Walker($db);
    
    // Get walker by email
    $walkerData = $walker->getByEmail($email);
    
    if (!$walkerData) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }
    
    // Verify password
    if (password_verify($password, $walkerData['password'])) {
        // Set session variables
        $_SESSION['walker_id'] = $walkerData['id'];
        $_SESSION['walker_email'] = $walkerData['email'];
        $_SESSION['walker_name'] = $walkerData['name'];
        $_SESSION['is_admin'] = true;
        
        echo json_encode([
            'success' => true, 
            'message' => 'Login successful',
            'walker' => [
                'id' => $walkerData['id'],
                'name' => $walkerData['name'],
                'email' => $walkerData['email']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Login failed: ' . $e->getMessage()]);
}
?>