<?php
// Script to update walker data with proper service badges for filtering
include_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db === null) {
        die("Database connection failed.");
    }
    
    // Clear existing walkers
    $clear_query = "DELETE FROM walkers";
    $db->exec($clear_query);
    
    // Insert walkers with proper service badges
    $walkers = [
        [
            'name' => 'Sarah Johnson',
            'image' => 'https://images.unsplash.com/photo-1494790108755-2616b332c371?w=400&h=400&fit=crop&crop=face',
            'rating' => 48,
            'review_count' => 127,
            'distance' => '0.5 miles',
            'price' => 25,
            'description' => 'Experienced professional dog walker with 5+ years caring for dogs of all sizes. Specializes in pet sitting and boarding services.',
            'availability' => 'Mon-Fri 9am-5pm',
            'badges' => '["Verified", "Insured", "Pet Sitting", "Pet Boarding"]',
            'background_check' => 1,
            'insured' => 1,
            'certified' => 1
        ],
        [
            'name' => 'Mike Chen',
            'image' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop&crop=face',
            'rating' => 46,
            'review_count' => 89,
            'distance' => '1.2 miles',
            'price' => 22,
            'description' => 'Part-time dog walker specializing in small breeds and puppies. Available for walking services.',
            'availability' => 'Weekends and evenings',
            'badges' => '["Verified", "Dog Walking"]',
            'background_check' => 0,
            'insured' => 0,
            'certified' => 1
        ],
        [
            'name' => 'Emma Rodriguez',
            'image' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&h=400&fit=crop&crop=face',
            'rating' => 49,
            'review_count' => 203,
            'distance' => '0.8 miles',
            'price' => 30,
            'description' => 'Professional pet care specialist with behavioral training expertise. Offers grooming and daycare services.',
            'availability' => 'Daily 6am-8pm',
            'badges' => '["Verified", "Insured", "Background Checked", "Grooming", "Doggy Daycare"]',
            'background_check' => 1,
            'insured' => 1,
            'certified' => 1
        ],
        [
            'name' => 'David Thompson',
            'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&crop=face',
            'rating' => 47,
            'review_count' => 156,
            'distance' => '1.5 miles',
            'price' => 28,
            'description' => 'Full-service pet care provider offering boarding and daycare services for busy pet owners.',
            'availability' => 'Mon-Sat 7am-7pm',
            'badges' => '["Verified", "Pet Boarding", "Doggy Daycare", "Background Checked"]',
            'background_check' => 1,
            'insured' => 1,
            'certified' => 1
        ]
    ];
    
    $insert_query = "INSERT INTO walkers (name, image, rating, review_count, distance, price, description, availability, badges, background_check, insured, certified) 
                     VALUES (:name, :image, :rating, :review_count, :distance, :price, :description, :availability, :badges, :background_check, :insured, :certified)";
    
    $stmt = $db->prepare($insert_query);
    
    foreach ($walkers as $walker) {
        $stmt->execute($walker);
    }
    
    echo "Walker data updated successfully with proper service badges!\n";
    echo "Added " . count($walkers) . " walkers with service filtering capabilities.\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>