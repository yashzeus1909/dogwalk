<?php
class Booking {
    private $conn;
    private $table_name = "bookings";

    public $id;
    public $walker_id;
    public $dog_name;
    public $dog_size;
    public $date;
    public $time;
    public $duration;
    public $phone;
    public $email;
    public $instructions;
    public $service_fee;
    public $app_fee;
    public $total;
    public $status;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT b.*, w.name as walker_name, w.image as walker_image 
                  FROM " . $this->table_name . " b 
                  LEFT JOIN walkers w ON b.walker_id = w.id 
                  ORDER BY b.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT b.*, w.name as walker_name, w.image as walker_image 
                  FROM " . $this->table_name . " b 
                  LEFT JOIN walkers w ON b.walker_id = w.id 
                  WHERE b.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->walker_id = $row['walker_id'];
            $this->dog_name = $row['dog_name'];
            $this->dog_size = $row['dog_size'];
            $this->date = $row['date'];
            $this->time = $row['time'];
            $this->duration = $row['duration'];
            $this->phone = $row['phone'];
            $this->email = $row['email'];
            $this->instructions = $row['instructions'];
            $this->service_fee = $row['service_fee'];
            $this->app_fee = $row['app_fee'];
            $this->total = $row['total'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                SET walker_id=:walker_id, dog_name=:dog_name, dog_size=:dog_size, 
                    date=:date, time=:time, duration=:duration, phone=:phone, 
                    email=:email, instructions=:instructions, service_fee=:service_fee, 
                    app_fee=:app_fee, total=:total, status=:status";

        $stmt = $this->conn->prepare($query);

        $this->dog_name = htmlspecialchars(strip_tags($this->dog_name));
        $this->dog_size = htmlspecialchars(strip_tags($this->dog_size));
        $this->date = htmlspecialchars(strip_tags($this->date));
        $this->time = htmlspecialchars(strip_tags($this->time));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->instructions = htmlspecialchars(strip_tags($this->instructions));
        $this->status = htmlspecialchars(strip_tags($this->status));

        $stmt->bindParam(":walker_id", $this->walker_id);
        $stmt->bindParam(":dog_name", $this->dog_name);
        $stmt->bindParam(":dog_size", $this->dog_size);
        $stmt->bindParam(":date", $this->date);
        $stmt->bindParam(":time", $this->time);
        $stmt->bindParam(":duration", $this->duration);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":instructions", $this->instructions);
        $stmt->bindParam(":service_fee", $this->service_fee);
        $stmt->bindParam(":app_fee", $this->app_fee);
        $stmt->bindParam(":total", $this->total);
        $stmt->bindParam(":status", $this->status);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                SET status=:status 
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->status = htmlspecialchars(strip_tags($this->status));

        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readByUser($email) {
        $query = "SELECT b.*, w.name as walker_name, w.image as walker_image 
                  FROM " . $this->table_name . " b 
                  LEFT JOIN walkers w ON b.walker_id = w.id 
                  WHERE b.email = ? 
                  ORDER BY b.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();
        return $stmt;
    }
}
?>