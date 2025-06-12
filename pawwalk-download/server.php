<?php
// Simple PHP development server router
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Serve static files directly
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg)$/', $path)) {
    return false; // Let the built-in server handle static files
}

// API routing
if (strpos($path, '/api/') === 0) {
    $api_file = __DIR__ . $path . '.php';
    if (file_exists($api_file)) {
        include $api_file;
        return true;
    }
    
    // Handle API endpoints without .php extension
    $api_path = str_replace('/api/', '', $path);
    $api_file = __DIR__ . '/api/' . $api_path . '.php';
    if (file_exists($api_file)) {
        include $api_file;
        return true;
    }
    
    // API endpoint not found
    http_response_code(404);
    echo json_encode(['error' => 'API endpoint not found']);
    return true;
}

// Serve index.html for all other requests (SPA routing)
if ($path === '/' || !file_exists(__DIR__ . $path)) {
    include __DIR__ . '/index.html';
    return true;
}

// Let the server handle other files
return false;
?>