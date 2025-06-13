<?php
require_once __DIR__ . '/env.php';

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    public $conn;

    public function __construct() {
        // Load environment configuration
        EnvLoader::load();
        
        // Get database configuration from environment variables
        $database_url = EnvLoader::get('DATABASE_URL');
        
        if ($database_url) {
            // Parse DATABASE_URL for MySQL
            $url = parse_url($database_url);
            $this->host = $url['host'];
            $this->port = isset($url['port']) ? $url['port'] : 3306;
            $this->db_name = ltrim($url['path'], '/');
            $this->username = $url['user'];
            $this->password = $url['pass'];
        } else {
            // Fallback to individual environment variables
            $this->host = EnvLoader::get('DB_HOST', 'localhost');
            $this->port = EnvLoader::get('DB_PORT', 3306);
            $this->db_name = EnvLoader::get('DB_DATABASE', 'pawwalk_db');
            $this->username = EnvLoader::get('DB_USERNAME', 'root');
            $this->password = EnvLoader::get('DB_PASSWORD', '');
        }
    }

    // Database connection
    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>