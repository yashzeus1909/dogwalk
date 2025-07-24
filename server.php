<?php
// PawWalk PHP Server
echo "Starting PawWalk PHP Server on port 5000...\n";

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test database connection
$host = getenv('PGHOST');
$port = getenv('PGPORT');
$dbname = getenv('PGDATABASE');
$user = getenv('PGUSER');
$password = getenv('PGPASSWORD');

if (!$host || !$port || !$dbname || !$user || !$password) {
    echo "Database configuration not found\n";
    echo "Required environment variables: PGHOST, PGPORT, PGDATABASE, PGUSER, PGPASSWORD\n";
    exit(1);
}

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful!\n";
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Start the server
echo "Server starting at http://0.0.0.0:5000\n";
$command = 'php -S 0.0.0.0:5000 start_php_server.php';
exec($command);
?>