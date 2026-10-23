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

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['position_id']) || !isset($input['size'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$positionId = intval($input['position_id']);
$closeSize = floatval($input['size']);

if ($positionId <= 0 || $closeSize <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid position ID or size']);
    exit;
}

try {
    $spexcon->begin_transaction();
    
    $positionQuery = "
        SELECT id, user_id, position_size, margin_amount, unrealized_pnl, position_type, coin_id
        FROM user_futures_positions 
        WHERE id = ? AND user_id = ? AND status = 'OPEN'
    ";
    $stmt = $spexcon->prepare($positionQuery);
    $stmt->bind_param("ii", $positionId, $UserUID);
    $stmt->execute();
    $result = $stmt->get_result();
    $position = $result->fetch_assoc();
    
    if (!$position) {
        $spexcon->rollback();
        echo json_encode(['success' => false, 'message' => 'Position not found or already closed']);
        exit;
    }
    
    $currentSize = floatval($position['position_size']);
    $marginAmount = floatval($position['margin_amount']);
    $unrealizedPnL = floatval($position['unrealized_pnl']);
    
    if ($closeSize > $currentSize) {
        $spexcon->rollback();
        echo json_encode(['success' => false, 'message' => 'Close size cannot exceed position size']);
        exit;
    }
    
    $returnAmount = 0;
    $logMsg = "";
    $logAmount = "0.00";
    
    if ($closeSize >= $currentSize) {
        $updateQuery = "
            UPDATE user_futures_positions 
            SET status = 'CLOSED', closed_at = NOW()
            WHERE id = ?
        ";
        $stmt = $spexcon->prepare($updateQuery);
        $stmt->bind_param("i", $positionId);
        $stmt->execute();
        
        $returnAmount = $marginAmount + $unrealizedPnL;
        
        $logMsg = "Futures position closed: {$position['coin_id']} {$position['position_type']} - Size: {$currentSize}";
        
        PushSend($UserUID, 'Future position closed', $logMsg, 'trade');
        
    } else {
        $remainingSize = $currentSize - $closeSize;
        $closingPercentage = $closeSize / $currentSize;
        
        $returnAmount = ($marginAmount + $unrealizedPnL) * $closingPercentage;
        $remainingMargin = $marginAmount * (1 - $closingPercentage);
        $remainingPnL = $unrealizedPnL * (1 - $closingPercentage);
        
        $updateQuery = "
            UPDATE user_futures_positions 
            SET position_size = ?, margin_amount = ?, unrealized_pnl = ?
            WHERE id = ?
        ";
        $stmt = $spexcon->prepare($updateQuery);
        $stmt->bind_param("dddi", $remainingSize, $remainingMargin, $remainingPnL, $positionId);
        $stmt->execute();
        
        $logMsg = "Futures position partially closed: {$position['coin_id']} {$position['position_type']} - Closed: {$closeSize}, Remaining: {$remainingSize}";

        PushSend($UserUID, 'Future position closed', 
            "Position partially closed: {$position['coin_id']} {$position['position_type']} - Closed: {$closeSize}, Remaining: {$remainingSize}", 
            'trade');
    }
    
    $balanceQuery = "UPDATE users SET balance = balance + ? WHERE uid = ?";
    $stmt = $spexcon->prepare($balanceQuery);
    $stmt->bind_param("di", $returnAmount, $UserUID);
    $stmt->execute();
    
    $logAmount = number_format(abs($returnAmount), 2);
    
    if ($returnAmount >= 0) {
        $logType = 'Futures Close';
        $detailedMsg = $logMsg . ($returnAmount > $marginAmount ? " - Profit: $" . number_format($returnAmount - $marginAmount, 2) : "");
    } else {
        $logType = 'Futures Close';
        $detailedMsg = $logMsg . " - Loss: $" . number_format(abs($returnAmount), 2);
    }
    
    $logQuery = "
        INSERT INTO user_logs (uid, type, msg, amount, cat, date) 
        VALUES (?, ?, ?, ?, 'trade', NOW())
    ";
    $stmt = $spexcon->prepare($logQuery);
    $stmt->bind_param("ssss", $UserUID, $logType, $detailedMsg, $logAmount);
    $stmt->execute();
    
    $spexcon->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Position closed successfully',
        'returned_amount' => $returnAmount,
        'is_profit' => $returnAmount >= ($marginAmount * ($closeSize / $currentSize)),
        'position_fully_closed' => $closeSize >= $currentSize
    ]);
    
} catch (Exception $e) {
    $spexcon->rollback();
    error_log("Close Position Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error closing position: ' . $e->getMessage()]);
}
?>