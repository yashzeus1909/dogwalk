<?php
require_once 'config.php';

setJsonHeaders();

try {
    $service = $_GET['service'] ?? '';
    $priceRange = $_GET['price'] ?? '';
    
    $sql = "SELECT * FROM users WHERE role = 'walker'";
    $params = [];
    $conditions = [];
    
    // Filter by service - direct database query
    if (!empty($service)) {
        $conditions[] = "services LIKE ?";
        $params[] = '%' . $service . '%';
    }
    
    // Filter by price range - direct database query
    if (!empty($priceRange)) {
        $rangeParts = explode('-', $priceRange);
        if (count($rangeParts) == 2) {
            $minPrice = (int)$rangeParts[0];
            $maxPrice = (int)$rangeParts[1];
            $conditions[] = "price_per_hour BETWEEN ? AND ?";
            $params[] = $minPrice;
            $params[] = $maxPrice;
        }
    }
    
    if (!empty($conditions)) {
        $sql .= " AND " . implode(" AND ", $conditions);
    }
    
    $sql .= " ORDER BY rating DESC";
    
    $db = getDatabaseConnection();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $walkers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'walkers' => $walkers
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching walkers: ' . $e->getMessage()
    ]);
}
?>