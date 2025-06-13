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
        
        if ($database_url && filter_var($database_url, FILTER_VALIDATE_URL)) {
            // Parse DATABASE_URL for MySQL
            $url = parse_url($database_url);
            
            // Validate parsed URL components
            if (!isset($url['host']) || !isset($url['path'])) {
                error_log("Invalid DATABASE_URL format: " . $database_url);
                $this->fallbackToIndividualVars();
                return;
            }
            
            $this->host = $url['host'];
            $this->port = isset($url['port']) ? $url['port'] : 3306;
            $this->db_name = ltrim($url['path'], '/');
            $this->username = isset($url['user']) ? $url['user'] : 'root';
            $this->password = isset($url['pass']) ? $url['pass'] : '';
        } else {
            // Fallback to individual environment variables
            $this->fallbackToIndividualVars();
        }
    }
    
    private function fallbackToIndividualVars() {
        $this->host = EnvLoader::get('DB_HOST', 'localhost');
        $this->port = EnvLoader::get('DB_PORT', 3306);
        $this->db_name = EnvLoader::get('DB_DATABASE', 'dogWalk');
        $this->username = EnvLoader::get('DB_USERNAME', 'root');
        $this->password = EnvLoader::get('DB_PASSWORD', '');
    }

    // Database connection
    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";
            
            // Log connection attempt details for debugging
            error_log("Attempting database connection to: {$this->host}:{$this->port}/{$this->db_name}");
            
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Test the connection
            $this->conn->query('SELECT 1');
            
        } catch(PDOException $exception) {
            $error_message = "Database connection failed: " . $exception->getMessage();
            $debug_info = "Host: {$this->host}, Port: {$this->port}, Database: {$this->db_name}, User: {$this->username}";
            
            error_log($error_message . " | " . $debug_info);
            
            // Return JSON error for API endpoints
            if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Database connection failed.',
                    'debug' => $debug_info,
                    'error_code' => $exception->getCode()
                ]);
            } else {
                echo $error_message;
            }
        }

        return $this->conn;
    }
}
?>