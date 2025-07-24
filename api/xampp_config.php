<?php
// XAMPP-specific configuration for PawWalk
// Use this config for local XAMPP development

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
    
    // Debug logging for XAMPP
    error_log("XAMPP - Raw input: " . $rawInput);
    error_log("XAMPP - Content type: " . ($_SERVER['CONTENT_TYPE'] ?? 'not set'));
    error_log("XAMPP - Request method: " . $_SERVER['REQUEST_METHOD']);
    
    if (empty($rawInput)) {
        // Try $_POST as fallback
        if (!empty($_POST)) {
            return $_POST;
        }
        return [];
    }
    
    $input = json_decode($rawInput, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("XAMPP - JSON decode error: " . json_last_error_msg());
        return [];
    }
    
    return $input ?: [];
}

// XAMPP MySQL Database connection
function getDatabaseConnection() {
    try {
        // XAMPP MySQL configuration
        $host = 'localhost';
        $port = '3306';
        $dbname = 'dog_walker_app'; // You may need to create this database
        $username = 'root';
        $password = ''; // Default XAMPP MySQL password is empty
        
        // Build DSN for MySQL
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        
        error_log("XAMPP - Connecting to MySQL: $dsn with user: $username");
        
        // Create PDO connection
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 10,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]);
        
        error_log("XAMPP - MySQL connection successful");
        return $pdo;
        
    } catch (PDOException $e) {
        error_log("XAMPP - Database connection failed: " . $e->getMessage());
        
        // Try to create database if it doesn't exist
        if (strpos($e->getMessage(), 'Unknown database') !== false) {
            try {
                $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $username, $password);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS pawwalk_db");
                $pdo->exec("USE pawwalk_db");
                
                // Create users table
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS users (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        first_name VARCHAR(100) NOT NULL,
                        last_name VARCHAR(100) NOT NULL,
                        email VARCHAR(255) NOT NULL UNIQUE,
                        password VARCHAR(255) NOT NULL,
                        phone VARCHAR(20),
                        address TEXT,
                        role VARCHAR(20) DEFAULT 'customer',
                        profile_image_url VARCHAR(500),
                        is_active BOOLEAN DEFAULT TRUE,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )
                ");
                
                // Create walkers table
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS walkers (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(100) NOT NULL,
                        email VARCHAR(255) NOT NULL UNIQUE,
                        password VARCHAR(255) NOT NULL,
                        image VARCHAR(500),
                        rating INT DEFAULT 0,
                        review_count INT DEFAULT 0,
                        distance VARCHAR(50),
                        price INT NOT NULL,
                        description TEXT,
                        availability VARCHAR(100),
                        badges JSON,
                        services JSON,
                        background_check BOOLEAN DEFAULT FALSE,
                        insured BOOLEAN DEFAULT FALSE,
                        certified BOOLEAN DEFAULT FALSE,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )
                ");
                
                // Create bookings table
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS bookings (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        walker_id INT NOT NULL,
                        user_id INT,
                        dog_name VARCHAR(100) NOT NULL,
                        dog_size VARCHAR(20) NOT NULL,
                        booking_date VARCHAR(20) NOT NULL,
                        booking_time VARCHAR(20) NOT NULL,
                        duration INT NOT NULL,
                        phone VARCHAR(20) NOT NULL,
                        address TEXT NOT NULL,
                        special_notes TEXT,
                        total_price DECIMAL(10,2) NOT NULL,
                        status VARCHAR(20) DEFAULT 'pending',
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )
                ");
                
                error_log("XAMPP - Database and tables created successfully");
                return $pdo;
                
            } catch (PDOException $createError) {
                error_log("XAMPP - Failed to create database: " . $createError->getMessage());
                throw new Exception("Database setup failed: " . $createError->getMessage());
            }
        }
        
        throw new Exception("Database connection failed: " . $e->getMessage());
    }
}

// Test database connection
function testDatabaseConnection() {
    try {
        $db = getDatabaseConnection();
        return ['success' => true, 'message' => 'Database connection successful'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
?>