<?php
// Simulate POST data for testing
$_POST = [];
$input = json_encode([
    'firstName' => 'Test',
    'lastName' => 'User',
    'email' => 'test@example.com',
    'password' => 'password123',
    'phone' => '1234567890',
    'address' => '123 Test St'
]);

// Mock php://input for testing
file_put_contents('php://memory', $input);

// Include the registration script
include 'api/customer_register.php';
?>