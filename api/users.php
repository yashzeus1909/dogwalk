<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/User.php';

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    http_response_code(500);
    echo json_encode(array("message" => "Database connection failed."));
    exit();
}

$user = new User($db);
$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        if(isset($_GET['email'])) {
            // Get user by email
            $stmt = $user->readByEmail($_GET['email']);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row) {
                $user_item = array(
                    "id" => (int)$row['id'],
                    "firstName" => $row['first_name'],
                    "lastName" => $row['last_name'],
                    "email" => $row['email'],
                    "phone" => $row['phone'],
                    "address" => $row['address'],
                    "createdAt" => $row['created_at']
                );
                
                http_response_code(200);
                echo json_encode($user_item);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "User not found."));
            }
        } else {
            // Get all users (admin function)
            $stmt = $user->read();
            $users_arr = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $user_item = array(
                    "id" => (int)$row['id'],
                    "firstName" => $row['first_name'],
                    "lastName" => $row['last_name'],
                    "email" => $row['email'],
                    "phone" => $row['phone'],
                    "address" => $row['address'],
                    "createdAt" => $row['created_at']
                );
                array_push($users_arr, $user_item);
            }

            http_response_code(200);
            echo json_encode($users_arr);
        }
        break;

    case 'POST':
        // Create new user
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->email) && !empty($data->firstName) && !empty($data->lastName)) {
            $user->first_name = $data->firstName;
            $user->last_name = $data->lastName;
            $user->email = $data->email;
            $user->phone = $data->phone ?? '';
            $user->address = $data->address ?? '';

            if($user->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "User created successfully.", "id" => $db->lastInsertId()));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create user."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "First name, last name, and email are required."));
        }
        break;

    case 'PUT':
        // Update user profile
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->email)) {
            $user->email = $data->email;
            $user->first_name = $data->firstName ?? '';
            $user->last_name = $data->lastName ?? '';
            $user->phone = $data->phone ?? '';
            $user->address = $data->address ?? '';

            if($user->updateByEmail()) {
                http_response_code(200);
                echo json_encode(array("message" => "User profile updated successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update user profile."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Email is required to update profile."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?>