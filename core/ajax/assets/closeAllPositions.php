<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

if (!getSession('Login')) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$UserUID = getSession('UID');

$UserData = FetchUserData($UserUID);
if ($UserData['balance_blocked'] == 1) {
    echo json_encode(['success' => false, 'message' => 'Your Balance is Locked by System']);
    exit;
}

try {
    $spexcon->begin_transaction();
    
    // Get all open positions for the user
    $positionsQuery = "
        SELECT id, coin_id, position_type, position_size, margin_amount, unrealized_pnl
        FROM user_futures_positions 
        WHERE user_id = ? AND status = 'OPEN'
    ";
    $stmt = $spexcon->prepare($positionsQuery);
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $positions = [];
    $totalReturnAmount = 0;
    $positionCount = 0;
    
    while ($row = $result->fetch_assoc()) {
        $positions[] = $row;
        $returnAmount = floatval($row['margin_amount']) + floatval($row['unrealized_pnl']);
        $totalReturnAmount += $returnAmount;
        $positionCount++;
    }
    
    if ($positionCount === 0) {
        $spexcon->rollback();
        echo json_encode(['success' => false, 'message' => 'No open positions to close']);
        exit;
    }

    $closeQuery = "
        UPDATE user_futures_positions 
        SET status = 'CLOSED', closed_at = NOW()
        WHERE user_id = ? AND status = 'OPEN'
    ";
    $stmt = $spexcon->prepare($closeQuery);
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    
    $affectedRows = $stmt->affected_rows;
    
    if ($affectedRows === 0) {
        $spexcon->rollback();
        echo json_encode(['success' => false, 'message' => 'No positions were closed']);
        exit;
    }
    
    if ($totalReturnAmount > 0) {
        $balanceQuery = "UPDATE users SET balance = balance + ? WHERE uid = ?";
        $stmt = $spexcon->prepare($balanceQuery);
        $stmt->bind_param("di", $totalReturnAmount, $UserUID);
        $stmt->execute();
    }
    
    $coinSummary = [];
    foreach ($positions as $pos) {
        $coin = $pos['coin_id'];
        if (!isset($coinSummary[$coin])) {
            $coinSummary[$coin] = 0;
        }
        $coinSummary[$coin]++;
    }
    
    $logMsg = "All futures positions closed ({$positionCount} positions): " . 
              implode(', ', array_map(function($coin, $count) {
                  return "{$coin} ({$count})";
              }, array_keys($coinSummary), array_values($coinSummary)));
    
    $logAmount = number_format($totalReturnAmount, 2);
    
    $logQuery = "
        INSERT INTO user_logs (uid, type, msg, amount, cat, date) 
        VALUES (?, 'Futures Close All', ?, ?, 'trade', NOW())
    ";
    $stmt = $spexcon->prepare($logQuery);
    $stmt->bind_param("sss", $UserUID, $logMsg, $logAmount);
    $stmt->execute();
    PushSend($UserUID,'All futures position closed',$logMsg,'trade');
    $spexcon->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => "Successfully closed {$positionCount} position(s)",
        'positions_closed' => $positionCount,
        'total_returned' => $totalReturnAmount
    ]);
    
} catch (Exception $e) {
    $spexcon->rollback();
    error_log("Close All Positions Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error closing positions']);
}
?>