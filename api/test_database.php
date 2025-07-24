<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include_once '../config/database.php';

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
    
    $has_email = in_array('email', $columns);
    
    // If email column is missing, add it
    if (!$has_email) {
        // Add email column
        $alter_query = "ALTER TABLE walkers ADD COLUMN email VARCHAR(255) NOT NULL DEFAULT '' AFTER name";
        $alter_stmt = $db->prepare($alter_query);
        $alter_stmt->execute();
        
        // Add index
        $index_query = "CREATE INDEX idx_walkers_email ON walkers(email)";
        $index_stmt = $db->prepare($index_query);
        $index_stmt->execute();
        
        // Update existing walkers with placeholder emails
        $update_query = "UPDATE walkers SET email = CONCAT('walker', id, '@example.com') WHERE email = ''";
        $update_stmt = $db->prepare($update_query);
        $update_stmt->execute();
        
        $has_email = true;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Database schema checked and updated',
        'columns' => $columns,
        'has_email' => $has_email
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>