<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../system/spex_v4_func.php';

try {
    $UserUID = getSession('UID');
    if (empty($UserUID)) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    
    $type = $input['type'] ?? '';
    $side = $input['side'] ?? '';
    $symbol = $input['symbol'] ?? '';
    $price = floatval($input['price'] ?? 0);
    $amount = floatval($input['amount'] ?? 0);
    $quoteOrderQty = floatval($input['quoteOrderQty'] ?? 0);
    $stopPrice = floatval($input['stopPrice'] ?? 0);

    if (empty($type) || empty($side) || empty($symbol)) {
        echo json_encode(['success' => false, 'message' => 'Missing parameters']);
        exit;
    }

    $baseCoin = strtolower(str_replace(['USDT', 'usdt'], '', $symbol));
    
    $coinQueries = [
        "SELECT id, symbol, name FROM coins WHERE id = '$baseCoin' LIMIT 1",
        "SELECT id, symbol, name FROM coins WHERE LOWER(symbol) = '$baseCoin' LIMIT 1",
        "SELECT id, symbol, name FROM coins WHERE UPPER(symbol) = '" . strtoupper($baseCoin) . "' LIMIT 1",
        "SELECT id, symbol, name FROM coins WHERE symbol LIKE '%$baseCoin%' LIMIT 1"
    ];
    
    $coinData = null;
    foreach ($coinQueries as $query) {
        $result = $spexcon->query($query);
        if ($result && $result->num_rows > 0) {
            $coinData = $result->fetch_assoc();
            break;
        }
    }
    
    if (!$coinData) {
        echo json_encode(['success' => false, 'message' => 'Trading pair not found']);
        exit;
    }
    
    $coinId = $coinData['id'];
    
    $priceResult = $spexcon->query("SELECT current_price_usd FROM coin_market_data WHERE coin_id = '$coinId' LIMIT 1");
    $priceData = $priceResult->fetch_assoc();
    $currentPrice = floatval($priceData['current_price_usd'] ?? 118870.00);
    
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
    
    $orderId = 'ORD_' . time() . '_' . rand(1000, 9999);
    
    $spexcon->begin_transaction();
    
    if ($type === 'market') {
        if ($side === 'buy') {
            if ($quoteOrderQty <= 0 || $quoteOrderQty > $userBalance) {
                throw new Exception("Invalid amount or insufficient balance");
            }
            
            $executionPrice = $currentPrice;
            $actualAmount = $quoteOrderQty / $executionPrice;
            
            $spexcon->query("UPDATE users SET balance = balance - $quoteOrderQty WHERE uid = $UserUID");
            
            $spexcon->query("
                INSERT INTO user_transactions (
                    user_id, coin_id, transaction_type, order_type, order_side, 
                    amount, price_per_unit_usd, total_value_usd, order_id, 
                    filled_quantity, status, executed_at
                ) VALUES (
                    $UserUID, '$coinId', 'MARKET_BUY', 'market', 'BUY', 
                    $actualAmount, $executionPrice, $quoteOrderQty, '$orderId', 
                    $actualAmount, 'COMPLETED', NOW()
                )
            ");
            
            $spexcon->query("
                INSERT INTO user_coin_balances (user_id, coin_id, total_balance, available_balance) 
                VALUES ($UserUID, '$coinId', $actualAmount, $actualAmount) 
                ON DUPLICATE KEY UPDATE 
                total_balance = total_balance + $actualAmount, 
                available_balance = available_balance + $actualAmount
            ");
            
            PushSend($UserUID, 'Trade Executed', 
                'Market BUY: ' . number_format($actualAmount, 8) . ' ' . strtoupper($coinData['symbol']) . ' at $' . number_format($executionPrice, 2), 
                'trade');
                
        } elseif ($side === 'sell') {
            if ($amount <= 0) {
                throw new Exception("Invalid amount");
            }
            
            $coinBalanceResult = $spexcon->query("
                SELECT available_balance FROM user_coin_balances 
                WHERE user_id = $UserUID AND coin_id = '$coinId' LIMIT 1
            ");
            $coinBalanceData = $coinBalanceResult->fetch_assoc();
            $availableBalance = floatval($coinBalanceData['available_balance'] ?? 0);
            
            if ($amount > $availableBalance) {
                throw new Exception("Insufficient " . strtoupper($coinData['symbol']) . " balance");
            }
            
            $executionPrice = $currentPrice;
            $totalReceived = $amount * $executionPrice;
            
            $spexcon->query("UPDATE users SET balance = balance + $totalReceived WHERE uid = $UserUID");
            
            $spexcon->query("
                INSERT INTO user_transactions (
                    user_id, coin_id, transaction_type, order_type, order_side, 
                    amount, price_per_unit_usd, total_value_usd, order_id, 
                    filled_quantity, status, executed_at
                ) VALUES (
                    $UserUID, '$coinId', 'MARKET_SELL', 'market', 'SELL', 
                    $amount, $executionPrice, $totalReceived, '$orderId', 
                    $amount, 'COMPLETED', NOW()
                )
            ");
            
            $spexcon->query("
                UPDATE user_coin_balances 
                SET total_balance = total_balance - $amount, available_balance = available_balance - $amount 
                WHERE user_id = $UserUID AND coin_id = '$coinId'
            ");
            
            PushSend($UserUID, 'Trade Executed', 
                'Market SELL: ' . number_format($amount, 8) . ' ' . strtoupper($coinData['symbol']) . ' at $' . number_format($executionPrice, 2), 
                'trade');
        }
        
    } elseif ($type === 'limit') {
        if ($price <= 0 || $amount <= 0) {
            throw new Exception("Invalid price or amount");
        }
        
        $totalValue = $price * $amount;
        
        if ($side === 'buy') {
            if ($totalValue > $userBalance) {
                throw new Exception("Insufficient USDT balance");
            }
            
            $spexcon->query("UPDATE users SET balance = balance - $totalValue WHERE uid = $UserUID");
            $transactionType = 'LIMIT_BUY';
            
        } elseif ($side === 'sell') {
            $coinBalanceResult = $spexcon->query("
                SELECT available_balance FROM user_coin_balances 
                WHERE user_id = $UserUID AND coin_id = '$coinId' LIMIT 1
            ");
            $coinBalanceData = $coinBalanceResult->fetch_assoc();
            $availableBalance = floatval($coinBalanceData['available_balance'] ?? 0);
            
            if ($amount > $availableBalance) {
                throw new Exception("Insufficient " . strtoupper($coinData['symbol']) . " balance");
            }
            
            $spexcon->query("
                UPDATE user_coin_balances 
                SET available_balance = available_balance - $amount 
                WHERE user_id = $UserUID AND coin_id = '$coinId'
            ");
            $transactionType = 'LIMIT_SELL';
        }
        
        $spexcon->query("
            INSERT INTO user_transactions (
                user_id, coin_id, transaction_type, order_type, order_side, 
                amount, price_per_unit_usd, total_value_usd, order_id, 
                remaining_quantity, status, expires_at
            ) VALUES (
                $UserUID, '$coinId', '$transactionType', 'limit', '$side', 
                $amount, $price, $totalValue, '$orderId', 
                $amount, 'PENDING', DATE_ADD(NOW(), INTERVAL 30 DAY)
            )
        ");
        
        PushSend($UserUID, 'Order Placed', 
            'Limit ' . strtoupper($side) . ': ' . number_format($amount, 8) . ' ' . strtoupper($coinData['symbol']) . ' at $' . number_format($price, 2), 
            'trade');
            
    } elseif ($type === 'stop_limit') {
        if ($price <= 0 || $amount <= 0 || $stopPrice <= 0) {
            throw new Exception("Invalid parameters for stop-limit order");
        }
        
        if ($side === 'buy' && $stopPrice <= $currentPrice) {
            throw new Exception("Buy stop price must be above current price");
        }
        
        if ($side === 'sell' && $stopPrice >= $currentPrice) {
            throw new Exception("Sell stop price must be below current price");
        }
        
        $totalValue = $price * $amount;
        
        if ($side === 'buy') {
            if ($totalValue > $userBalance) {
                throw new Exception("Insufficient USDT balance");
            }
            
            // Reserve USDT balance for stop-limit buy
            $spexcon->query("UPDATE users SET balance = balance - $totalValue WHERE uid = $UserUID");
            $transactionType = 'STOP_LIMIT_BUY';
            
        } elseif ($side === 'sell') {
            $coinBalanceResult = $spexcon->query("
                SELECT available_balance FROM user_coin_balances 
                WHERE user_id = $UserUID AND coin_id = '$coinId' LIMIT 1
            ");
            $coinBalanceData = $coinBalanceResult->fetch_assoc();
            $availableBalance = floatval($coinBalanceData['available_balance'] ?? 0);
            
            if ($amount > $availableBalance) {
                throw new Exception("Insufficient " . strtoupper($coinData['symbol']) . " balance");
            }
            
            // Reserve coin balance for stop-limit sell
            $spexcon->query("
                UPDATE user_coin_balances 
                SET available_balance = available_balance - $amount 
                WHERE user_id = $UserUID AND coin_id = '$coinId'
            ");
            $transactionType = 'STOP_LIMIT_SELL';
        }
        
        $spexcon->query("
            INSERT INTO user_transactions (
                user_id, coin_id, transaction_type, order_type, order_side, 
                amount, price_per_unit_usd, trigger_price, total_value_usd, order_id, 
                remaining_quantity, status, expires_at
            ) VALUES (
                $UserUID, '$coinId', '$transactionType', 'stop_limit', '$side', 
                $amount, $price, $stopPrice, $totalValue, '$orderId', 
                $amount, 'PENDING', DATE_ADD(NOW(), INTERVAL 30 DAY)
            )
        ");
        
        PushSend($UserUID, 'Stop-Limit Order Placed', 
            'Stop-Limit ' . strtoupper($side) . ': ' . number_format($amount, 8) . ' ' . strtoupper($coinData['symbol']) . 
            ' | Stop Price: $' . number_format($stopPrice, 2) . ' | Limit Price: $' . number_format($price, 2), 
            'trade');
    }
    
    $spexcon->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Order executed successfully',
        'orderId' => $orderId,
        'executionPrice' => $type === 'market' ? $currentPrice : $price,
        'coin' => strtoupper($coinData['symbol'])
    ]);
    
} catch (Exception $e) {
    if (isset($spexcon)) {
        $spexcon->rollback();
    }
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>