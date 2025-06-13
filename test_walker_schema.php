<?php
// Test script to check walker table schema and add email column if missing

include_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    
    // Check if walkers table exists and get its structure
    $query = "DESCRIBE walkers";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $columns = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $columns[] = $row['Field'];
    }
    
    echo "Current walkers table columns:\n";
    print_r($columns);
    
    // Check if email column exists
    if (!in_array('email', $columns)) {
        echo "\nEmail column missing. Adding it...\n";
        
        $alter_query = "ALTER TABLE walkers ADD COLUMN email VARCHAR(255) NOT NULL DEFAULT '' AFTER name";
        $alter_stmt = $db->prepare($alter_query);
        
        if ($alter_stmt->execute()) {
            echo "Email column added successfully!\n";
            
            // Add index for email
            $index_query = "CREATE INDEX idx_walkers_email ON walkers(email)";
            $index_stmt = $db->prepare($index_query);
            $index_stmt->execute();
            echo "Email index added!\n";
            
            // Update existing walkers with placeholder emails
            $update_query = "UPDATE walkers SET email = CONCAT('walker', id, '@example.com') WHERE email = ''";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->execute();
            echo "Updated existing walkers with placeholder emails!\n";
            
        } else {
            echo "Failed to add email column\n";
        }
    } else {
        echo "\nEmail column already exists!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>