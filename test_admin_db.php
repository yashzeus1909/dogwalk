<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        echo "Database connection failed\n";
        exit(1);
    }
    
    echo "Database connected successfully\n";
    
    // Check if tables exist
    $tables = $db->query("SHOW TABLES")->fetchAll();
    echo "Available tables:\n";
    foreach ($tables as $table) {
        echo "- " . array_values($table)[0] . "\n";
    }
    
    // Check users table structure
    $userTableExists = false;
    foreach ($tables as $table) {
        if (in_array('users', array_values($table))) {
            $userTableExists = true;
            break;
        }
    }
    
    if (!$userTableExists) {
        echo "\nCreating users table...\n";
        $db->exec("
            CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                phone VARCHAR(20),
                address TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        
        // Insert sample users
        $db->exec("
            INSERT INTO users (first_name, last_name, email, phone, address) VALUES
            ('John', 'Doe', 'john.doe@example.com', '555-0123', '123 Main St, Downtown'),
            ('Jane', 'Smith', 'jane.smith@example.com', '555-0456', '456 Oak Ave, Midtown'),
            ('Mike', 'Johnson', 'mike.johnson@example.com', '555-0789', '789 Pine Rd, Uptown')
        ");
        echo "Users table created with sample data\n";
    } else {
        $stmt = $db->query("SELECT COUNT(*) as count FROM users");
        $count = $stmt->fetch()['count'];
        echo "\nUsers table exists with $count records\n";
    }
    
    // Check bookings table structure
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll();
    echo "\nBookings table columns:\n";
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
    }
    
    // Test the admin query
    echo "\nTesting admin query...\n";
    $query = "SELECT b.*, 
                     b.email as customer_email,
                     b.phone as customer_phone,
                     '' as customer_name
              FROM bookings b 
              LIMIT 1";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result) {
        echo "Query successful - Sample booking: " . ($result['customer_name'] ?: 'No name') . "\n";
    } else {
        echo "No bookings found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>