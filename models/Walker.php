<?php
class Walker {
    private $conn;
    private $table_name = "walkers";

    public $id;
    public $name;
    public $email;
    public $image;
    public $rating;
    public $review_count;
    public $distance;
    public $price;
    public $description;
    public $availability;
    public $badges;
    public $background_check;
    public $insured;
    public $certified;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all walkers
    function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY rating DESC, review_count DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Search walkers
    function search($location, $service_type) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        
        if (!empty($location)) {
            $query .= " AND (LOWER(distance) LIKE LOWER(:location) OR LOWER(name) LIKE LOWER(:location))";
        }
        
        if (!empty($service_type) && $service_type !== 'All Services') {
            // Search in badges JSON field and description for service type
            $query .= " AND (JSON_SEARCH(LOWER(badges), 'one', LOWER(:service_type)) IS NOT NULL 
                        OR LOWER(description) LIKE LOWER(:service_desc)
                        OR (LOWER(:service_type) = 'dog walking' AND (LOWER(description) LIKE '%walk%' OR LOWER(badges) LIKE '%walk%'))
                        OR (LOWER(:service_type) = 'pet sitting' AND (LOWER(description) LIKE '%sit%' OR LOWER(badges) LIKE '%sitting%'))
                        OR (LOWER(:service_type) = 'pet boarding' AND LOWER(badges) LIKE '%boarding%')
                        OR (LOWER(:service_type) = 'doggy daycare' AND LOWER(badges) LIKE '%daycare%')
                        OR (LOWER(:service_type) = 'grooming' AND LOWER(badges) LIKE '%grooming%'))";
        }
        
        $query .= " ORDER BY rating DESC, review_count DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($location)) {
            $location_param = "%{$location}%";
            $stmt->bindParam(":location", $location_param);
        }
        
        if (!empty($service_type) && $service_type !== 'All Services') {
            $service_param = "%{$service_type}%";
            $service_desc_param = "%{$service_type}%";
            $stmt->bindParam(":service_type", $service_param);
            $stmt->bindParam(":service_desc", $service_desc_param);
        }
        
        $stmt->execute();
        return $stmt;
    }

    // Get single walker
    function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->image = $row['image'];
            $this->rating = $row['rating'];
            $this->review_count = $row['review_count'];
            $this->distance = $row['distance'];
            $this->price = $row['price'];
            $this->description = $row['description'];
            $this->availability = $row['availability'];
            // Handle JSON badges for MySQL
            $this->badges = $row['badges'] ? json_decode($row['badges'], true) : [];
            $this->background_check = $row['background_check'];
            $this->insured = $row['insured'];
            $this->certified = $row['certified'];
            return true;
        }
        return false;
    }

    // Create walker
    function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                (name, email, image, rating, review_count, distance, price, description, 
                 availability, badges, background_check, insured, certified) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->availability = htmlspecialchars(strip_tags($this->availability));

        // Convert badges array to JSON for MySQL
        $badges_json = is_array($this->badges) ? json_encode($this->badges) : $this->badges;
        
        // Execute with array of parameters
        $params = [
            $this->name,
            $this->email,
            $this->image,
            $this->rating,
            $this->review_count,
            $this->distance,
            $this->price,
            $this->description,
            $this->availability,
            $badges_json,
            $this->background_check,
            $this->insured,
            $this->certified
        ];

        try {
            if ($stmt->execute($params)) {
                return true;
            }
        } catch (PDOException $e) {
            error_log("Walker create error: " . $e->getMessage());
            throw $e;
        }
        return false;
    }

    // Update walker
    function update() {
        $query = "UPDATE " . $this->table_name . " 
                SET name = ?, email = ?, image = ?, rating = ?, review_count = ?,
                    distance = ?, price = ?, description = ?,
                    availability = ?, badges = ?, background_check = ?,
                    insured = ?, certified = ?
                WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->availability = htmlspecialchars(strip_tags($this->availability));

        // Convert badges array to JSON for MySQL
        $badges_json = is_array($this->badges) ? json_encode($this->badges) : $this->badges;

        // Execute with array of parameters
        $params = [
            $this->name,
            $this->email,
            $this->image,
            $this->rating,
            $this->review_count,
            $this->distance,
            $this->price,
            $this->description,
            $this->availability,
            $badges_json,
            $this->background_check,
            $this->insured,
            $this->certified,
            $this->id
        ];

        try {
            if ($stmt->execute($params)) {
                return true;
            }
        } catch (PDOException $e) {
            error_log("Walker update error: " . $e->getMessage());
            throw $e;
        }
        return false;
    }

    // Delete walker
    function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>