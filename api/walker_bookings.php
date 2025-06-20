<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

include_once '../config/database.php';
include_once '../models/Booking.php';
include_once '../models/Walker.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    // Create database connection
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }

    $booking = new Booking($db);
    $walker = new Walker($db);

    switch ($method) {
        case 'GET':
            // Get bookings for a specific walker
            $walker_id = isset($_GET['walker_id']) ? (int)$_GET['walker_id'] : 0;
            
            if (!$walker_id) {
                throw new Exception('Walker ID is required');
            }

            // Verify walker exists
            $walker->id = $walker_id;
            if (!$walker->readOne()) {
                throw new Exception('Walker not found');
            }

            // Get walker's bookings
            $stmt = $booking->readByWalker($walker_id);
            $bookings = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $bookings[] = [
                    'id' => $row['id'],
                    'dog_name' => $row['dog_name'],
                    'dog_size' => $row['dog_size'],
                    'booking_date' => $row['booking_date'],
                    'booking_time' => $row['booking_time'],
                    'duration' => $row['duration'],
                    'phone' => $row['phone'],
                    'email' => $row['email'],
                    'address' => $row['address'],
                    'special_notes' => $row['special_notes'],
                    'total_price' => $row['total_price'],
                    'status' => $row['status'],
                    'created_at' => $row['created_at'],
                    'customer_name' => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')),
                    'walker_name' => $row['walker_name'] ?? 'Unknown'
                ];
            }

            echo json_encode([
                'success' => true,
                'bookings' => $bookings,
                'walker_name' => $walker->name,
                'total_bookings' => count($bookings)
            ]);
            break;

        case 'PUT':
            // Update booking status
            $input = json_decode(file_get_contents('php://input'), true);
            
            $booking_id = isset($input['booking_id']) ? (int)$input['booking_id'] : 0;
            $walker_id = isset($input['walker_id']) ? (int)$input['walker_id'] : 0;
            $new_status = isset($input['status']) ? trim($input['status']) : '';

            if (!$booking_id || !$walker_id || !$new_status) {
                throw new Exception('Booking ID, Walker ID, and status are required');
            }

            // Verify the booking belongs to this walker
            $booking->id = $booking_id;
            if (!$booking->readOne()) {
                throw new Exception('Booking not found');
            }

            if ($booking->walker_id != $walker_id) {
                throw new Exception('Unauthorized: This booking does not belong to you');
            }

            // Validate status
            $valid_statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];
            if (!in_array($new_status, $valid_statuses)) {
                throw new Exception('Invalid status. Valid statuses: ' . implode(', ', $valid_statuses));
            }

            // Update booking status
            $booking->status = $new_status;
            if ($booking->update()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Booking status updated successfully',
                    'booking_id' => $booking_id,
                    'new_status' => $new_status
                ]);
            } else {
                throw new Exception('Failed to update booking status');
            }
            break;

        case 'DELETE':
            // Delete a booking
            $input = json_decode(file_get_contents('php://input'), true);
            
            $booking_id = isset($input['booking_id']) ? (int)$input['booking_id'] : 0;
            $walker_id = isset($input['walker_id']) ? (int)$input['walker_id'] : 0;

            if (!$booking_id || !$walker_id) {
                throw new Exception('Booking ID and Walker ID are required');
            }

            // Verify the booking belongs to this walker
            $booking->id = $booking_id;
            if (!$booking->readOne()) {
                throw new Exception('Booking not found');
            }

            if ($booking->walker_id != $walker_id) {
                throw new Exception('Unauthorized: This booking does not belong to you');
            }

            // Only allow deletion of pending or cancelled bookings
            if (!in_array($booking->status, ['pending', 'cancelled'])) {
                throw new Exception('Only pending or cancelled bookings can be deleted');
            }

            // Delete the booking
            if ($booking->delete()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Booking deleted successfully',
                    'booking_id' => $booking_id
                ]);
            } else {
                throw new Exception('Failed to delete booking');
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