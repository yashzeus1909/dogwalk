<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

include_once '../config/database.php';
include_once '../models/Walker.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    // Create database connection
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }

    $walker = new Walker($db);

    switch ($method) {
        case 'GET':
            // Get walker profile details
            $walker_id = isset($_GET['walker_id']) ? (int)$_GET['walker_id'] : 0;
            
            if (!$walker_id) {
                throw new Exception('Walker ID is required');
            }

            $walker->id = $walker_id;
            if (!$walker->readOne()) {
                throw new Exception('Walker not found');
            }

            echo json_encode([
                'success' => true,
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
            break;

        case 'POST':
        case 'PUT':
            // Update walker profile
            $input = $_POST;
            if (empty($input)) {
                $input = json_decode(file_get_contents('php://input'), true);
            }
            
            $walker_id = isset($input['walker_id']) ? (int)$input['walker_id'] : 0;
            
            if (!$walker_id) {
                throw new Exception('Walker ID is required');
            }

            // Verify walker exists
            $walker->id = $walker_id;
            if (!$walker->readOne()) {
                throw new Exception('Walker not found');
            }

            // Validate required fields
            if (isset($input['name']) && empty(trim($input['name']))) {
                throw new Exception('Name cannot be empty');
            }
            
            if (isset($input['email']) && !filter_var(trim($input['email']), FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format');
            }

            if (isset($input['price']) && (int)$input['price'] < 1) {
                throw new Exception('Price must be at least $1');
            }

            // Update walker properties with provided data
            if (isset($input['name'])) {
                $walker->name = trim($input['name']);
            }
            
            if (isset($input['email'])) {
                $walker->email = trim($input['email']);
            }
            
            if (isset($input['image'])) {
                $walker->image = !empty($input['image']) ? trim($input['image']) : null;
                
                // Validate image URL if provided
                if ($walker->image && !filter_var($walker->image, FILTER_VALIDATE_URL)) {
                    throw new Exception('Invalid image URL format');
                }
            }
            
            if (isset($input['price'])) {
                $walker->price = (int)$input['price'];
            }
            
            if (isset($input['description'])) {
                $walker->description = !empty($input['description']) ? trim($input['description']) : null;
            }
            
            if (isset($input['availability'])) {
                $walker->availability = !empty($input['availability']) ? trim($input['availability']) : null;
            }
            
            if (isset($input['distance'])) {
                $walker->distance = !empty($input['distance']) ? trim($input['distance']) : null;
            }

            // Handle service badges
            if (isset($input['badges'])) {
                if (is_array($input['badges'])) {
                    $walker->badges = $input['badges'];
                } else if (is_string($input['badges'])) {
                    $walker->badges = json_decode($input['badges'], true) ?: [];
                } else {
                    $walker->badges = [];
                }
            }

            // Handle certifications
            if (isset($input['background_check'])) {
                $walker->background_check = $input['background_check'] ? 1 : 0;
            }
            
            if (isset($input['insured'])) {
                $walker->insured = $input['insured'] ? 1 : 0;
            }
            
            if (isset($input['certified'])) {
                $walker->certified = $input['certified'] ? 1 : 0;
            }

            // Update the walker
            if ($walker->update()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Walker profile updated successfully',
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
            } else {
                throw new Exception('Failed to update walker profile');
            }
            break;

        default:
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
            break;
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>