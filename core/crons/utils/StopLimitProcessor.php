<?php
/*
 * SpeX Stop-Limit Order Processor
 * Every : 10s
*/
require_once __DIR__ . '/../../system/spex_v4_func.php';
global $spexcon;
class StopLimitProcessor {
    private $connection;
    private $logFile;
    
    public function __construct($spexcon) {
        $this->connection = $spexcon;
        $this->logFile = __DIR__ . '/../logs/stop_limit_orders.log';
        
        if (!file_exists(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }
    }
    
    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($this->logFile, "[$timestamp] $message\n", FILE_APPEND);
    }
    
    public function processPendingOrders() {
        try {
            $pendingOrders = $this->getPendingStopLimitOrders();
            $processedCount = 0;
            
            foreach ($pendingOrders as $order) {
                if ($this->processOrder($order)) {
                    $processedCount++;
                }
            }
            
            if ($processedCount > 0) {
                $this->log("Processed $processedCount stop-limit orders");
            }
            
            return $processedCount;
            
        } catch (Exception $e) {
            $this->log("ERROR: " . $e->getMessage());
            return false;
        }
    }
    
    private function getPendingStopLimitOrders() {
        $query = "
            SELECT ut.*, cmd.current_price_usd, c.symbol as coin_symbol
            FROM user_transactions ut
            LEFT JOIN coin_market_data cmd ON ut.coin_id = cmd.coin_id
            LEFT JOIN coins c ON ut.coin_id = c.id
            WHERE ut.status = 'PENDING' 
            AND ut.order_type = 'stop_limit'
            AND ut.expires_at > NOW()
            ORDER BY ut.created_at ASC
        ";
        
        $result = $this->connection->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    private function processOrder($order) {
        try {
            $currentPrice = floatval($order['current_price_usd'] ?? 0);
            $triggerPrice = floatval($order['trigger_price']);
            $orderSide = strtoupper($order['order_side']);
            $userId = intval($order['user_id']);
            $orderId = $order['order_id'];
            
            if ($currentPrice <= 0) {
                $this->log("SKIP: No price data for {$order['coin_id']} - Order: $orderId");
                return false;
            }
            
            $shouldTrigger = false;
            
            if ($orderSide === 'BUY' && $currentPrice >= $triggerPrice) {
                $shouldTrigger = true;
            } elseif ($orderSide === 'SELL' && $currentPrice <= $triggerPrice) {
                $shouldTrigger = true;
            }
            
            if ($shouldTrigger) {
                return $this->executeStopLimitOrder($order, $currentPrice);
            }
            
            return false;
            
        } catch (Exception $e) {
            $this->log("ERROR processing order {$order['order_id']}: " . $e->getMessage());
            return false;
        }
    }
    
    private function executeStopLimitOrder($order, $currentPrice) {
        try {
            $this->connection->begin_transaction();
            
            $userId = intval($order['user_id']);
            $coinId = $order['coin_id'];
            $orderId = $order['order_id'];
            $orderSide = strtoupper($order['order_side']);
            $amount = floatval($order['amount']);
            $limitPrice = floatval($order['price_per_unit_usd']);
            $totalValue = floatval($order['total_value_usd']);
            
            $newOrderId = 'STOP_EXEC_' . time() . '_' . rand(1000, 9999);
            
            if ($orderSide === 'BUY') {
                $this->connection->query("
                    INSERT INTO user_transactions (
                        user_id, coin_id, transaction_type, order_type, order_side,
                        amount, price_per_unit_usd, total_value_usd, order_id,
                        remaining_quantity, status, expires_at, notes
                    ) VALUES (
                        $userId, '$coinId', 'LIMIT_BUY', 'limit', 'BUY',
                        $amount, $limitPrice, $totalValue, '$newOrderId',
                        $amount, 'PENDING', DATE_ADD(NOW(), INTERVAL 30 DAY), 
                        'Converted from stop-limit: $orderId'
                    )
                ");
                
            } else {
                $this->connection->query("
                    INSERT INTO user_transactions (
                        user_id, coin_id, transaction_type, order_type, order_side,
                        amount, price_per_unit_usd, total_value_usd, order_id,
                        remaining_quantity, status, expires_at, notes
                    ) VALUES (
                        $userId, '$coinId', 'LIMIT_SELL', 'limit', 'SELL',
                        $amount, $limitPrice, $totalValue, '$newOrderId',
                        $amount, 'PENDING', DATE_ADD(NOW(), INTERVAL 30 DAY), 
                        'Converted from stop-limit: $orderId'
                    )
                ");
            }
            
            $this->connection->query("
                UPDATE user_transactions 
                SET status = 'TRIGGERED', notes = 'Stop-limit triggered and converted to limit order: $newOrderId'
                WHERE order_id = '$orderId'
            ");
            
            PushSend($userId, 'Stop Order Triggered', 
                'Your stop-limit order has been triggered and converted to a limit order at $' . number_format($limitPrice, 2), 
                'trade');
            
            $this->connection->commit();
            
            $this->log("TRIGGERED: Order $orderId converted to limit order $newOrderId at price $currentPrice");
            return true;
            
        } catch (Exception $e) {
            $this->connection->rollback();
            $this->log("ERROR executing stop-limit order {$order['order_id']}: " . $e->getMessage());
            return false;
        }
    }
}

$processor = new StopLimitProcessor($spexcon);
$processor->processPendingOrders();
?>