<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$orderId = $input['orderId'] ?? '';

if (empty($orderId)) {
    echo json_encode(['success' => false, 'message' => 'Order ID required']);
    exit;
}

try {
    $spexcon->begin_transaction();
    
    $orderResult = $spexcon->query("
        SELECT * FROM user_transactions 
        WHERE order_id = '$orderId' AND user_id = $UserUID AND status = 'PENDING' 
        LIMIT 1
    ");
    
    if (!$orderResult || $orderResult->num_rows === 0) {
        throw new Exception("Order not found or cannot be cancelled");
    }
    
    $order = $orderResult->fetch_assoc();
    $orderSide = $order['order_side'];
    $amount = floatval($order['amount']);
    $price = floatval($order['price_per_unit_usd']);
    $totalValue = floatval($order['total_value_usd']);
    $coinId = $order['coin_id'];
    
    $spexcon->query("UPDATE user_transactions SET status = 'CANCELLED' WHERE order_id = '$orderId'");
    
    if ($orderSide === 'BUY') {
        $spexcon->query("UPDATE users SET balance = balance + $totalValue WHERE uid = $UserUID");
        
        PushSend($UserUID, 'Order Cancelled', 
            'Your BUY order cancelled. $' . number_format($totalValue, 2) . ' USDT refunded to your balance', 
            'trade');
            
    } elseif ($orderSide === 'SELL') {
        $spexcon->query("
            UPDATE user_coin_balances 
            SET available_balance = available_balance + $amount 
            WHERE user_id = $UserUID AND coin_id = '$coinId'
        ");
        
        $coinResult = $spexcon->query("SELECT symbol FROM coins WHERE id = '$coinId' LIMIT 1");
        $coinData = $coinResult->fetch_assoc();
        $coinSymbol = strtoupper($coinData['symbol'] ?? $coinId);
        
        PushSend($UserUID, 'Order Cancelled', 
            'Your SELL order cancelled. ' . number_format($amount, 8) . ' ' . $coinSymbol . ' refunded to your balance', 
            'trade');
    }
    
    $spexcon->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Order cancelled and balance refunded successfully'
    ]);
    
} catch (Exception $e) {
    $spexcon->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>