<?php
// Simple PHP server script
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Route to index.html for root requests
if ($path === '/') {
    include 'index.html';
    exit;
}

// Handle static files
$file_path = __DIR__ . $path;
if (file_exists($file_path) && !is_dir($file_path)) {
    $extension = pathinfo($file_path, PATHINFO_EXTENSION);
    
    switch($extension) {
        case 'css':
            header('Content-Type: text/css');
            break;
        case 'js':
            header('Content-Type: application/javascript');
            break;
        case 'html':
            header('Content-Type: text/html');
            break;
        case 'php':
            include $file_path;
            exit;
    }
    
    readfile($file_path);
    exit;
}

// If file not found, return 404
http_response_code(404);
echo "File not found";
?>