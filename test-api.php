<?php
// Simple test script to verify API endpoints are working
header('Content-Type: text/html; charset=UTF-8');

echo "<h1>PawWalk API Test</h1>";

// Test walkers endpoint
echo "<h2>Testing Walkers API</h2>";
$walkers_url = 'http://localhost:8000/api/walkers.php';
$walkers_response = @file_get_contents($walkers_url);

if ($walkers_response) {
    $walkers_data = json_decode($walkers_response, true);
    echo "<p>✅ Walkers API working - Found " . count($walkers_data) . " walkers</p>";
    echo "<pre>" . htmlspecialchars(json_encode($walkers_data, JSON_PRETTY_PRINT)) . "</pre>";
} else {
    echo "<p>❌ Walkers API not responding</p>";
}

// Test bookings endpoint
echo "<h2>Testing Bookings API</h2>";
$bookings_url = 'http://localhost:8000/api/bookings.php';
$bookings_response = @file_get_contents($bookings_url);

if ($bookings_response) {
    $bookings_data = json_decode($bookings_response, true);
    echo "<p>✅ Bookings API working - Found " . count($bookings_data) . " bookings</p>";
    echo "<pre>" . htmlspecialchars(json_encode($bookings_data, JSON_PRETTY_PRINT)) . "</pre>";
} else {
    echo "<p>❌ Bookings API not responding</p>";
}

echo "<h2>Frontend Test</h2>";
echo "<p><a href='index.html'>Open PawWalk Application</a></p>";
?>