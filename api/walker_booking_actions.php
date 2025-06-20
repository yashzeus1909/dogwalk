<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, DELETE');
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
    
    switch ($method) {
        case 'PUT':
            // Update booking status
            $input = json_decode(file_get_contents('php://input'), true);
            
            $booking_id = isset($input['booking_id']) ? (int)$input['booking_id'] : 0;
            $walker_id = isset($input['walker_id']) ? (int)$input['walker_id'] : 0;
            $new_status = isset($input['status']) ? trim($input['status']) : '';
            $notes = isset($input['notes']) ? trim($input['notes']) : '';

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

            // Validate status transition
            $valid_statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];
            if (!in_array($new_status, $valid_statuses)) {
                throw new Exception('Invalid status. Valid statuses: ' . implode(', ', $valid_statuses));
            }

            // Check valid status transitions
            $current_status = $booking->status;
            $valid_transitions = [
                'pending' => ['confirmed', 'cancelled'],
                'confirmed' => ['in_progress', 'cancelled', 'completed'],
                'in_progress' => ['completed', 'cancelled'],
                'completed' => [], // Cannot change from completed
                'cancelled' => [] // Cannot change from cancelled
            ];

            if (!empty($valid_transitions[$current_status]) && !in_array($new_status, $valid_transitions[$current_status])) {
                throw new Exception("Cannot change status from '{$current_status}' to '{$new_status}'");
            }

            if (empty($valid_transitions[$current_status])) {
                throw new Exception("Cannot change status from '{$current_status}' - booking is finalized");
            }

            // Update booking status
            $booking->status = $new_status;
            
            // Add notes to special_notes if provided
            if ($notes) {
                $existing_notes = $booking->special_notes ?: '';
                $timestamp = date('Y-m-d H:i:s');
                $walker_note = "\n[Walker Update {$timestamp}]: {$notes}";
                $booking->special_notes = $existing_notes . $walker_note;
            }

            if ($booking->update()) {
                echo json_encode([
                    'success' => true,
                    'message' => "Booking status updated to '{$new_status}' successfully",
                    'booking' => [
                        'id' => $booking->id,
                        'status' => $booking->status,
                        'special_notes' => $booking->special_notes,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
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
            $reason = isset($input['reason']) ? trim($input['reason']) : '';

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

            // Log deletion reason if provided
            if ($reason) {
                error_log("Booking {$booking_id} deleted by walker {$walker_id}. Reason: {$reason}");
            }

            // Delete the booking
            if ($booking->delete()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Booking deleted successfully',
                    'booking_id' => $booking_id,
                    'deleted_at' => date('Y-m-d H:i:s')
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