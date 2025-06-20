<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include_once '../config/database.php';
include_once '../models/Walker.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Create database connection
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }

    $walker = new Walker($db);
    
    // Get login credentials
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $walker_id = isset($_POST['walker_id']) ? (int)$_POST['walker_id'] : 0;

    if (empty($email) && !$walker_id) {
        throw new Exception('Email or Walker ID is required');
    }

    // Find walker by email or ID
    if ($walker_id) {
        $walker->id = $walker_id;
        if (!$walker->readOne()) {
            throw new Exception('Walker not found');
        }
    } else {
        // Find walker by email
        $query = "SELECT * FROM walkers WHERE email = ? LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute([$email]);
        
        $walker_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$walker_data) {
            throw new Exception('Walker not found with this email');
        }
        
        // Set walker properties
        $walker->id = $walker_data['id'];
        $walker->name = $walker_data['name'];
        $walker->email = $walker_data['email'];
        $walker->image = $walker_data['image'];
        $walker->rating = $walker_data['rating'];
        $walker->review_count = $walker_data['review_count'];
        $walker->distance = $walker_data['distance'];
        $walker->price = $walker_data['price'];
        $walker->description = $walker_data['description'];
        $walker->availability = $walker_data['availability'];
        $walker->badges = json_decode($walker_data['badges'], true) ?: [];
        $walker->background_check = $walker_data['background_check'];
        $walker->insured = $walker_data['insured'];
        $walker->certified = $walker_data['certified'];
    }

    // Return walker information for dashboard
    echo json_encode([
        'success' => true,
        'message' => 'Walker authenticated successfully',
        'walker' => [
            'id' => $walker->id,
            'name' => $walker->name,
            'email' => $walker->email,
            'image' => $walker->image,
            'rating' => number_format($walker->rating / 10, 1),
            'review_count' => $walker->review_count,
            'distance' => $walker->distance,
            'price' => $walker->price,
            'description' => $walker->description,
            'availability' => $walker->availability,
            'badges' => $walker->badges,
            'background_check' => $walker->background_check,
            'insured' => $walker->insured,
            'certified' => $walker->certified
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>