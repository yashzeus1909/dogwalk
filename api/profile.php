<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Use MySQL database for profile management
require_once 'mysql_config.php';
error_log("PROFILE API: Using MySQL database (dog_walker_app)");

try {
    $pdo = getDatabaseConnection();
    error_log("Profile API: Database connection successful");
} catch(Exception $e) {
    error_log("Profile API: Database connection failed - " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get user profile
    $stmt = $pdo->prepare("SELECT id, \"firstName\", \"lastName\", email, role, address, \"profileImageUrl\" FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo json_encode($user);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Update user profile
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
        exit;
    }
    
    $firstName = trim($input['firstName'] ?? '');
    $lastName = trim($input['lastName'] ?? '');
    $phone = trim($input['phone'] ?? '');
    $address = trim($input['address'] ?? '');
    $profileImageUrl = trim($input['profileImageUrl'] ?? '');
    
    // Validate required fields
    if (empty($firstName) || empty($lastName)) {
        echo json_encode(['success' => false, 'message' => 'First name and last name are required']);
        exit;
    }
    
    // Start transaction for data integrity
    $pdo->beginTransaction();
    try {
        // Update user profile in database
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, phone = ?, address = ?, profile_image_url = ?, updated_at = NOW() WHERE id = ?");
        $result = $stmt->execute([$firstName, $lastName, $phone, $address, $profileImageUrl, $user_id]);
        
        if (!$result) {
            throw new Exception("Failed to update user profile");
        }
        
        // Verify the update was successful
        $stmt = $pdo->prepare("SELECT first_name, last_name, phone, address, profile_image_url, updated_at FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $updatedUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$updatedUser) {
            throw new Exception("Profile update verification failed");
        }
        
        // Commit the transaction
        $pdo->commit();
        
        // Log successful update
        error_log("Profile updated successfully for user ID: $user_id");
        
        // Update session data
        $_SESSION['user_name'] = $firstName . ' ' . $lastName;
        
        // Return success response with updated data
        echo json_encode([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => [
                'firstName' => $updatedUser['first_name'],
                'lastName' => $updatedUser['last_name'],
                'phone' => $updatedUser['phone'],
                'address' => $updatedUser['address'],
                'profileImageUrl' => $updatedUser['profile_image_url'],
                'updatedAt' => $updatedUser['updated_at']
            ]
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollback();
        error_log("Profile update failed for user ID $user_id: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Profile update failed: ' . $e->getMessage()
        ]);
    }
    $email = $input['email'] ?? '';
    $address = $input['address'] ?? '';
    $profileImageUrl = $input['profileImageUrl'] ?? '';
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET \"firstName\" = ?, \"lastName\" = ?, email = ?, address = ?, \"profileImageUrl\" = ? WHERE id = ?");
        $stmt->execute([$firstName, $lastName, $email, $address, $profileImageUrl, $user_id]);
        
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Update failed: ' . $e->getMessage()]);
    }
}
?>