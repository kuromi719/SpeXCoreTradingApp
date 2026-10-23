<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['token_symbol'], $input['usdt_amount'], $input['token_amount'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $tokenSymbol = $input['token_symbol'];
    $usdtAmount = floatval($input['usdt_amount']);
    $tokenAmount = floatval($input['token_amount']);
    
    $userData = FetchUserData($UserUID);
    
    if ($userData['balance_blocked'] == 1) {
        echo json_encode(['success' => false, 'message' => 'Your Balance is Locked by System']);
        exit;
    }
    
    if ($userData['balance'] < $usdtAmount) {
        echo json_encode(['success' => false, 'message' => 'Insufficient USDT balance']);
        exit;
    }
    
    if ($usdtAmount < 1) {
        echo json_encode(['success' => false, 'message' => 'Minimum purchase amount is $1 USDT']);
        exit;
    }
    
    $spexcon->begin_transaction();
    
    $presaleQuery = $spexcon->prepare("
        SELECT price, trading_enabled FROM presales 
        WHERE coin_symbol = ? AND trading_enabled = 1
    ");
    $presaleQuery->bind_param("s", $tokenSymbol);
    $presaleQuery->execute();
    $presale = $presaleQuery->get_result()->fetch_assoc();
    
    if (!$presale) {
        throw new Exception('Token not available for trading');
    }
    
    $expectedTokens = $usdtAmount / $presale['price'];
    if (abs($tokenAmount - $expectedTokens) > 0.01) {
        throw new Exception('Price calculation error');
    }
    
    $newBalance = $userData['balance'] - $usdtAmount;
    $updateBalanceQuery = $spexcon->prepare("UPDATE users SET balance = ? WHERE uid = ?");
    $updateBalanceQuery->bind_param("di", $newBalance, $UserUID);
    $updateBalanceQuery->execute();
    
    $checkTokenQuery = $spexcon->prepare("
        SELECT balance FROM user_tokens 
        WHERE user_id = ? AND coin_symbol = ?
    ");
    $checkTokenQuery->bind_param("is", $UserUID, $tokenSymbol);
    $checkTokenQuery->execute();
    $existingToken = $checkTokenQuery->get_result()->fetch_assoc();
    
    if ($existingToken) {
        $newTokenBalance = $existingToken['balance'] + $tokenAmount;
        $updateTokenQuery = $spexcon->prepare("
            UPDATE user_tokens 
            SET balance = ?, updated_at = NOW() 
            WHERE user_id = ? AND coin_symbol = ?
        ");
        $updateTokenQuery->bind_param("dis", $newTokenBalance, $UserUID, $tokenSymbol);
        $updateTokenQuery->execute();
    } else {
        $coinQuery = $spexcon->prepare("
            SELECT coin_name, coin_logo FROM presales 
            WHERE coin_symbol = ? LIMIT 1
        ");
        $coinQuery->bind_param("s", $tokenSymbol);
        $coinQuery->execute();
        $coinData = $coinQuery->get_result()->fetch_assoc();
        
        $insertTokenQuery = $spexcon->prepare("
            INSERT INTO user_tokens (user_id, coin_symbol, coin_name, balance, coin_logo) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $insertTokenQuery->bind_param("issds", $UserUID, $tokenSymbol, $coinData['coin_name'], $tokenAmount, $coinData['coin_logo']);
        $insertTokenQuery->execute();
    }
    
    $spexcon->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Tokens purchased successfully',
        'new_balance' => $newBalance,
        'tokens_received' => $tokenAmount
    ]);
    
} catch (Exception $e) {
    $spexcon->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>