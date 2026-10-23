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

$userResult = $spexcon->query("SELECT balance, balance_blocked FROM users WHERE uid = $UserUID LIMIT 1");
$userData = $userResult->fetch_assoc();
    
if (!$userData) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}
    
$userBalance = floatval($userData['balance']);
$balanceBlocked = intval($userData['balance_blocked']);
    
if ($balanceBlocked === 1) {
    echo json_encode(['success' => false, 'message' => 'Your Balance is Locked by System']);
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
        throw new Exception("Order not found or cannot be closed");
    }
    
    $order = $orderResult->fetch_assoc();
    $orderSide = $order['order_side'];
    $amount = floatval($order['amount']);
    $orderPrice = floatval($order['price_per_unit_usd']);
    $coinId = $order['coin_id'];
    
    $priceResult = $spexcon->query("SELECT current_price_usd FROM coin_market_data WHERE coin_id = '$coinId' LIMIT 1");
    $priceData = $priceResult->fetch_assoc();
    $currentPrice = floatval($priceData['current_price_usd'] ?? 118870.00);
    
    $coinResult = $spexcon->query("SELECT symbol FROM coins WHERE id = '$coinId' LIMIT 1");
    $coinData = $coinResult->fetch_assoc();
    $coinSymbol = strtoupper($coinData['symbol'] ?? $coinId);
    
    // Calculate P&L and execute opposite market order
    $closeOrderId = 'CLOSE_' . time() . '_' . rand(1000, 9999);
    $pnl = 0;
    
    if ($orderSide === 'BUY') {
        // Close BUY order = SELL at market price
        $sellValue = $amount * $currentPrice;
        $originalCost = $amount * $orderPrice;
        $pnl = $sellValue - $originalCost;
        
        // Add USDT to balance (sell proceeds)
        $spexcon->query("UPDATE users SET balance = balance + $sellValue WHERE uid = $UserUID");
        
        $spexcon->query("
            INSERT INTO user_transactions (
                user_id, coin_id, transaction_type, order_type, order_side, 
                amount, price_per_unit_usd, total_value_usd, order_id, 
                filled_quantity, status, executed_at, notes
            ) VALUES (
                $UserUID, '$coinId', 'MARKET_SELL_CLOSE', 'market', 'SELL', 
                $amount, $currentPrice, $sellValue, '$closeOrderId', 
                $amount, 'COMPLETED', NOW(), 'Close order: $orderId'
            )
        ");
        
        // Remove coin from user balance (we're selling it)
        $spexcon->query("
            UPDATE user_coin_balances 
            SET total_balance = total_balance - $amount, available_balance = available_balance - $amount 
            WHERE user_id = $UserUID AND coin_id = '$coinId'
        ");
        
    } elseif ($orderSide === 'SELL') {
        // Close SELL order = BUY at market price
        $buyCost = $amount * $currentPrice;
        $originalValue = $amount * $orderPrice;
        $pnl = $originalValue - $buyCost;
        
        // Subtract USDT from balance (buy cost)
        $spexcon->query("UPDATE users SET balance = balance - $buyCost WHERE uid = $UserUID");
        
        $spexcon->query("
            INSERT INTO user_transactions (
                user_id, coin_id, transaction_type, order_type, order_side, 
                amount, price_per_unit_usd, total_value_usd, order_id, 
                filled_quantity, status, executed_at, notes
            ) VALUES (
                $UserUID, '$coinId', 'MARKET_BUY_CLOSE', 'market', 'BUY', 
                $amount, $currentPrice, $buyCost, '$closeOrderId', 
                $amount, 'COMPLETED', NOW(), 'Close order: $orderId'
            )
        ");
        
        // Add coin to user balance (we're buying it)
        $spexcon->query("
            INSERT INTO user_coin_balances (user_id, coin_id, total_balance, available_balance) 
            VALUES ($UserUID, '$coinId', $amount, $amount) 
            ON DUPLICATE KEY UPDATE 
            total_balance = total_balance + $amount, 
            available_balance = available_balance + $amount
        ");
        
        // Return locked coin balance from original sell order
        $spexcon->query("
            UPDATE user_coin_balances 
            SET available_balance = available_balance + $amount 
            WHERE user_id = $UserUID AND coin_id = '$coinId'
        ");
    }
    
    // Mark original order as closed
    $spexcon->query("UPDATE user_transactions SET status = 'CLOSED', notes = 'Closed at market price' WHERE order_id = '$orderId'");
    
    // Calculate final P&L for notification
    $pnlText = $pnl >= 0 ? '+$' . number_format($pnl, 2) : '-$' . number_format(abs($pnl), 2);
    $pnlColor = $pnl >= 0 ? 'profit' : 'loss';

    PushSend($UserUID, 'Order Closed', 
        'Order closed at market price: ' . number_format($amount, 8) . ' ' . $coinSymbol . 
        ' | P&L: ' . $pnlText . ' | Price: $' . number_format($currentPrice, 2), 
        'trade');
    
    $spexcon->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Order closed successfully',
        'pnl' => $pnl,
        'pnlText' => $pnlText,
        'closePrice' => $currentPrice,
        'originalPrice' => $orderPrice
    ]);
    
} catch (Exception $e) {
    $spexcon->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>