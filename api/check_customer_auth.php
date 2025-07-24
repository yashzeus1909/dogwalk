<?php
// Use MySQL database for authentication checks
require_once 'config.php';
error_log("AUTH CHECK: Using MySQL database (dog_walker_app)");

setJsonHeaders();
startSession();

// Check if user is logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => true,
        'authenticated' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email']
        ]
    ]);
} else {
    echo json_encode([
        'success' => true,
        'authenticated' => false
    ]);
}
?>