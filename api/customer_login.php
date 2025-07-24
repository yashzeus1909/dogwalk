<?php
// Use MySQL database for authentication
require_once 'mysql_config.php';
error_log("LOGIN: Using MySQL database (dog_walker_app) for authentication");

setJsonHeaders();
startSession();

try {
    // Get JSON input from request body
    $input = getJsonInput();
    
    if (!isset($input['email']) || !isset($input['password'])) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        exit;
    }
    
    $email = trim($input['email']);
    $inputPassword = $input['password'];
    
    if (empty($email) || empty($inputPassword)) {
        echo json_encode(['success' => false, 'message' => 'Email and password cannot be empty']);
        exit;
    }
    
    // Connect to database
    $db = getDatabaseConnection();
    
    // Check if user exists
    $stmt = $db->prepare("SELECT id, first_name, last_name, email, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }
    
    // Verify password
    if (!password_verify($inputPassword, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['logged_in'] = true;
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'user' => [
            'id' => $user['id'],
            'name' => $user['first_name'] . ' ' . $user['last_name'],
            'email' => $user['email']
        ]
    ]);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred during login']);
}
?>