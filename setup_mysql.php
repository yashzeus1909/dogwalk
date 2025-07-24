<?php
// Setup MySQL database for the Dog Walker application

echo "Setting up MySQL database...\n";

try {
    // Connect to MySQL server (without specific database)
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to MySQL server.\n";
    
    // Read and execute the schema
    $schema = file_get_contents('database/setup_schema.sql');
    
    // Split by semicolon and execute each statement
    $statements = explode(';', $schema);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                // Skip errors for statements that might already exist
                if (strpos($e->getMessage(), 'already exists') === false && 
                    strpos($e->getMessage(), 'Duplicate') === false) {
                    echo "Warning: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    echo "Database setup completed successfully!\n";
    
    // Test the connection with the new database
    $db = new PDO('mysql:host=localhost;dbname=dog_walker_app', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if data was inserted
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'walker'");
    $result = $stmt->fetch();
    echo "Found " . $result['count'] . " walkers in the database.\n";
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM bookings");
    $result = $stmt->fetch();
    echo "Found " . $result['count'] . " bookings in the database.\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>