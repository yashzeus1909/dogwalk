<?php
// Walkers API - fetch walkers from unified users table based on role
require_once 'config.php';

setJsonHeaders();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'config.php';

try {
    $db = getDatabaseConnection();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Fetch all walkers (users with role = 'walker')
        $stmt = $db->query("
            SELECT 
                id,
                (first_name || ' ' || last_name) as name,
                email,
                profile_image_url as image,
                rating,
                review_count,
                distance,
                price_per_hour as price,
                description,
                availability,
                badges,
                services,
                background_check,
                insured,
                certified
            FROM users 
            WHERE role = 'walker' AND is_active = 1
            ORDER BY rating DESC, review_count DESC
        ");
        
        $walkers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Convert JSON fields to arrays for frontend compatibility
        foreach ($walkers as &$walker) {
            $walker['badges'] = $walker['badges'] ? json_decode($walker['badges'], true) : [];
            $walker['services'] = $walker['services'] ? json_decode($walker['services'], true) : [];
            $walker['rating'] = (float) $walker['rating'];
            $walker['price'] = (float) $walker['price'];
            $walker['background_check'] = (bool) $walker['background_check'];
            $walker['insured'] = (bool) $walker['insured'];
            $walker['certified'] = (bool) $walker['certified'];
        }
        
        echo json_encode([
            'success' => true,
            'walkers' => $walkers,
            'count' => count($walkers)
        ]);
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Create new walker (register user with role = 'walker')
        $input = getJsonInput();
        
        $requiredFields = ['firstName', 'lastName', 'email', 'password', 'phone', 'pricePerHour'];
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
        $address = trim($input['address'] ?? '');
        $pricePerHour = floatval($input['pricePerHour']);
        $description = trim($input['description'] ?? '');
        $services = isset($input['services']) ? implode(',', $input['services']) : '';
        $availability = trim($input['availability'] ?? '');
        
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email format']);
            exit;
        }
        
        // Check if email already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email already registered']);
            exit;
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new walker
        $db->beginTransaction();
        try {
            $stmt = $db->prepare("
                INSERT INTO users (
                    first_name, last_name, email, password, phone, address, role,
                    price_per_hour, description, services, availability,
                    created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, 'walker', ?, ?, ?, ?, datetime('now'), datetime('now'))
            ");
            
            $result = $stmt->execute([
                $firstName, $lastName, $email, $hashedPassword, $phone, $address,
                $pricePerHour, $description, $services, $availability
            ]);
            
            if (!$result) {
                throw new Exception("Failed to create walker profile");
            }
            
            $walkerId = $db->lastInsertId();
            $db->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Walker registered successfully',
                'walkerId' => $walkerId
            ]);
            
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }
    
} catch (Exception $e) {
    error_log("Walkers API error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>