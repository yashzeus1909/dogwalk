<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Sample walkers data for demonstration
$walkers = [
    [
        "id" => 1,
        "name" => "Sarah M.",
        "image" => "https://images.unsplash.com/photo-1494790108755-2616b612b47c?w=150&h=150&fit=crop&crop=face",
        "rating" => 49,
        "reviewCount" => 127,
        "distance" => "0.5 miles away",
        "price" => 25,
        "description" => "Experienced dog walker with 5+ years caring for pets of all sizes. I love long walks and ensuring your furry friend gets the exercise they need!",
        "availability" => "Available today",
        "badges" => ["Background Check", "Insured", "5-Star Rated"],
        "backgroundCheck" => true,
        "insured" => true,
        "certified" => true
    ],
    [
        "id" => 2,
        "name" => "Mike T.",
        "image" => "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face",
        "rating" => 48,
        "reviewCount" => 89,
        "distance" => "0.8 miles away",
        "price" => 22,
        "description" => "Professional pet care specialist who treats every dog like family. Available for walks, feeding, and basic training.",
        "availability" => "Available tomorrow",
        "badges" => ["Certified", "Experienced"],
        "backgroundCheck" => true,
        "insured" => false,
        "certified" => true
    ],
    [
        "id" => 3,
        "name" => "Emma K.",
        "image" => "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=face",
        "rating" => 50,
        "reviewCount" => 203,
        "distance" => "1.2 miles away",
        "price" => 30,
        "description" => "Veterinary student with a passion for animal care. Specializing in senior dogs and those with special needs.",
        "availability" => "Available this week",
        "badges" => ["Vet Student", "Special Needs", "Top Rated"],
        "backgroundCheck" => true,
        "insured" => true,
        "certified" => true
    ]
];

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        $result = $walkers;
        
        // Handle search filtering
        if(isset($_GET['search']) && isset($_GET['location']) && !empty($_GET['location'])) {
            $location = strtolower($_GET['location']);
            $result = array_filter($walkers, function($walker) use ($location) {
                return strpos(strtolower($walker['distance']), $location) !== false ||
                       strpos(strtolower($walker['name']), $location) !== false;
            });
            $result = array_values($result); // Re-index array
        }
        
        http_response_code(200);
        echo json_encode($result);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->name) && !empty($data->price)) {
            // In a real app, this would save to database
            http_response_code(201);
            echo json_encode(array("message" => "Walker was created.", "id" => count($walkers) + 1));
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