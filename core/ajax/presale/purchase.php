<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['presale_id'], $input['amount'], $input['total_cost'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $presaleId = intval($input['presale_id']);
    $amount = floatval($input['amount']);
    $totalCost = floatval($input['total_cost']);
    
    $userData = FetchUserData($UserUID);
    
    if ($userData['balance_blocked'] == 1) {
        echo json_encode(['success' => false, 'message' => 'Your Balance is Locked by System']);
        exit;
    }
    
    if ($userData['balance'] < $totalCost) {
        echo json_encode(['success' => false, 'message' => 'Insufficient balance']);
        exit;
    }
    
    $spexcon->begin_transaction();
    
    $presaleQuery = $spexcon->prepare("
        SELECT * FROM presales 
        WHERE id = ? AND status = 'active' 
        AND start_date <= NOW() AND end_date >= NOW()
    ");
    $presaleQuery->bind_param("i", $presaleId);
    $presaleQuery->execute();
    $presale = $presaleQuery->get_result()->fetch_assoc();
    
    if (!$presale) {
        throw new Exception('Presale not found or not active');
    }
    
    if ($amount < $presale['min_purchase'] || $amount > $presale['max_purchase']) {
        throw new Exception('Amount outside allowed range');
    }
    
    if ($presale['sold_amount'] + $amount > $presale['total_supply']) {
        throw new Exception('Not enough tokens available');
    }
    
    $expectedCost = $amount * $presale['price'];
    if (abs($totalCost - $expectedCost) > 0.01) {
        throw new Exception('Price calculation error');
    }
    
    $newBalance = $userData['balance'] - $totalCost;
    $updateBalanceQuery = $spexcon->prepare("UPDATE users SET balance = ? WHERE uid = ?");
    $updateBalanceQuery->bind_param("di", $newBalance, $UserUID);
    $updateBalanceQuery->execute();
    
    $insertPurchaseQuery = $spexcon->prepare("
        INSERT INTO presale_purchases (user_id, presale_id, amount, price, total_cost, status) 
        VALUES (?, ?, ?, ?, ?, 'completed')
    ");
    $insertPurchaseQuery->bind_param("iiddd", $UserUID, $presaleId, $amount, $presale['price'], $totalCost);
    $insertPurchaseQuery->execute();
    
    $updatePresaleQuery = $spexcon->prepare("UPDATE presales SET sold_amount = sold_amount + ? WHERE id = ?");
    $updatePresaleQuery->bind_param("di", $amount, $presaleId);
    $updatePresaleQuery->execute();
    
    $spexcon->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Purchase completed successfully',
        'new_balance' => $newBalance,
        'purchase_id' => $spexcon->insert_id
    ]);
    
} catch (Exception $e) {
    $spexcon->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>