<?php
require_once 'config.php';

setJsonHeaders();

try {
    $query = $_POST['query'] ?? $_GET['query'] ?? '';
    
    if (empty($query)) {
        throw new Exception('Query parameter is required');
    }
    
    // Security: Only allow SELECT queries for safety
    $query = trim($query);
    if (!preg_match('/^SELECT\s+/i', $query)) {
        throw new Exception('Only SELECT queries are allowed');
    }
    
    $db = getDatabaseConnection();
    $stmt = $db->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'query' => $query,
        'results' => $results,
        'count' => count($results)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'query' => $query ?? ''
    ]);
}
?>