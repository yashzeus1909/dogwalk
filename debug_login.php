<?php
// Debug login process step by step
require_once 'api/config.php';

echo "=== LOGIN DEBUG TEST ===\n";

// Test 1: Database connection
echo "1. Testing database connection...\n";
try {
    $db = getDatabaseConnection();
    echo "   ✓ Database connected\n";
} catch (Exception $e) {
    echo "   ✗ Database error: " . $e->getMessage() . "\n";
    exit;
}

// Test 2: Check if test user exists
echo "2. Checking test user...\n";
$email = "simple.test@example.com";
$password = "password123";

$stmt = $db->prepare("SELECT id, first_name, last_name, email, password FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "   ✓ User found: " . $user['first_name'] . " " . $user['last_name'] . "\n";
    
    // Test 3: Password verification
    echo "3. Testing password verification...\n";
    if (password_verify($password, $user['password'])) {
        echo "   ✓ Password verified\n";
    } else {
        echo "   ✗ Password verification failed\n";
    }
} else {
    echo "   ✗ User not found\n";
}

// Test 4: Simulate the exact login API call
echo "4. Simulating login API call...\n";
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['CONTENT_TYPE'] = 'application/json';

// Simulate the JSON input that would come from the frontend
$jsonData = json_encode(['email' => $email, 'password' => $password]);
echo "   Input JSON: " . $jsonData . "\n";

// Test JSON parsing
$input = json_decode($jsonData, true);
if (isset($input['email']) && isset($input['password'])) {
    echo "   ✓ JSON parsed correctly\n";
    echo "   Email: " . $input['email'] . "\n";
    echo "   Password: " . $input['password'] . "\n";
} else {
    echo "   ✗ JSON parsing failed\n";
}

echo "=== END DEBUG TEST ===\n";
?>