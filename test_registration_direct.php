<?php
header('Content-Type: application/json');

// Test data
$testData = [
    'firstName' => 'Test',
    'lastName' => 'User', 
    'email' => 'test@example.com',
    'password' => 'password123',
    'phone' => '1234567890',
    'address' => '123 Test St'
];

$firstName = $testData['firstName'];
$lastName = $testData['lastName'];
$email = $testData['email'];
$password = $testData['password'];
$phone = $testData['phone'];
$address = $testData['address'];

// Validate password length
if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long']);
    exit;
}

// Connect to PostgreSQL database
try {
    $host = getenv('PGHOST');
    $port = getenv('PGPORT');
    $dbname = getenv('PGDATABASE');
    $user = getenv('PGUSER');
    $dbpassword = getenv('PGPASSWORD');
    
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $db = new PDO($dsn, $user, $dbpassword);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if email already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email address already exists']);
        exit;
    }
    
    // Create user account
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("INSERT INTO users (first_name, last_name, email, phone, address, password, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW()) RETURNING id");
    $stmt->execute([$firstName, $lastName, $email, $phone, $address, $hashedPassword]);
    $userId = $stmt->fetchColumn();
    
    if ($userId) {
        echo json_encode([
            'success' => true,
            'message' => 'Account created successfully',
            'user_id' => $userId
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create account']);
    }
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>