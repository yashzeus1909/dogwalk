<?php
header('Content-Type: application/json');
session_start();

if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
    echo json_encode([
        'authenticated' => true,
        'user_id' => $_SESSION['user_id'],
        'role' => $_SESSION['user_role']
    ]);
} else {
    echo json_encode([
        'authenticated' => false
    ]);
}
?>