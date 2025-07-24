<?php
// Setup SQLite database for the Dog Walker application

echo "Setting up SQLite database...\n";

require_once 'config/database.php';

try {
    // Create database directory if it doesn't exist
    $dbDir = dirname(__DIR__ . '/database/dog_walker_app.db');
    if (!is_dir($dbDir)) {
        mkdir($dbDir, 0755, true);
    }

    // Get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Could not connect to database');
    }
    
    echo "Connected to SQLite database.\n";
    
    // Read and execute the schema
    $schema = file_get_contents('database/sqlite_schema.sql');
    
    // Split by semicolon and execute each statement
    $statements = explode(';', $schema);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                $db->exec($statement);
            } catch (PDOException $e) {
                // Skip errors for statements that might already exist
                if (strpos($e->getMessage(), 'already exists') === false && 
                    strpos($e->getMessage(), 'UNIQUE constraint failed') === false) {
                    echo "Warning: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    echo "Database setup completed successfully!\n";
    
    // Check if data was inserted
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'walker'");
    $result = $stmt->fetch();
    echo "Found " . $result['count'] . " walkers in the database.\n";
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM bookings");
    $result = $stmt->fetch();
    echo "Found " . $result['count'] . " bookings in the database.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>