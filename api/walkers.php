<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/Walker.php';

$database = new Database();
$db = $database->getConnection();
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
        $walkers_arr["walkers"] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            
            $walker_item = array(
                "id" => $id,
                "name" => $name,
                "image" => $image,
                "rating" => (int)$rating,
                "reviewCount" => (int)$review_count,
                "distance" => $distance,
                "price" => (int)$price,
                "description" => $description,
                "availability" => $availability,
                "badges" => json_decode($badges ? $badges : '[]'),
                "backgroundCheck" => (bool)$background_check,
                "insured" => (bool)$insured,
                "certified" => (bool)$certified
            );

            array_push($walkers_arr["walkers"], $walker_item);
        }

        http_response_code(200);
        echo json_encode($walkers_arr["walkers"]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->name) && !empty($data->price)) {
            $walker->name = $data->name;
            $walker->image = $data->image;
            $walker->rating = $data->rating;
            $walker->review_count = $data->review_count;
            $walker->distance = $data->distance;
            $walker->price = $data->price;
            $walker->description = $data->description;
            $walker->availability = $data->availability;
            $walker->badges = json_encode($data->badges);
            $walker->background_check = $data->background_check;
            $walker->insured = $data->insured;
            $walker->certified = $data->certified;

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