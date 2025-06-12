<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/Walker.php';

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    http_response_code(500);
    echo json_encode(array("message" => "Database connection failed."));
    exit();
}

$walker = new Walker($db);

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        if(isset($_GET['search'])) {
            $location = isset($_GET['location']) ? $_GET['location'] : "";
            $service_type = isset($_GET['service_type']) ? $_GET['service_type'] : "";
            $stmt = $walker->search($location, $service_type);
        } else {
            $stmt = $walker->read();
        }
        
        $walkers_arr = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            
            // Convert PostgreSQL array to PHP array
            $badges_array = array();
            if ($badges) {
                // Remove curly braces and split by comma
                $badges_clean = trim($badges, '{}');
                if (!empty($badges_clean)) {
                    $badges_array = array_map('trim', explode(',', $badges_clean));
                    // Remove quotes if present
                    $badges_array = array_map(function($badge) {
                        return trim($badge, '"');
                    }, $badges_array);
                }
            }
            
            $walker_item = array(
                "id" => (int)$id,
                "name" => $name,
                "image" => $image,
                "rating" => (int)$rating,
                "reviewCount" => (int)$review_count,
                "distance" => $distance,
                "price" => (int)$price,
                "description" => $description,
                "availability" => $availability,
                "badges" => $badges_array,
                "backgroundCheck" => (bool)$background_check,
                "insured" => (bool)$insured,
                "certified" => (bool)$certified
            );

            array_push($walkers_arr, $walker_item);
        }

        http_response_code(200);
        echo json_encode($walkers_arr);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->name) && !empty($data->price)) {
            $walker->name = $data->name;
            $walker->image = $data->image ?? '';
            $walker->rating = $data->rating ?? 0;
            $walker->review_count = $data->review_count ?? 0;
            $walker->distance = $data->distance ?? '';
            $walker->price = $data->price;
            $walker->description = $data->description ?? '';
            $walker->availability = $data->availability ?? '';
            $walker->badges = isset($data->badges) ? '{' . implode(',', $data->badges) . '}' : '{}';
            $walker->background_check = $data->background_check ?? false;
            $walker->insured = $data->insured ?? false;
            $walker->certified = $data->certified ?? false;

            if($walker->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Walker was created."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create walker."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to create walker. Data is incomplete."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?>