<?php
// MySQL configuration for PawWalk
// Uses direct MySQL database connection with environment variables

// Load environment variables from .env file
function loadEnvFile($filePath = '.env') {
    if (!file_exists($filePath)) {
        return false;
    }
    
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Skip comments
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        // Remove quotes if present
        if (preg_match('/^"(.*)"$/', $value, $matches)) {
            $value = $matches[1];
        } elseif (preg_match("/^'(.*)'$/", $value, $matches)) {
            $value = $matches[1];
        }
        
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
    }
    return true;
}

// Load .env file at startup
loadEnvFile(__DIR__ . '/../.env');

// Set JSON response headers
function setJsonHeaders() {
    header('Content-Type: application/json');
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
}

// Start PHP session
function startSession() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

// Get JSON input from request body
function getJsonInput() {
    $rawInput = file_get_contents('php://input');
    
    if (empty($rawInput)) {
        // Try $_POST as fallback
        if (!empty($_POST)) {
            return $_POST;
        }
        return [];
    }
    
    $input = json_decode($rawInput, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error: " . json_last_error_msg());
        return [];
    }
    
    return $input ?: [];
}

// Database connection function - Uses direct MySQL database connection
function getDatabaseConnection() {
    try {
        // Get database connection details from environment variables
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $dbname = $_ENV['DB_DATABASE'] ?? '';
        $user = $_ENV['DB_USERNAME'] ?? $_ENV['DB_USER'] ?? '';
        $password = $_ENV['DB_PASSWORD'] ?? '';
        
        // Debug connection parameters (remove in production)
        error_log("MySQL Connection - Host: $host, Port: $port, DB: $dbname, User: $user");
        
        if (empty($dbname) || empty($user)) {
            throw new Exception("Missing required database credentials. Please set DB_HOST, DB_DATABASE, DB_USER, and DB_PASSWORD environment variables.");
        }
        
        // Construct DSN for MySQL
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]);
        
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection error: " . $e->getMessage());
        throw new Exception("Database connection failed: " . $e->getMessage());
    }
}

// Test database connection
function testDatabaseConnection() {
    try {
        $db = getDatabaseConnection();
        return ['success' => true, 'message' => 'Direct MySQL database connection successful'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// ====== DIRECT DATABASE OPERATIONS ONLY ======
// All operations query database tables directly - NO file storage, NO JSON files, NO external data

// CREATE Operations
function createUser($userData) {
    $db = getDatabaseConnection();
    $sql = "INSERT INTO users (first_name, last_name, email, password, phone, address, role, 
            price_per_hour, description, services, availability, rating, review_count, 
            background_check, insured, certified) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($sql);
    $result = $stmt->execute([
        $userData['first_name'], $userData['last_name'], $userData['email'], 
        $userData['password'], $userData['phone'], $userData['address'], $userData['role'],
        $userData['price_per_hour'] ?? null, $userData['description'] ?? null, 
        $userData['services'] ?? null, $userData['availability'] ?? null,
        $userData['rating'] ?? 0, $userData['review_count'] ?? 0,
        $userData['background_check'] ?? 0, $userData['insured'] ?? 0, $userData['certified'] ?? 0
    ]);
    
    return $result ? $db->lastInsertId() : false;
}

function createBooking($bookingData) {
    $db = getDatabaseConnection();
    $sql = "INSERT INTO bookings (walker_id, customer_id, dog_name, dog_size, booking_date, 
            booking_time, duration, phone, address, special_notes, total_price, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
    
    $stmt = $db->prepare($sql);
    $result = $stmt->execute([
        $bookingData['walker_id'], $bookingData['customer_id'], $bookingData['dog_name'],
        $bookingData['dog_size'], $bookingData['booking_date'], $bookingData['booking_time'],
        $bookingData['duration'], $bookingData['phone'], $bookingData['address'],
        $bookingData['special_notes'], $bookingData['total_price']
    ]);
    
    return $result ? $db->lastInsertId() : false;
}

// READ Operations
function getUserById($id) {
    $db = getDatabaseConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Direct database user lookup - no file storage
function getUserByEmail($email) {
    $db = getDatabaseConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE LOWER(email) = LOWER(?)");
    $stmt->execute([$email]);
    return $stmt->fetch();
}

function getAllWalkers($filters = []) {
    $db = getDatabaseConnection();
    $sql = "SELECT * FROM users WHERE role = 'walker'";
    $params = [];
    
    if (!empty($filters['service'])) {
        $sql .= " AND services LIKE ?";
        $params[] = '%' . $filters['service'] . '%';
    }
    
    if (!empty($filters['min_price']) && !empty($filters['max_price'])) {
        $sql .= " AND price_per_hour BETWEEN ? AND ?";
        $params[] = $filters['min_price'];
        $params[] = $filters['max_price'];
    }
    
    $sql .= " ORDER BY rating DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getBookingById($id) {
    $db = getDatabaseConnection();
    $stmt = $db->prepare("SELECT * FROM bookings WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getBookingsByUser($userId, $role = 'customer') {
    $db = getDatabaseConnection();
    $column = ($role === 'walker') ? 'walker_id' : 'customer_id';
    $stmt = $db->prepare("SELECT * FROM bookings WHERE $column = ? ORDER BY booking_date DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function getAllBookings() {
    $db = getDatabaseConnection();
    $stmt = $db->query("SELECT * FROM bookings ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

// UPDATE Operations
function updateUser($id, $userData) {
    $db = getDatabaseConnection();
    $setParts = [];
    $params = [];
    
    foreach ($userData as $field => $value) {
        if ($field !== 'id') {
            $setParts[] = "$field = ?";
            $params[] = $value;
        }
    }
    
    $setParts[] = "updated_at = NOW()";
    $params[] = $id;
    
    $sql = "UPDATE users SET " . implode(', ', $setParts) . " WHERE id = ?";
    $stmt = $db->prepare($sql);
    return $stmt->execute($params);
}

function updateBooking($id, $bookingData) {
    $db = getDatabaseConnection();
    $setParts = [];
    $params = [];
    
    foreach ($bookingData as $field => $value) {
        if ($field !== 'id') {
            $setParts[] = "$field = ?";
            $params[] = $value;
        }
    }
    
    $setParts[] = "updated_at = NOW()";
    $params[] = $id;
    
    $sql = "UPDATE bookings SET " . implode(', ', $setParts) . " WHERE id = ?";
    $stmt = $db->prepare($sql);
    return $stmt->execute($params);
}

function updateBookingStatus($id, $status) {
    $db = getDatabaseConnection();
    $stmt = $db->prepare("UPDATE bookings SET status = ?, updated_at = NOW() WHERE id = ?");
    return $stmt->execute([$status, $id]);
}

// DELETE Operations
function deleteUser($id) {
    $db = getDatabaseConnection();
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    return $stmt->execute([$id]);
}

function deleteBooking($id) {
    $db = getDatabaseConnection();
    $stmt = $db->prepare("DELETE FROM bookings WHERE id = ?");
    return $stmt->execute([$id]);
}

// AUTHENTICATION Operations
function authenticateUser($email, $password) {
    $user = getUserByEmail($email);
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

// Direct database email validation - no file storage
function isEmailExists($email) {
    $db = getDatabaseConnection();
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE LOWER(email) = LOWER(?)");
    $stmt->execute([$email]);
    $result = $stmt->fetch();
    return $result['count'] > 0;
}
?>