<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT");
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
        if(isset($_GET['user_email'])) {
            $stmt = $booking->readByUser($_GET['user_email']);
        } else {
            $stmt = $booking->read();
        }
        
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
                "email" => $email,
                "instructions" => $instructions,
                "serviceFee" => (int)$service_fee,
                "appFee" => (int)$app_fee,
                "total" => (int)$total,
                "status" => $status,
                "createdAt" => $created_at,
                "walkerName" => isset($walker_name) ? $walker_name : null,
                "walkerImage" => isset($walker_image) ? $walker_image : null
            );

            array_push($bookings_arr, $booking_item);
        }

        http_response_code(200);
        echo json_encode($bookings_arr);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->walkerId) && !empty($data->dogName) && !empty($data->email)) {
            $booking->walker_id = $data->walkerId;
            $booking->dog_name = $data->dogName;
            $booking->dog_size = $data->dogSize;
            $booking->booking_date = $data->date;
            $booking->booking_time = $data->time;
            $booking->duration = $data->duration;
            $booking->phone = $data->phone;
            $booking->email = $data->email;
            $booking->instructions = $data->instructions ?? '';
            $booking->service_fee = $data->serviceFee;
            $booking->app_fee = $data->appFee;
            $booking->total = $data->total;
            $booking->status = isset($data->status) ? $data->status : 'pending';

            if($booking->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Booking was created.", "id" => $db->lastInsertId()));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create booking."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to create booking. Data is incomplete."));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->id) && !empty($data->status)) {
            $booking->id = $data->id;
            $booking->status = $data->status;

            if($booking->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Booking was updated."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update booking."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to update booking. Data is incomplete."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?>