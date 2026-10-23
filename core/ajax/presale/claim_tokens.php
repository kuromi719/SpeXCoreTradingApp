<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['purchase_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing purchase ID']);
    exit;
}


try {
    $purchaseId = intval($input['purchase_id']);

    $userData = FetchUserData($UserUID);
    
    if ($userData['balance_blocked'] == 1) {
        echo json_encode(['success' => false, 'message' => 'Your Balance is Locked by System']);
        exit;
    }
    
    $spexcon->begin_transaction();
    
    $purchaseQuery = $spexcon->prepare("
        SELECT pp.*, p.coin_name, p.coin_symbol, p.coin_logo, p.status as presale_status, p.claim_enabled
        FROM presale_purchases pp
        JOIN presales p ON pp.presale_id = p.id
        WHERE pp.id = ? AND pp.user_id = ? AND pp.status = 'completed'
    ");
    $purchaseQuery->bind_param("ii", $purchaseId, $UserUID);
    $purchaseQuery->execute();
    $purchase = $purchaseQuery->get_result()->fetch_assoc();
    
    if (!$purchase) {
        throw new Exception('Purchase not found');
    }
    
    if ($purchase['claimed']) {
        throw new Exception('Tokens already claimed');
    }
    
    if ($purchase['presale_status'] !== 'ended') {
        throw new Exception('Presale has not ended yet');
    }
    
    if (!$purchase['claim_enabled']) {
        throw new Exception('Token claiming is not enabled yet');
    }
    
    $checkTokenQuery = $spexcon->prepare("
        SELECT balance FROM user_tokens 
        WHERE user_id = ? AND coin_symbol = ?
    ");
    $checkTokenQuery->bind_param("is", $UserUID, $purchase['coin_symbol']);
    $checkTokenQuery->execute();
    $existingToken = $checkTokenQuery->get_result()->fetch_assoc();
    
    if ($existingToken) {
        $newBalance = $existingToken['balance'] + $purchase['amount'];
        $updateTokenQuery = $spexcon->prepare("
            UPDATE user_tokens 
            SET balance = ?, updated_at = NOW() 
            WHERE user_id = ? AND coin_symbol = ?
        ");
        $updateTokenQuery->bind_param("dis", $newBalance, $UserUID, $purchase['coin_symbol']);
        $updateTokenQuery->execute();
    } else {
        $insertTokenQuery = $spexcon->prepare("
            INSERT INTO user_tokens (user_id, coin_symbol, coin_name, balance, coin_logo) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $insertTokenQuery->bind_param("issds", $UserUID, $purchase['coin_symbol'], $purchase['coin_name'], $purchase['amount'], $purchase['coin_logo']);
        $insertTokenQuery->execute();
    }
    
    $updatePurchaseQuery = $spexcon->prepare("
        UPDATE presale_purchases 
        SET claimed = 1, claimed_at = NOW() 
        WHERE id = ?
    ");
    $updatePurchaseQuery->bind_param("i", $purchaseId);
    $updatePurchaseQuery->execute();
    
    $spexcon->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Tokens claimed successfully',
        'token_amount' => $purchase['amount'],
        'coin_symbol' => $purchase['coin_symbol']
    ]);
    
} catch (Exception $e) {
    $spexcon->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>