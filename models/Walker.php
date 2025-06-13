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
                SET name=:name, email=:email, image=:image, rating=:rating, review_count=:review_count,
                    distance=:distance, price=:price, description=:description,
                    availability=:availability, badges=:badges, background_check=:background_check,
                    insured=:insured, certified=:certified";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->availability = htmlspecialchars(strip_tags($this->availability));

        // Convert badges array to JSON for MySQL
        $badges_json = is_array($this->badges) ? json_encode($this->badges) : $this->badges;
        
        // Bind data
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":rating", $this->rating);
        $stmt->bindParam(":review_count", $this->review_count);
        $stmt->bindParam(":distance", $this->distance);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":availability", $this->availability);
        $stmt->bindParam(":badges", $badges_json);
        $stmt->bindParam(":background_check", $this->background_check);
        $stmt->bindParam(":insured", $this->insured);
        $stmt->bindParam(":certified", $this->certified);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Update walker
    function update() {
        $query = "UPDATE " . $this->table_name . " 
                SET name=:name, email=:email, image=:image, rating=:rating, review_count=:review_count,
                    distance=:distance, price=:price, description=:description,
                    availability=:availability, badges=:badges, background_check=:background_check,
                    insured=:insured, certified=:certified
                WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->availability = htmlspecialchars(strip_tags($this->availability));

        // Bind data
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":rating", $this->rating);
        $stmt->bindParam(":review_count", $this->review_count);
        $stmt->bindParam(":distance", $this->distance);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":availability", $this->availability);
        $stmt->bindParam(":badges", $this->badges);
        $stmt->bindParam(":background_check", $this->background_check);
        $stmt->bindParam(":insured", $this->insured);
        $stmt->bindParam(":certified", $this->certified);

        if ($stmt->execute()) {
            return true;
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