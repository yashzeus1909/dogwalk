<?php
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Pure PHP server is working!',
    'timestamp' => date('Y-m-d H:i:s')
]);
?>