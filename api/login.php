<?php
require_once 'config.php';

setJsonHeaders();
startSession();

try {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        throw new Exception('Email and password are required');
    }
    
    $db = getDatabaseConnection();
    // Direct database query - case insensitive email lookup
    $stmt = $db->prepare("SELECT id, first_name, last_name, email, password, role FROM users WHERE LOWER(email) = LOWER(?)");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || !password_verify($password, $user['password'])) {
        throw new Exception('Invalid email or password');
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'role' => $user['role'],
        'user_id' => $user['id']
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>