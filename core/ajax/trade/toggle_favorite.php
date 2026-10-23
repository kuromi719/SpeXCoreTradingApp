<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$coinId = $input['coinId'] ?? '';

if (empty($coinId)) {
    echo json_encode(['success' => false, 'message' => 'Coin ID required']);
    exit;
}

try {
    $checkStmt = $spexcon->prepare("
        SELECT id FROM user_watchlist 
        WHERE user_id = ? AND coin_id = ?
    ");
    $checkStmt->bind_param("is", $UserUID, $coinId);
    $checkStmt->execute();
    $exists = $checkStmt->get_result()->fetch_assoc();
    
    if ($exists) {
        $deleteStmt = $spexcon->prepare("
            DELETE FROM user_watchlist 
            WHERE user_id = ? AND coin_id = ?
        ");
        $deleteStmt->bind_param("is", $UserUID, $coinId);
        $deleteStmt->execute();
        
        echo json_encode([
            'success' => true,
            'action' => 'removed',
            'message' => 'Removed from favorites'
        ]);
    } else {
        $insertStmt = $spexcon->prepare("
            INSERT INTO user_watchlist (user_id, coin_id) 
            VALUES (?, ?)
        ");
        $insertStmt->bind_param("is", $UserUID, $coinId);
        $insertStmt->execute();
        
        echo json_encode([
            'success' => true,
            'action' => 'added',
            'message' => 'Added to favorites'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error'
    ]);
}
?>