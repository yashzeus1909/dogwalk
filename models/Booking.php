<?php
class Booking {
    private $conn;
    private $table_name = "bookings";

    public $id;
    public $walker_id;
    public $user_id;
    public $dog_name;
    public $dog_size;
    public $booking_date;
    public $booking_time;
    public $duration;
    public $phone;
    public $email;
    public $instructions;
    public $service_fee;
    public $app_fee;
    public $total;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all bookings with walker information
    function read() {
        $query = "SELECT b.*, w.name as walker_name, w.image as walker_image,
                         u.first_name, u.last_name
                  FROM " . $this->table_name . " b
                  LEFT JOIN walkers w ON b.walker_id = w.id
                  LEFT JOIN users u ON b.user_id = u.id
                  ORDER BY b.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read bookings by user email
    function readByUser($email) {
        $query = "SELECT b.*, w.name as walker_name, w.image as walker_image,
                         u.first_name, u.last_name
                  FROM " . $this->table_name . " b
                  LEFT JOIN walkers w ON b.walker_id = w.id
                  LEFT JOIN users u ON b.user_id = u.id
                  WHERE b.email = :email
                  ORDER BY b.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt;
    }

    // Read bookings by walker
    function readByWalker($walker_id) {
        $query = "SELECT b.*, w.name as walker_name, w.image as walker_image,
                         u.first_name, u.last_name
                  FROM " . $this->table_name . " b
                  LEFT JOIN walkers w ON b.walker_id = w.id
                  LEFT JOIN users u ON b.user_id = u.id
                  WHERE b.walker_id = :walker_id
                  ORDER BY b.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":walker_id", $walker_id);
        $stmt->execute();
        return $stmt;
    }

    // Get single booking
    function readOne() {
        $query = "SELECT b.*, w.name as walker_name, w.image as walker_image,
                         u.first_name, u.last_name
                  FROM " . $this->table_name . " b
                  LEFT JOIN walkers w ON b.walker_id = w.id
                  LEFT JOIN users u ON b.user_id = u.id
                  WHERE b.id = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->walker_id = $row['walker_id'];
            $this->user_id = $row['user_id'];
            $this->dog_name = $row['dog_name'];
            $this->dog_size = $row['dog_size'];
            $this->booking_date = $row['booking_date'];
            $this->booking_time = $row['booking_time'];
            $this->duration = $row['duration'];
            $this->phone = $row['phone'];
            $this->email = $row['email'];
            $this->instructions = $row['instructions'];
            $this->service_fee = $row['service_fee'];
            $this->app_fee = $row['app_fee'];
            $this->total = $row['total'];
            $this->status = $row['status'];
            return true;
        }
        return false;
    }

    // Create booking
    function create() {
        // First, try to find or create user
        $user_id = $this->findOrCreateUser();
        
        $query = "INSERT INTO " . $this->table_name . " 
                SET walker_id=:walker_id, user_id=:user_id, dog_name=:dog_name, 
                    dog_size=:dog_size, booking_date=:booking_date, booking_time=:booking_time,
                    duration=:duration, phone=:phone, email=:email, instructions=:instructions,
                    service_fee=:service_fee, app_fee=:app_fee, total=:total, status=:status";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->dog_name = htmlspecialchars(strip_tags($this->dog_name));
        $this->dog_size = htmlspecialchars(strip_tags($this->dog_size));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->instructions = htmlspecialchars(strip_tags($this->instructions));

        // Bind data
        $stmt->bindParam(":walker_id", $this->walker_id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":dog_name", $this->dog_name);
        $stmt->bindParam(":dog_size", $this->dog_size);
        $stmt->bindParam(":booking_date", $this->booking_date);
        $stmt->bindParam(":booking_time", $this->booking_time);
        $stmt->bindParam(":duration", $this->duration);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":instructions", $this->instructions);
        $stmt->bindParam(":service_fee", $this->service_fee);
        $stmt->bindParam(":app_fee", $this->app_fee);
        $stmt->bindParam(":total", $this->total);
        $stmt->bindParam(":status", $this->status);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Update booking status
    function update() {
        $query = "UPDATE " . $this->table_name . " 
                SET status=:status, updated_at=CURRENT_TIMESTAMP
                WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->status = htmlspecialchars(strip_tags($this->status));

        // Bind data
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":status", $this->status);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete booking
    function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Find or create user based on email
    private function findOrCreateUser() {
        // Check if user exists
        $query = "SELECT id FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['id'];
        } else {
            // Create new user with basic info
            $query = "INSERT INTO users (first_name, last_name, email, phone) 
                     VALUES (:first_name, :last_name, :email, :phone)";
            $stmt = $this->conn->prepare($query);
            
            // Extract name from email or use defaults
            $email_parts = explode('@', $this->email);
            $name_part = $email_parts[0];
            $name_parts = explode('.', $name_part);
            
            $first_name = ucfirst($name_parts[0] ?? 'User');
            $last_name = ucfirst($name_parts[1] ?? 'Guest');
            
            $stmt->bindParam(":first_name", $first_name);
            $stmt->bindParam(":last_name", $last_name);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":phone", $this->phone);
            
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
        }
        return null;
    }
}
?>