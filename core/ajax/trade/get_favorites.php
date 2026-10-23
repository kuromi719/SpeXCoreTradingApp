<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $stmt = $spexcon->prepare("
        SELECT coin_id 
        FROM user_watchlist 
        WHERE user_id = ?
    ");
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $favoriteIds = [];
    while ($row = $result->fetch_assoc()) {
        $favoriteIds[] = $row['coin_id'];
    }
    
    echo json_encode([
        'success' => true,
        'favorite_ids' => $favoriteIds
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error',
        'favorite_ids' => []
    ]);
}
?>