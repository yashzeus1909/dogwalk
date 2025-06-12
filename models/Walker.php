<?php
class Walker {
    private $conn;
    private $table_name = "walkers";

    public $id;
    public $name;
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
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY rating DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->name = $row['name'];
            $this->image = $row['image'];
            $this->rating = $row['rating'];
            $this->review_count = $row['review_count'];
            $this->distance = $row['distance'];
            $this->price = $row['price'];
            $this->description = $row['description'];
            $this->availability = $row['availability'];
            $this->badges = $row['badges'];
            $this->background_check = $row['background_check'];
            $this->insured = $row['insured'];
            $this->certified = $row['certified'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                SET name=:name, image=:image, rating=:rating, review_count=:review_count, 
                    distance=:distance, price=:price, description=:description, 
                    availability=:availability, badges=:badges, background_check=:background_check, 
                    insured=:insured, certified=:certified";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->rating = htmlspecialchars(strip_tags($this->rating));
        $this->review_count = htmlspecialchars(strip_tags($this->review_count));
        $this->distance = htmlspecialchars(strip_tags($this->distance));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->availability = htmlspecialchars(strip_tags($this->availability));
        $this->badges = htmlspecialchars(strip_tags($this->badges));

        $stmt->bindParam(":name", $this->name);
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

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function search($location = "", $service_type = "") {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        
        if(!empty($location)) {
            $query .= " AND (distance LIKE :location OR name LIKE :location)";
        }
        
        $query .= " ORDER BY rating DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if(!empty($location)) {
            $location = "%{$location}%";
            $stmt->bindParam(":location", $location);
        }
        
        $stmt->execute();
        return $stmt;
    }
}
?>