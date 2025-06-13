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
    // Get JSON input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (!$data) {
        throw new Exception('Invalid JSON data');
    }
    
    // Create database connection
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    
    $walker = new Walker($db);
    
    // Get walker ID
    $walker_id = isset($data['id']) ? (int)$data['id'] : 0;
    
    if (!$walker_id) {
        throw new Exception('Walker ID is required');
    }
    
    // Check if walker exists
    $walker->id = $walker_id;
    if (!$walker->readOne()) {
        throw new Exception('Walker not found');
    }
    
    $walker_name = $walker->name; // Store name for response
    
    // Delete the walker
    if ($walker->delete()) {
        echo json_encode([
            'success' => true,
            'message' => 'Walker deleted successfully',
            'walker_name' => $walker_name,
            'walker_id' => $walker_id
        ]);
    } else {
        throw new Exception('Failed to delete walker from database');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>