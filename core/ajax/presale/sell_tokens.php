<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['token_symbol'], $input['token_amount'], $input['usdt_amount'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $tokenSymbol = $input['token_symbol'];
    $tokenAmount = floatval($input['token_amount']);
    $usdtAmount = floatval($input['usdt_amount']);
    
    $userData = FetchUserData($UserUID);
    
    if ($userData['balance_blocked'] == 1) {
        echo json_encode(['success' => false, 'message' => 'Your Balance is Locked by System']);
        exit;
    }
    
    if ($tokenAmount <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid token amount']);
        exit;
    }
    
    $spexcon->begin_transaction();
    
    $userTokenQuery = $spexcon->prepare("
        SELECT balance FROM user_tokens 
        WHERE user_id = ? AND coin_symbol = ?
    ");
    $userTokenQuery->bind_param("is", $UserUID, $tokenSymbol);
    $userTokenQuery->execute();
    $userToken = $userTokenQuery->get_result()->fetch_assoc();
    
    if (!$userToken || $userToken['balance'] < $tokenAmount) {
        throw new Exception('Insufficient token balance');
    }
    
    $presaleQuery = $spexcon->prepare("
        SELECT price FROM presales 
        WHERE coin_symbol = ?
    ");
    $presaleQuery->bind_param("s", $tokenSymbol);
    $presaleQuery->execute();
    $presale = $presaleQuery->get_result()->fetch_assoc();
    
    if (!$presale) {
        throw new Exception('Token not found');
    }
    
    $expectedUSDT = $tokenAmount * $presale['price'];
    if (abs($usdtAmount - $expectedUSDT) > 0.01) {
        throw new Exception('Price calculation error');
    }
    
    $newTokenBalance = $userToken['balance'] - $tokenAmount;
    
    if ($newTokenBalance > 0) {
        $updateTokenQuery = $spexcon->prepare("
            UPDATE user_tokens 
            SET balance = ?, updated_at = NOW() 
            WHERE user_id = ? AND coin_symbol = ?
        ");
        $updateTokenQuery->bind_param("dis", $newTokenBalance, $UserUID, $tokenSymbol);
        $updateTokenQuery->execute();
    } else {
        $deleteTokenQuery = $spexcon->prepare("
            DELETE FROM user_tokens 
            WHERE user_id = ? AND coin_symbol = ?
        ");
        $deleteTokenQuery->bind_param("is", $UserUID, $tokenSymbol);
        $deleteTokenQuery->execute();
    }
    
    $newBalance = $userData['balance'] + $usdtAmount;
    $updateBalanceQuery = $spexcon->prepare("UPDATE users SET balance = ? WHERE uid = ?");
    $updateBalanceQuery->bind_param("di", $newBalance, $UserUID);
    $updateBalanceQuery->execute();
    
    $spexcon->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Tokens sold successfully',
        'new_balance' => $newBalance,
        'usdt_received' => $usdtAmount
    ]);
    
} catch (Exception $e) {
    $spexcon->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>