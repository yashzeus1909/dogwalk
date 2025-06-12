<?php
/**
 * Database Setup Script for PawWalk Application
 * Run this script to initialize the PostgreSQL database with sample data
 */

require_once __DIR__ . '/../config/database.php';

function setupDatabase() {
    echo "Setting up PawWalk database...\n";
    
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        if (!$conn) {
            throw new Exception("Could not connect to database. Please check your configuration.");
        }
        
        echo "✓ Connected to PostgreSQL database\n";
        
        // Read and execute schema file
        $schema_file = __DIR__ . '/../database/schema.sql';
        if (!file_exists($schema_file)) {
            throw new Exception("Schema file not found: " . $schema_file);
        }
        
        $schema_sql = file_get_contents($schema_file);
        
        // Split by semicolons and execute each statement
        $statements = array_filter(array_map('trim', explode(';', $schema_sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^\s*--/', $statement)) {
                $conn->exec($statement);
            }
        }
        
        echo "✓ Database schema created successfully\n";
        echo "✓ Sample data inserted\n";
        
        // Verify setup
        $stmt = $conn->query("SELECT COUNT(*) as walker_count FROM walkers");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "✓ Found " . $result['walker_count'] . " walkers in database\n";
        
        $stmt = $conn->query("SELECT COUNT(*) as booking_count FROM bookings");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "✓ Found " . $result['booking_count'] . " bookings in database\n";
        
        echo "\n🎉 Database setup completed successfully!\n";
        echo "You can now start the application with: php -S localhost:8000 server.php\n";
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        echo "\nPlease check:\n";
        echo "1. PostgreSQL is installed and running\n";
        echo "2. Database credentials are correct\n";
        echo "3. Database exists and user has proper permissions\n";
        exit(1);
    }
}

// Check if script is run from command line
if (php_sapi_name() === 'cli') {
    setupDatabase();
} else {
    echo "This script should be run from the command line.\n";
    echo "Usage: php scripts/setup_database.php\n";
}
?>