<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

$txHash = $input['tx_hash'] ?? '';

if (empty($txHash)) {
    echo json_encode(['success' => false, 'message' => 'Transaction hash required']);
    exit;
}

try {
    $query = "SELECT status, amount, total_value_usd, created_at, order_id 
              FROM user_transactions 
              WHERE transaction_hash = ? 
              AND user_id = ?";
    
    $stmt = $spexcon->prepare($query);
    $stmt->bind_param("si", $txHash, $UserUID);
    $stmt->execute();
    $result = $stmt->get_result();
    $transaction = $result->fetch_assoc();
    
    if (!$transaction) {
        echo json_encode([
            'success' => false,
            'message' => 'Transaction not found'
        ]);
        exit;
    }
    
    // transaction 15 dakikadan eski ve hala pending ise iptal et
    $createdTime = strtotime($transaction['created_at']);
    $currentTime = time();
    $timeDiff = $currentTime - $createdTime;
    
    if ($transaction['status'] === 'PENDING' && $timeDiff > 900) { // 15 dakika
        $updateQuery = "UPDATE user_transactions 
                       SET status = 'FAILED',
                           notes = CONCAT(IFNULL(notes, ''), ' | Timeout at: ', NOW())
                       WHERE transaction_hash = ?";
        
        $stmt = $spexcon->prepare($updateQuery);
        $stmt->bind_param("s", $txHash);
        $stmt->execute();
        
        // clearSessions
        unset($_SESSION['payment_tx']);
        unset($_SESSION['payment_amount']);
        unset($_SESSION['payment_user']);
        
        echo json_encode([
            'success' => true,
            'status' => 'FAILED',
            'message' => 'Payment timeout'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'status' => $transaction['status'],
            'amount' => $transaction['total_value_usd'],
            'blockchain_tx' => $transaction['order_id'],
            'time_left' => max(0, 900 - $timeDiff)
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error checking payment status'
    ]);
}
?>