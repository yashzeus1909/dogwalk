<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Initialize session to store bookings
session_start();

// Initialize bookings if not exists
if (!isset($_SESSION['bookings'])) {
    $_SESSION['bookings'] = [
        [
            "id" => 1,
            "walkerId" => 1,
            "dogName" => "Rocky",
            "dogSize" => "Medium",
            "date" => "2024-06-15",
            "time" => "2:00 PM",
            "duration" => 60,
            "phone" => "555-0123",
            "email" => "john.doe@example.com",
            "instructions" => "Rocky loves to play fetch in the park!",
            "serviceFee" => 2500,
            "appFee" => 375,
            "total" => 2875,
            "status" => "confirmed",
            "createdAt" => "2024-06-10 14:30:00",
            "walkerName" => "Sarah M.",
            "walkerImage" => "https://images.unsplash.com/photo-1494790108755-2616b612b47c?w=150&h=150&fit=crop&crop=face"
        ]
    ];
}

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        $bookings = $_SESSION['bookings'];
        
        // Filter by user email if specified
        if(isset($_GET['user_email']) && !empty($_GET['user_email'])) {
            $user_email = $_GET['user_email'];
            $bookings = array_filter($bookings, function($booking) use ($user_email) {
                return $booking['email'] === $user_email;
            });
            $bookings = array_values($bookings); // Re-index array
        }
        
        http_response_code(200);
        echo json_encode($bookings);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->walkerId) && !empty($data->dogName) && !empty($data->email)) {
            // Walker data for lookup
            $walkers = [
                1 => ["name" => "Sarah M.", "image" => "https://images.unsplash.com/photo-1494790108755-2616b612b47c?w=150&h=150&fit=crop&crop=face"],
                2 => ["name" => "Mike T.", "image" => "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face"],
                3 => ["name" => "Emma K.", "image" => "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=face"]
            ];
            
            $walker = $walkers[$data->walkerId] ?? ["name" => "Unknown Walker", "image" => ""];
            
            $new_booking = [
                "id" => count($_SESSION['bookings']) + 1,
                "walkerId" => (int)$data->walkerId,
                "dogName" => $data->dogName,
                "dogSize" => $data->dogSize,
                "date" => $data->date,
                "time" => $data->time,
                "duration" => (int)$data->duration,
                "phone" => $data->phone,
                "email" => $data->email,
                "instructions" => $data->instructions ?? "",
                "serviceFee" => (int)$data->serviceFee,
                "appFee" => (int)$data->appFee,
                "total" => (int)$data->total,
                "status" => $data->status ?? "pending",
                "createdAt" => date('Y-m-d H:i:s'),
                "walkerName" => $walker["name"],
                "walkerImage" => $walker["image"]
            ];
            
            $_SESSION['bookings'][] = $new_booking;
            
            http_response_code(201);
            echo json_encode(array("message" => "Booking was created.", "id" => $new_booking["id"]));
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to create booking. Data is incomplete."));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->id) && !empty($data->status)) {
            $booking_id = (int)$data->id;
            $found = false;
            
            for ($i = 0; $i < count($_SESSION['bookings']); $i++) {
                if ($_SESSION['bookings'][$i]['id'] === $booking_id) {
                    $_SESSION['bookings'][$i]['status'] = $data->status;
                    $found = true;
                    break;
                }
            }
            
            if ($found) {
                http_response_code(200);
                echo json_encode(array("message" => "Booking was updated."));
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Booking not found."));
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