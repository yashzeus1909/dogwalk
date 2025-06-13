<?php
// Direct database fix script
include_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        die("Database connection failed\n");
    }
    
    // Check current table structure
    $query = "SHOW COLUMNS FROM walkers";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $columns = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $columns[] = $row['Field'];
    }
    
    echo "Current columns: " . implode(', ', $columns) . "\n";
    
    // Check if email column exists
    if (!in_array('email', $columns)) {
        echo "Adding email column...\n";
        
        // Add email column
        $alter_query = "ALTER TABLE walkers ADD COLUMN email VARCHAR(255) NOT NULL DEFAULT '' AFTER name";
        $db->exec($alter_query);
        echo "Email column added!\n";
        
        // Update existing walkers with placeholder emails
        $update_query = "UPDATE walkers SET email = CONCAT('walker', id, '@example.com') WHERE email = ''";
        $db->exec($update_query);
        echo "Updated existing walkers with emails!\n";
        
    } else {
        echo "Email column already exists!\n";
    }
    
    // Test walker update query to identify the exact issue
    echo "\nTesting walker update query...\n";
    $test_query = "SELECT COUNT(*) as count FROM walkers WHERE email = ''";
    $test_stmt = $db->prepare($test_query);
    $test_stmt->execute();
    $result = $test_stmt->fetch();
    echo "Walkers with empty email: " . $result['count'] . "\n";
    
    echo "Database fix completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>