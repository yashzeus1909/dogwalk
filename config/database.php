<?php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    public $conn;

    public function __construct() {
        // SQLite database file path
        $this->db_name = __DIR__ . '/../database/dog_walker_app.db';
    }

    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "sqlite:" . $this->db_name;
            $this->conn = new PDO($dsn);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Enable foreign keys for SQLite
            $this->conn->exec("PRAGMA foreign_keys = ON");
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
        }

        return $this->conn;
    }
}

// Helper function to get database connection
function getDatabaseConnection() {
    $database = new Database();
    return $database->getConnection();
}
?>