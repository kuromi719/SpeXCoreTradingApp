<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';
require_once __DIR__ . '/../../utils/CRUDManager.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $UID = getSession('UID');
    
    if (!$UID) {
        echo json_encode(['success' => false, 'message' => 'User not logged in']);
        exit;
    }
    
    $coinId = Sanitize($_POST['coin_id'] ?? '');
    
    if (empty($coinId)) {
        echo json_encode(['success' => false, 'message' => 'Coin ID is required']);
        exit;
    }
    
    $crud = new CRUDManager();
    
    $coinExists = $crud->queryOne("SELECT id FROM coins WHERE id = ?", [$coinId]);
    if (!$coinExists) {
        echo json_encode(['success' => false, 'message' => 'Coin not found']);
        exit;
    }
    
    $existingWatchlist = $crud->queryOne("
        SELECT id FROM user_watchlist 
        WHERE user_id = ? AND coin_id = ?
    ", [$UID, $coinId]);
    
    if ($existingWatchlist) {
        $removed = $crud->query("
            DELETE FROM user_watchlist 
            WHERE user_id = ? AND coin_id = ?
        ", [$UID, $coinId]);
        
        if ($removed) {
            echo json_encode([
                'success' => true, 
                'message' => 'Removed from watchlist',
                'action' => 'removed'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove from watchlist']);
        }
    } else {
        $added = $crud->query("
            INSERT INTO user_watchlist (user_id, coin_id, added_at) 
            VALUES (?, ?, NOW())
        ", [$UID, $coinId]);
        
        if ($added) {
            echo json_encode([
                'success' => true, 
                'message' => 'Added to watchlist',
                'action' => 'added'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add to watchlist']);
        }
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>