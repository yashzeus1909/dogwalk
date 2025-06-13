<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include_once 'config/database.php';

try {
    // Create database instance
    $database = new Database();
    
    // Get environment variables for debugging
    $env_debug = [
        'DATABASE_URL' => getenv('DATABASE_URL') ?: 'Not set',
        'DB_HOST' => getenv('DB_HOST') ?: 'Not set',
        'DB_PORT' => getenv('DB_PORT') ?: 'Not set',
        'DB_DATABASE' => getenv('DB_DATABASE') ?: 'Not set',
        'DB_USERNAME' => getenv('DB_USERNAME') ?: 'Not set',
        'DB_PASSWORD' => getenv('DB_PASSWORD') ? '[HIDDEN]' : 'Not set'
    ];
    
    // Attempt connection
    $db = $database->getConnection();
    
    if ($db) {
        // Test query
        $stmt = $db->query("SELECT VERSION() as mysql_version");
        $version = $stmt->fetch();
        
        echo json_encode([
            'success' => true,
            'message' => 'Database connection successful',
            'mysql_version' => $version['mysql_version'],
            'environment' => $env_debug
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database connection returned null',
            'environment' => $env_debug
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Connection test failed: ' . $e->getMessage(),
        'environment' => isset($env_debug) ? $env_debug : []
    ]);
}
?>