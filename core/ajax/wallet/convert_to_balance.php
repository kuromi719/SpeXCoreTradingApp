<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$UserData = FetchUserData($UserUID);
if ($UserData['balance_blocked'] == 1) {
    echo json_encode(['success' => false, 'message' => 'Your Balance is Locked by System']);
    exit;
}

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

$coin_id = $input['coin_id'] ?? '';
$amount = floatval($input['amount'] ?? 0);
$usd_value = floatval($input['usd_value'] ?? 0);

if (empty($coin_id) || $amount <= 0 || $usd_value <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid conversion data']);
    exit;
}

try {
    $spexcon->begin_transaction();
    
    $checkQuery = "SELECT available_balance, current_price_usd 
                   FROM user_coin_balances ucb
                   LEFT JOIN coin_market_data cmd ON ucb.coin_id = cmd.coin_id
                   WHERE ucb.user_id = ? AND ucb.coin_id = ?";
    $stmt = $spexcon->prepare($checkQuery);
    $stmt->bind_param("is", $UserUID, $coin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $coinBalance = $result->fetch_assoc();
    
    if (!$coinBalance || floatval($coinBalance['available_balance']) < $amount) {
        throw new Exception('Insufficient coin balance');
    }
    
    $currentPrice = floatval($coinBalance['current_price_usd']);
    $calculatedValue = $amount * $currentPrice;
    
    $updateCoinQuery = "UPDATE user_coin_balances 
                        SET available_balance = available_balance - ?,
                            total_balance = total_balance - ?
                        WHERE user_id = ? AND coin_id = ?";
    $stmt = $spexcon->prepare($updateCoinQuery);
    $stmt->bind_param("ddis", $amount, $amount, $UserUID, $coin_id);
    $stmt->execute();
    
    $updateBalanceQuery = "UPDATE users SET balance = balance + ? WHERE uid = ?";
    $stmt = $spexcon->prepare($updateBalanceQuery);
    $stmt->bind_param("di", $calculatedValue, $UserUID);
    $stmt->execute();
    
    $insertTxQuery = "INSERT INTO user_transactions 
                      (user_id, coin_id, transaction_type, amount, 
                       price_per_unit_usd, total_value_usd, fee_amount, 
                       fee_coin_id, fee_value_usd, exchange_rate, 
                       status, notes, created_at) 
                      VALUES (?, ?, 'SELL', ?, ?, ?, 0, 'usd', 0, 1, 
                              'COMPLETED', 'Converted to USD balance', current_timestamp())";
    
    $stmt = $spexcon->prepare($insertTxQuery);
    $stmt->bind_param("isddd", $UserUID, $coin_id, $amount, $currentPrice, $calculatedValue);
    $stmt->execute();
    
    PushSend($UserUID, 'Conversion Successful', 
             'Converted ' . number_format($amount, 8) . ' ' . strtoupper($coin_id) . 
             ' to $' . number_format($calculatedValue, 2) . ' USD', 'convert');
    
    $spexcon->commit();
    
    $newBalance = floatval($UserData['balance']) + $calculatedValue;
    
    echo json_encode([
        'success' => true,
        'message' => 'Conversion completed successfully',
        'new_balance' => $newBalance,
        'converted_amount' => $calculatedValue
    ]);
    
} catch (Exception $e) {
    $spexcon->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>