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
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['coin_id']) || !isset($input['position_type']) || !isset($input['margin_amount'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }

    $coinId = Sanitize($input['coin_id']);
    $positionType = strtoupper(Sanitize($input['position_type']));
    $positionSize = floatval($input['position_size']);
    $entryPrice = floatval($input['entry_price']);
    $marginAmount = floatval($input['margin_amount']);
    $leverage = floatval($input['leverage']);
    $liquidationPrice = isset($input['liquidation_price']) ? floatval($input['liquidation_price']) : 0;

    if (!in_array($positionType, ['LONG', 'SHORT'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid position type']);
        exit;
    }

    if ($marginAmount < 1) {
        echo json_encode(['success' => false, 'message' => 'Minimum margin amount is $1.00']);
        exit;
    }

    global $spexcon;
    
    $spexcon->begin_transaction();
    
    $balanceQuery = "SELECT balance FROM users WHERE uid = ?";
    $stmt = $spexcon->prepare($balanceQuery);
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    
    if (!$userData || floatval($userData['balance']) < $marginAmount) {
        $spexcon->rollback();
        echo json_encode(['success' => false, 'message' => 'Insufficient balance']);
        exit;
    }
    
    $coinQuery = "SELECT id, symbol FROM coins WHERE id = ?";
    $stmt = $spexcon->prepare($coinQuery);
    $stmt->bind_param("s", $coinId);
    $stmt->execute();
    $result = $stmt->get_result();
    $coinData = $result->fetch_assoc();
    
    if (!$coinData) {
        $spexcon->rollback();
        echo json_encode(['success' => false, 'message' => 'Invalid coin selected']);
        exit;
    }

    $deductQuery = "UPDATE users SET balance = balance - ? WHERE uid = ?";
    $stmt = $spexcon->prepare($deductQuery);
    $stmt->bind_param("di", $marginAmount, $UserUID);
    $stmt->execute();
    
    $insertQuery = "
        INSERT INTO user_futures_positions 
        (user_id, coin_id, position_type, position_size, entry_price, current_price, margin_amount, leverage, liquidation_price, unrealized_pnl, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0.00000000, 'OPEN')
    ";
    $stmt = $spexcon->prepare($insertQuery);
    $stmt->bind_param("issdddddd", $UserUID, $coinId, $positionType, $positionSize, $entryPrice, $entryPrice, $marginAmount, $leverage, $liquidationPrice);
    $stmt->execute();
    
    $positionId = $spexcon->insert_id;
    
    $coinSymbol = strtoupper($coinData['symbol']);
    $logMsg = "Futures position opened: {$coinSymbol} {$positionType} {$leverage}x - Size: {$positionSize}";
    $logAmount = number_format($marginAmount, 2);
    
    $logQuery = "INSERT INTO user_logs (uid, type, msg, amount, cat) VALUES (?, 'Futures Open', ?, ?, 'trade')";
    $stmt = $spexcon->prepare($logQuery);
    $stmt->bind_param("sss", $UserUID, $logMsg, $logAmount);
    $stmt->execute();
    
    $pushTitle = "Futures Position Opened";
    $pushMsg = "Your {$coinSymbol} {$positionType} position with {$leverage}x leverage has been opened successfully.";
    
    try {
        PushSend($UserUID, $pushTitle, $pushMsg, 'trade');
    } catch (Exception $e) {
        error_log("Push notification failed: " . $e->getMessage());
    }
    
    $spexcon->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Futures position opened successfully!',
        'position_id' => $positionId,
        'coin_symbol' => $coinSymbol
    ]);
    
} catch (Exception $e) {
    if (isset($spexcon)) {
        $spexcon->rollback();
    }
    error_log("Open Futures Position Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error opening position: ' . $e->getMessage()]);
}
?>