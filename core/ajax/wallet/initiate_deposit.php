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
$amount = floatval($input['amount'] ?? 0);
$cryptoAmount = $input['cryptoAmount'] ?? '';
$gateway = $input['gateway'] ?? '';

$WebsData = FetchWebSettings();
$minDeposit = floatval($WebsData['minDeposit']);

if ($amount < $minDeposit) {
    echo json_encode(['success' => false, 'message' => 'Amount below minimum deposit']);
    exit;
}

$validGateways = ['BTC', 'ETH', 'USDT', 'TRC20', 'BEP20'];
if (!in_array($gateway, $validGateways)) {
    echo json_encode(['success' => false, 'message' => 'Invalid gateway selected']);
    exit;
}

try {
    $txHash = 'DEP_' . $UserUID . '_' . time() . '_' . rand(1000, 9999);
    
    $insertQuery = "INSERT INTO user_transactions 
                    (user_id, coin_id, transaction_type, amount, 
                     price_per_unit_usd, total_value_usd, status, 
                     transaction_hash, notes, created_at) 
                    VALUES (?, 'usd', 'DEPOSIT', ?, 1, ?, 'PENDING', 
                            ?, ?, current_timestamp())";
    
    $notes = "LivePayment Deposit - Gateway: " . $gateway;
    
    $stmt = $spexcon->prepare($insertQuery);
    $stmt->bind_param("iddss", $UserUID, $amount, $amount, $txHash, $notes);
    $stmt->execute();
    
    // Sessions SETTER
    setSession('payment_tx', $txHash);
    setSession('payment_amount', $amount);
    setSession('payment_user', $UserUID);
    setSession('payment_gateway', $gateway);
    setSession('payment_cryptoAmount',$cryptoAmount);
    
    PushSend($UserUID, 'Deposit Initiated', 
             'Deposit of $' . number_format($amount, 2) . ' via ' . $gateway . ' initiated', 'deposit');
    
    $paymentUrl = "?r=LivePayment";
    
    echo json_encode([
        'success' => true,
        'paymentUrl' => $paymentUrl,
        'transactionId' => $txHash
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to initiate deposit'
    ]);
}
?>