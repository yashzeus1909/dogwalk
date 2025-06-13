<?php
/**
 * MySQL Database Setup Script for PawWalk Application
 * Automatically creates database and tables with sample data
 */

require_once __DIR__ . '/../config/env.php';

// Load environment variables
EnvLoader::load();

// Get database configuration
$host = EnvLoader::get('DB_HOST', 'localhost');
$port = EnvLoader::get('DB_PORT', 3306);
$database = EnvLoader::get('DB_DATABASE', 'pawwalk_db');
$username = EnvLoader::get('DB_USERNAME', 'root');
$password = EnvLoader::get('DB_PASSWORD', '');

echo "Setting up MySQL database for PawWalk application...\n";
echo "Host: $host:$port\n";
echo "Database: $database\n";
echo "Username: $username\n\n";

try {
    // First, connect without specifying database to create it if needed
    $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    echo "Creating database '$database' if it doesn't exist...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database created/verified\n";
    
    // Now connect to the specific database
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read and execute the SQL schema file
    $schema_file = __DIR__ . '/../database/mysql_schema.sql';
    if (!file_exists($schema_file)) {
        throw new Exception("Schema file not found: $schema_file");
    }
    
    echo "Reading schema file...\n";
    $sql = file_get_contents($schema_file);
    
    // Split SQL into individual statements and execute
    $statements = explode(';', $sql);
    
    echo "Executing database schema...\n";
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
    
    // Verify data was inserted
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $user_count = $stmt->fetch()['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM walkers");
    $walker_count = $stmt->fetch()['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings");
    $booking_count = $stmt->fetch()['count'];
    
    echo "\n✓ Database setup completed successfully!\n";
    echo "Created:\n";
    echo "- $user_count users\n";
    echo "- $walker_count walkers\n";
    echo "- $booking_count bookings\n\n";
    
    echo "You can now start the application with:\n";
    echo "php -S localhost:8000 server.php\n";
    
} catch (PDOException $e) {
    echo "❌ Database connection error: " . $e->getMessage() . "\n";
    echo "\nPlease check your database credentials in the .env file:\n";
    echo "- DB_HOST=$host\n";
    echo "- DB_PORT=$port\n";
    echo "- DB_DATABASE=$database\n";
    echo "- DB_USERNAME=$username\n";
    echo "- DB_PASSWORD=[hidden]\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Setup error: " . $e->getMessage() . "\n";
    exit(1);
}
?>