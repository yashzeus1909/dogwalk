<?php
include_once '../config/database.php';
include_once '../models/Walker.php';

header('Content-Type: application/json');

// Create database connection
$database = new Database();
$db = $database->getConnection();
$walker = new Walker($db);

// Sample walkers to add
$sampleWalkers = [
    [
        'name' => 'Emma Thompson',
        'image' => 'https://images.unsplash.com/photo-1494790108755-2616b332c371?w=400&h=400&fit=crop&crop=face',
        'rating' => 48, // 4.8 stars
        'review_count' => 156,
        'distance' => '0.5 miles',
        'price' => 32,
        'description' => 'Certified dog trainer with 5+ years experience. Specializes in behavioral training and exercise.',
        'availability' => 'Mon-Fri 6am-8pm, Weekends 8am-6pm',
        'badges' => '["Verified", "Certified", "Dog Walking", "Training", "Insured"]',
        'background_check' => 1,
        'insured' => 1,
        'certified' => 1
    ],
    [
        'name' => 'Marcus Rodriguez',
        'image' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop&crop=face',
        'rating' => 46, // 4.6 stars
        'review_count' => 89,
        'distance' => '1.2 miles',
        'price' => 28,
        'description' => 'Professional pet sitter offering overnight care and daily walks. Great with senior dogs.',
        'availability' => 'Flexible schedule, 7 days a week',
        'badges' => '["Verified", "Pet Sitting", "Pet Boarding", "Background Checked"]',
        'background_check' => 1,
        'insured' => 1,
        'certified' => 0
    ],
    [
        'name' => 'Lily Chen',
        'image' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&h=400&fit=crop&crop=face',
        'rating' => 50, // 5.0 stars
        'review_count' => 203,
        'distance' => '0.8 miles',
        'price' => 35,
        'description' => 'Full-service pet care including grooming, daycare, and walking. Licensed facility.',
        'availability' => 'Mon-Sat 7am-7pm',
        'badges' => '["Verified", "Grooming", "Doggy Daycare", "Pet Sitting", "Certified"]',
        'background_check' => 1,
        'insured' => 1,
        'certified' => 1
    ],
    [
        'name' => 'Jake Morrison',
        'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&crop=face',
        'rating' => 44, // 4.4 stars
        'review_count' => 67,
        'distance' => '2.1 miles',
        'price' => 24,
        'description' => 'Part-time walker specializing in large breed dogs. Available evenings and weekends.',
        'availability' => 'Evenings 5pm-9pm, Weekends all day',
        'badges' => '["Dog Walking", "Pet Sitting"]',
        'background_check' => 0,
        'insured' => 1,
        'certified' => 0
    ]
];

$addedWalkers = [];
$errors = [];

foreach ($sampleWalkers as $walkerData) {
    // Set walker properties
    $walker->name = $walkerData['name'];
    $walker->image = $walkerData['image'];
    $walker->rating = $walkerData['rating'];
    $walker->review_count = $walkerData['review_count'];
    $walker->distance = $walkerData['distance'];
    $walker->price = $walkerData['price'];
    $walker->description = $walkerData['description'];
    $walker->availability = $walkerData['availability'];
    $walker->badges = $walkerData['badges'];
    $walker->background_check = $walkerData['background_check'];
    $walker->insured = $walkerData['insured'];
    $walker->certified = $walkerData['certified'];
    
    // Try to create walker
    if ($walker->create()) {
        $addedWalkers[] = $walkerData['name'];
    } else {
        $errors[] = "Failed to add " . $walkerData['name'];
    }
}

// Return results
echo json_encode([
    'success' => empty($errors),
    'added_walkers' => $addedWalkers,
    'errors' => $errors,
    'total_added' => count($addedWalkers)
]);
?>