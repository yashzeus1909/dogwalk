<?php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    public $conn;

    public function __construct() {
        // Get database configuration from environment variables
        $database_url = getenv('DATABASE_URL');
        
        if ($database_url) {
            // Parse DATABASE_URL for PostgreSQL
            $url = parse_url($database_url);
            $this->host = $url['host'];
            $this->port = isset($url['port']) ? $url['port'] : 5432;
            $this->db_name = ltrim($url['path'], '/');
            $this->username = $url['user'];
            $this->password = $url['pass'];
        } else {
            // Fallback to individual environment variables
            $this->host = getenv('PGHOST') ?: 'localhost';
            $this->port = getenv('PGPORT') ?: 5432;
            $this->db_name = getenv('PGDATABASE') ?: 'pawwalk_db';
            $this->username = getenv('PGUSER') ?: 'postgres';
            $this->password = getenv('PGPASSWORD') ?: '';
        }
    }

    // Database connection
    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db_name}";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>