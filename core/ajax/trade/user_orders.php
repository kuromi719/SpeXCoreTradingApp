<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $openOrdersQuery = $spexcon->prepare("
        SELECT id, CONCAT(UPPER(coin_id), 'USDT') as symbol, order_side as side, 
               order_type as type, price_per_unit_usd as price, amount as origQty, 
               filled_quantity as executedQty, status, created_at as time, order_id as orderId,
               total_value_usd as total, trigger_price
        FROM user_transactions 
        WHERE user_id = ? AND status = 'PENDING' 
        ORDER BY created_at DESC LIMIT 50
    ");
    $openOrdersQuery->bind_param("i", $UserUID);
    $openOrdersQuery->execute();
    $openOrders = $openOrdersQuery->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $orderHistoryQuery = $spexcon->prepare("
        SELECT id, CONCAT(UPPER(coin_id), 'USDT') as symbol, order_side as side, 
               order_type as type, price_per_unit_usd as price, amount as origQty, 
               filled_quantity as executedQty, status, created_at as time, order_id as orderId,
               total_value_usd as total, average_price, executed_at
        FROM user_transactions 
        WHERE user_id = ? AND status IN ('COMPLETED', 'CANCELLED', 'FAILED')
        ORDER BY created_at DESC LIMIT 100
    ");
    $orderHistoryQuery->bind_param("i", $UserUID);
    $orderHistoryQuery->execute();
    $orderHistory = $orderHistoryQuery->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $tradeHistoryQuery = $spexcon->prepare("
        SELECT CONCAT(UPPER(coin_id), 'USDT') as symbol, 
               COALESCE(average_price, price_per_unit_usd) as price, 
               filled_quantity as qty, 0 as commission, 'USDT' as commissionAsset,
               CASE WHEN order_side = 'BUY' THEN 1 ELSE 0 END as isBuyer, 
               COALESCE(executed_at, created_at) as time, order_id,
               total_value_usd as total
        FROM user_transactions 
        WHERE user_id = ? AND status = 'COMPLETED' 
        AND transaction_type IN ('MARKET_BUY', 'MARKET_SELL', 'LIMIT_BUY', 'LIMIT_SELL')
        ORDER BY COALESCE(executed_at, created_at) DESC LIMIT 100
    ");
    $tradeHistoryQuery->bind_param("i", $UserUID);
    $tradeHistoryQuery->execute();
    $tradeHistory = $tradeHistoryQuery->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $fundsQuery = $spexcon->prepare("
        SELECT ucb.coin_id as asset, ucb.available_balance as free, 
               ucb.locked_balance as locked, cmd.current_price_usd as usdPrice
        FROM user_coin_balances ucb
        LEFT JOIN coin_market_data cmd ON ucb.coin_id = cmd.coin_id
        WHERE ucb.user_id = ? AND ucb.total_balance > 0
    ");
    $fundsQuery->bind_param("i", $UserUID);
    $fundsQuery->execute();
    $funds = $fundsQuery->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $usdtQuery = $spexcon->prepare("SELECT balance FROM users WHERE uid = ?");
    $usdtQuery->bind_param("i", $UserUID);
    $usdtQuery->execute();
    $usdtResult = $usdtQuery->get_result()->fetch_assoc();
    
    if ($usdtResult && floatval($usdtResult['balance']) > 0) {
        $funds[] = [
            'asset' => 'USDT',
            'free' => floatval($usdtResult['balance']),
            'locked' => 0,
            'usdPrice' => 1.0
        ];
    }
    
    echo json_encode([
        'success' => true,
        'open_orders' => $openOrders,
        'order_history' => $orderHistory,
        'trade_history' => $tradeHistory,
        'funds' => $funds
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error',
        'open_orders' => [],
        'order_history' => [],
        'trade_history' => [],
        'funds' => []
    ]);
}
?>