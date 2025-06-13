<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/Booking.php';

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    http_response_code(500);
    echo json_encode(array("message" => "Database connection failed."));
    exit();
}

$booking = new Booking($db);
$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        // Get bookings for a specific user by email
        if(!isset($_GET['email']) || empty($_GET['email'])) {
            http_response_code(400);
            echo json_encode(array("message" => "Email parameter is required."));
            break;
        }

        $stmt = $booking->readByUser($_GET['email']);
        $bookings_arr = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            
            $booking_item = array(
                "id" => (int)$id,
                "walkerId" => (int)$walker_id,
                "dogName" => $dog_name,
                "dogSize" => $dog_size,
                "date" => $booking_date,
                "time" => $booking_time,
                "duration" => (int)$duration,
                "phone" => $phone,
                "address" => isset($address) ? $address : '',
                "email" => isset($email) ? $email : '',
                "specialNotes" => isset($special_notes) ? $special_notes : '',
                "totalPrice" => isset($total_price) ? $total_price : '',
                "status" => $status,
                "createdAt" => isset($created_at) ? $created_at : '',
                "walkerName" => isset($walker_name) ? $walker_name : 'Unknown Walker',
                "walkerImage" => isset($walker_image) ? $walker_image : ''
            );

            array_push($bookings_arr, $booking_item);
        }

        http_response_code(200);
        echo json_encode($bookings_arr);
        break;

    case 'PUT':
        // Update booking status
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->id) && !empty($data->status)) {
            $booking->id = $data->id;
            $booking->status = $data->status;

            if($booking->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Booking status updated successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update booking status."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Booking ID and status are required."));
        }
        break;

    case 'DELETE':
        // Cancel/delete booking
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->id)) {
            $booking->id = $data->id;

            if($booking->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Booking cancelled successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to cancel booking."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Booking ID is required."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?>