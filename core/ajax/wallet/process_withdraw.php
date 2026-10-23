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

$network = $input['network'] ?? '';
$address = $input['address'] ?? '';
$amount = floatval($input['amount'] ?? 0);
$verificationCode = $input['verificationCode'] ?? '';

$sessionCode = getSession('withdraw_verification_code');
$codeTime = getSession('withdraw_verification_time');
$sessionData = json_decode(getSession('withdraw_verification_data'), true);

if (empty($sessionCode) || empty($verificationCode)) {
    echo json_encode(['success' => false, 'message' => 'Verification code required']);
    exit;
}

if ((time() - $codeTime) > 600) {
    unset($_SESSION['withdraw_verification_code']);
    unset($_SESSION['withdraw_verification_time']);
    unset($_SESSION['withdraw_verification_data']);
    echo json_encode(['success' => false, 'message' => 'Verification code expired']);
    exit;
}

if ($verificationCode !== $sessionCode) {
    echo json_encode(['success' => false, 'message' => 'Invalid verification code']);
    exit;
}

if ($sessionData['amount'] != $amount || $sessionData['address'] != $address) {
    echo json_encode(['success' => false, 'message' => 'Withdrawal data mismatch']);
    exit;
}

unset($_SESSION['withdraw_verification_code']);
unset($_SESSION['withdraw_verification_time']);
unset($_SESSION['withdraw_verification_data']);

$WebsData = FetchWebSettings();
$minWithdraw = floatval($WebsData['minWithdraw']);
$dailyLimit = floatval($WebsData['daily_withdraw_limit']);

$validNetworks = ['trc20', 'erc20', 'bep20', 'polygon', 'arbitrum', 'optimism', 'avalanche', 'solana'];

if (!in_array($network, $validNetworks)) {
    echo json_encode(['success' => false, 'message' => 'Invalid network selected']);
    exit;
}

if (empty($address)) {
    echo json_encode(['success' => false, 'message' => 'Withdrawal address required']);
    exit;
}

if ($amount < $minWithdraw) {
    echo json_encode(['success' => false, 'message' => 'Amount below minimum withdrawal']);
    exit;
}

if ($amount > floatval($UserData['balance'])) {
    echo json_encode(['success' => false, 'message' => 'Insufficient balance']);
    exit;
}

try {
    $todayQuery = "SELECT SUM(total_value_usd) as today_total 
                   FROM user_transactions 
                   WHERE user_id = ? 
                   AND transaction_type = 'WITHDRAWAL' 
                   AND status IN ('PENDING', 'COMPLETED')
                   AND DATE(created_at) = CURDATE()";
    
    $stmt = $spexcon->prepare($todayQuery);
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    $result = $stmt->get_result();
    $todayTotal = floatval($result->fetch_assoc()['today_total'] ?? 0);
    
    if (($todayTotal + $amount) > $dailyLimit) {
        echo json_encode(['success' => false, 'message' => 'Daily withdrawal limit exceeded']);
        exit;
    }
    
    $spexcon->begin_transaction();
    
    $updateQuery = "UPDATE users SET balance = balance - ? WHERE uid = ? AND balance >= ?";
    $stmt = $spexcon->prepare($updateQuery);
    $stmt->bind_param("did", $amount, $UserUID, $amount);
    $stmt->execute();
    
    if ($stmt->affected_rows === 0) {
        throw new Exception('Failed to update balance');
    }
    
    $txHash = 'WD_' . $UserUID . '_' . time() . '_' . rand(1000, 9999);
    
    $insertQuery = "INSERT INTO user_transactions 
                    (user_id, coin_id, transaction_type, amount, 
                     price_per_unit_usd, total_value_usd, status, 
                     to_address, transaction_hash, notes, created_at) 
                    VALUES (?, 'usdt', 'WITHDRAWAL', ?, 1, ?, 'PENDING', 
                            ?, ?, ?, current_timestamp())";
    
    $notes = "Network: " . strtoupper($network) . " | Email verified";
    
    $stmt = $spexcon->prepare($insertQuery);
    $stmt->bind_param("iddsss", $UserUID, $amount, $amount, $address, $txHash, $notes);
    $stmt->execute();
    
    PushSend($UserUID, 'Withdrawal Request', 
             'Withdrawal of ' . number_format($amount, 2) . ' USDT is being processed', 
             'withdraw');
    
    $spexcon->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Withdrawal request submitted successfully',
        'transaction_id' => $txHash,
        'amount' => $amount,
        'network' => $network
    ]);
    
} catch (Exception $e) {
    $spexcon->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>