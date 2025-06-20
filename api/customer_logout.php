<?php
session_start();
header('Content-Type: application/json');

// Clear customer session data
unset($_SESSION['user_id']);
unset($_SESSION['user_email']);
unset($_SESSION['user_name']);
unset($_SESSION['is_customer']);

echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
?>