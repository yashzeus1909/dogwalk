<?php
// Session check API for user authentication status
session_start();
require_once 'config.php';

setJsonHeaders();

try {
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_email'])) {
        // Verify user still exists in database
        $db = getDatabaseConnection();
        $stmt = $db->prepare("SELECT id, first_name, last_name, email, role FROM users WHERE id = ? AND email = ?");
        $stmt->execute([$_SESSION['user_id'], $_SESSION['user_email']]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo json_encode([
                'success' => true,
                'logged_in' => true,
                'user' => [
                    'id' => $user['id'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'full_name' => $user['first_name'] . ' ' . $user['last_name']
                ]
            ]);
        } else {
            // User no longer exists, destroy session
            session_destroy();
            echo json_encode([
                'success' => true,
                'logged_in' => false,
                'message' => 'Session expired'
            ]);
        }
    } else {
        echo json_encode([
            'success' => true,
            'logged_in' => false,
            'message' => 'No active session'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'logged_in' => false,
        'message' => 'Session check failed: ' . $e->getMessage()
    ]);
}
?>