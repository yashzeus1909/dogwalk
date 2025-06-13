<?php
/**
 * XAMPP MySQL Database Setup Script for PawWalk Application
 * Automatically creates database and tables with sample data for XAMPP environment
 */

require_once __DIR__ . '/../config/env.php';

// Load environment variables
EnvLoader::load();

// Get database configuration for XAMPP
$host = EnvLoader::get('DB_HOST', 'localhost');
$port = EnvLoader::get('DB_PORT', 3306);
$database = EnvLoader::get('DB_DATABASE', 'dogWalk');
$username = EnvLoader::get('DB_USERNAME', 'root');
$password = EnvLoader::get('DB_PASSWORD', '');

echo "Setting up XAMPP MySQL database for PawWalk application...\n";
echo "Host: $host:$port\n";
echo "Database: $database\n";
echo "Username: $username\n\n";

try {
    // Connect to MySQL without specifying database
    $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Connected to XAMPP MySQL server\n";
    
    // Read and execute the XAMPP schema file
    $schema_file = __DIR__ . '/../database/xampp_mysql_schema.sql';
    if (!file_exists($schema_file)) {
        throw new Exception("XAMPP schema file not found: $schema_file");
    }
    
    echo "Reading XAMPP schema file...\n";
    $sql = file_get_contents($schema_file);
    
    // Split SQL into individual statements and execute
    $statements = explode(';', $sql);
    
    echo "Creating database and tables...\n";
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                // Skip errors for DROP statements and comments
                if (strpos($e->getMessage(), 'Unknown table') === false && 
                    strpos($statement, 'DROP') === false) {
                    echo "Warning: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    echo "✓ Database schema executed successfully\n";
    
    // Connect to the specific database to verify data
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verify data was inserted
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $user_count = $stmt->fetch()['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM walkers");
    $walker_count = $stmt->fetch()['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings");
    $booking_count = $stmt->fetch()['count'];
    
    echo "\n✓ XAMPP Database setup completed successfully!\n";
    echo "Created in database '$database':\n";
    echo "- $user_count users\n";
    echo "- $walker_count walkers\n";
    echo "- $booking_count bookings\n\n";
    
    echo "Next steps:\n";
    echo "1. Place your project files in C:/xampp/htdocs/dogWalk/\n";
    echo "2. Start Apache and MySQL in XAMPP Control Panel\n";
    echo "3. Open browser and go to: http://localhost/dogWalk/\n";
    
} catch (PDOException $e) {
    echo "❌ Database connection error: " . $e->getMessage() . "\n";
    echo "\nTroubleshooting for XAMPP:\n";
    echo "1. Make sure XAMPP MySQL service is running\n";
    echo "2. Check XAMPP Control Panel\n";
    echo "3. Default XAMPP MySQL settings:\n";
    echo "   - Host: localhost\n";
    echo "   - Port: 3306\n";
    echo "   - Username: root\n";
    echo "   - Password: (empty)\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Setup error: " . $e->getMessage() . "\n";
    exit(1);
}
?>