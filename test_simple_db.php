<?php
// Test PostgreSQL connection directly
$host = getenv('PGHOST');
$port = getenv('PGPORT');
$dbname = getenv('PGDATABASE');
$user = getenv('PGUSER');
$password = getenv('PGPASSWORD');

echo "Testing PostgreSQL connection...\n";
echo "Host: $host\n";
echo "Port: $port\n";
echo "Database: $dbname\n";
echo "User: $user\n";

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connection successful!\n";
    
    // Test if users table exists and has password column
    $stmt = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'users'");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Users table columns: " . implode(', ', $columns) . "\n";
    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>