<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

$UserUID = getSession('UID');
$sessionTxHash = getSession('payment_tx');
$sessionAmount = getSession('payment_amount');

if (empty($UserUID) || empty($sessionTxHash) || empty($sessionAmount)) {
    echo json_encode(['success' => false, 'message' => 'Invalid session']);
    exit;
}

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

$txHash = $input['tx_hash'] ?? '';
$blockchainTx = $input['blockchain_tx'] ?? '';
$amount = floatval($input['amount'] ?? 0);
$network = $input['network'] ?? '';
$address = $input['address'] ?? '';

if ($txHash !== $sessionTxHash || $amount != $sessionAmount) {
    echo json_encode(['success' => false, 'message' => 'Transaction mismatch']);
    exit;
}

if (empty($blockchainTx)) {
    echo json_encode(['success' => false, 'message' => 'Transaction ID required']);
    exit;
}

try {
    $spexcon->begin_transaction();
    
    $checkQuery = "SELECT * FROM user_transactions 
                   WHERE transaction_hash = ? 
                   AND user_id = ? 
                   AND status = 'PENDING'";
    
    $stmt = $spexcon->prepare($checkQuery);
    $stmt->bind_param("si", $txHash, $UserUID);
    $stmt->execute();
    $result = $stmt->get_result();
    $transaction = $result->fetch_assoc();
    
    if (!$transaction) {
        throw new Exception('Transaction not found');
    }
    
    // Transaction'ı güncelle - status PENDING kalacak, sadece blockchain tx id eklenecek
    $updateQuery = "UPDATE user_transactions 
                    SET order_id = ?,
                        from_address = ?,
                        notes = CONCAT(IFNULL(notes, ''), ' | Network: ', ?, ' | Blockchain TX: ', ?, ' | Waiting for admin approval | Submitted at: ', NOW())
                    WHERE id = ?";
    
    $stmt = $spexcon->prepare($updateQuery);
    $stmt->bind_param("ssssi", $blockchainTx, $address, $network, $blockchainTx, $transaction['id']);
    $stmt->execute();
    
    unset($_SESSION['payment_tx']);
    unset($_SESSION['payment_amount']);
    unset($_SESSION['payment_user']);
    
    PushSend($UserUID, 'Payment Submitted', 
             'Your deposit of $' . number_format($amount, 2) . ' is waiting for approval', 
             'deposit');
    
    $spexcon->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Payment submitted successfully. Waiting for admin approval.'
    ]);
    
} catch (Exception $e) {
    $spexcon->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>