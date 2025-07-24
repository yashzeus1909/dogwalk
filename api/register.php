<?php
require_once 'config.php';

setJsonHeaders();

try {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $user_type = $_POST['user_type'] ?? 'customer';
    
    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($phone)) {
        throw new Exception('All fields except address are required');
    }
    
    // Validate password length
    if (strlen($password) < 6) {
        throw new Exception('Password must be at least 6 characters long');
    }
    
    // Check if email already exists (case-insensitive)
    if (isEmailExists($email)) {
        throw new Exception('Email address already exists');
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Get database connection
    $db = getDatabaseConnection();
    
    // Start transaction for walker registration
    $db->beginTransaction();
    
    try {
        if ($user_type === 'walker') {
            // Additional validation for walker fields
            $hourly_rate = $_POST['hourly_rate'] ?? '';
            $experience = $_POST['experience'] ?? '';
            $description = $_POST['description'] ?? '';
            $services = $_POST['services'] ?? [];
            $availability = $_POST['availability'] ?? [];
            
            if (empty($hourly_rate) || empty($experience) || empty($description)) {
                throw new Exception('Walker information fields are required');
            }
            
            if (empty($services)) {
                throw new Exception('Please select at least one service');
            }
            
            if (empty($availability)) {
                throw new Exception('Please select your availability');
            }
            
            // Convert arrays to JSON strings for database storage
            $services_json = json_encode($services);
            $availability_json = json_encode($availability);
            
            // Get checkbox values
            $background_check = isset($_POST['background_check']) ? 1 : 0;
            $insured = isset($_POST['insured']) ? 1 : 0;
            $certified = isset($_POST['certified']) ? 1 : 0;
            
            // Insert walker with walker-specific fields
            $sql = "INSERT INTO users (first_name, last_name, email, password, phone, address, role, 
                   price, description, availability, services, background_check, insured, certified, 
                   rating, review_count, distance, created_at, updated_at) 
                   VALUES (?, ?, ?, ?, ?, ?, 'walker', ?, ?, ?, ?, ?, ?, ?, 5.0, 0, 0, NOW(), NOW())";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $first_name, $last_name, $email, $hashed_password, $phone, $address,
                $hourly_rate, $description, $availability_json, $services_json,
                $background_check, $insured, $certified
            ]);
        } else {
            // Insert regular customer
            $sql = "INSERT INTO users (first_name, last_name, email, password, phone, address, role, created_at, updated_at) 
                   VALUES (?, ?, ?, ?, ?, ?, 'customer', NOW(), NOW())";
            $stmt = $db->prepare($sql);
            $stmt->execute([$first_name, $last_name, $email, $hashed_password, $phone, $address]);
        }
        
        $user_id = $db->lastInsertId();
        $db->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful',
            'user_id' => $user_id,
            'user_type' => $user_type
        ]);
        
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>