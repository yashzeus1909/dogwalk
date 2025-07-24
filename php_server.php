<?php
// Pure PHP server for PawWalk application
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Remove query string from path
$path = strtok($path, '?');

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Serve static files
if ($path === '/' || $path === '/index.html') {
    readfile('index.html');
    exit;
}

// Serve PHP pages
$phpPages = [
    '/customer_login.php' => 'customer_login.php',
    '/admin_dashboard.php' => 'admin_dashboard.php', 
    '/walker_dashboard.php' => 'walker_dashboard.php',
    '/booking.php' => 'booking.php'
];

if (isset($phpPages[$path])) {
    include $phpPages[$path];
    exit;
}

// Handle API requests
if (strpos($path, '/api/') === 0) {
    $apiPath = str_replace('/api/', '', $path);
    $apiFile = 'api/' . $apiPath;
    
    if (file_exists($apiFile)) {
        include $apiFile;
        exit;
    }
    
    // API endpoint not found
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'API endpoint not found']);
    exit;
}

// Handle CSS files
if (strpos($path, '.css') !== false) {
    $file = '.' . $path;
    if (file_exists($file)) {
        header('Content-Type: text/css');
        readfile($file);
    } else {
        http_response_code(404);
        echo "CSS file not found";
    }
    exit;
}

// Handle JS files
if (strpos($path, '.js') !== false) {
    $file = '.' . $path;
    if (file_exists($file)) {
        header('Content-Type: application/javascript');
        readfile($file);
    } else {
        http_response_code(404);
        echo "JS file not found";
    }
    exit;
}

// Handle image files
if (preg_match('/\.(png|jpg|jpeg|gif|ico|svg)$/', $path)) {
    $file = '.' . $path;
    if (file_exists($file)) {
        $mime = mime_content_type($file);
        header('Content-Type: ' . $mime);
        readfile($file);
    } else {
        http_response_code(404);
        echo "Image not found";
    }
    exit;
}

// 404 for unknown routes
http_response_code(404);
echo "404 Not Found";
?>