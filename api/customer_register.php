<?php
// Use Replit PostgreSQL database since XAMPP MySQL is not available
require_once 'config.php';
error_log("REGISTRATION: Using MySQL database (dog_walker_app) for data storage");

setJsonHeaders();
startSession();

try {
    // Get JSON input from request body
    $input = getJsonInput();
    
    $requiredFields = ['firstName', 'lastName', 'email', 'password', 'phone', 'address'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty(trim($input[$field]))) {
            echo json_encode(['success' => false, 'message' => ucfirst($field) . ' is required']);
            exit;
        }
    }
    
    $firstName = trim($input['firstName']);
    $lastName = trim($input['lastName']);
    $email = trim($input['email']);
    $password = $input['password'];
    $phone = trim($input['phone']);
    $address = trim($input['address']);
    $role = trim($input['role'] ?? 'customer');
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }
    
    // Validate password length
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long']);
        exit;
    }
    
    // Connect to database
    $db = getDatabaseConnection();
    
    // Check if user already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit;
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user with transaction for data integrity
    $db->beginTransaction();
    try {
        $stmt = $db->prepare("INSERT INTO users (first_name, last_name, email, password, phone, address, role, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $result = $stmt->execute([$firstName, $lastName, $email, $hashedPassword, $phone, $address, $role]);
        
        if (!$result) {
            throw new Exception("Failed to insert user data into database");
        }
        
        // Get the new user ID
        $userId = $db->lastInsertId();
        
        // Verify the user was actually inserted
        $stmt = $db->prepare("SELECT id, first_name, last_name, email, role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $newUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$newUser) {
            throw new Exception("User registration verification failed");
        }
        
        // Commit the transaction
        $db->commit();
        
        // Log successful registration
        error_log("User registered successfully - ID: $userId, Email: $email, Role: $role");
        
        // Set session for immediate login
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $firstName . ' ' . $lastName;
        $_SESSION['user_role'] = $role;
        $_SESSION['logged_in'] = true;
        
        // Return success response with user data
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful! You are now logged in.',
            'user' => [
                'id' => $userId,
                'name' => $firstName . ' ' . $lastName,
                'email' => $email,
                'role' => $role
            ],
            'redirect' => $role === 'admin' ? '/admin_dashboard.php' : 
                         ($role === 'walker' ? '/walker_dashboard.php' : '/customer_profile.html')
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on any error
        $db->rollback();
        error_log("Registration transaction failed: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Database transaction failed: ' . $e->getMessage()
        ]);
    }
    
} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Registration failed: ' . $e->getMessage()
    ]);
}
?>