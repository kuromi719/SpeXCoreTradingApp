<?php
/*
 * SpeX-StopLossTakeProfit CRON-JOB
 * 
 * Time : Every 5s
 */

require_once __DIR__ . '/../../system/spex_v4_func.php';
global $spexcon;
class StopLossTakeProfit {
    private $connection;
    private $logFile;
    
    public function __construct($spexcon) {
        $this->connection = $spexcon;
        $this->logFile = __DIR__ . '/../logs/stop_loss_take_profit.log';
        
        if (!file_exists(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }
    }
    
    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($this->logFile, "[$timestamp] $message\n", FILE_APPEND);
    }
    
    public function processStopOrders() {
        try {
            $stopOrders = $this->connection->query("
                SELECT ut.*, cmd.current_price_usd, c.symbol
                FROM user_transactions ut
                LEFT JOIN coin_market_data cmd ON ut.coin_id = cmd.coin_id
                LEFT JOIN coins c ON ut.coin_id = c.id
                WHERE ut.status = 'PENDING' 
                AND ut.transaction_type LIKE '%STOP%'
                AND cmd.current_price_usd IS NOT NULL
                ORDER BY ut.created_at ASC
            ");
            
            $processedCount = 0;
            
            if ($stopOrders) {
                while ($order = $stopOrders->fetch_assoc()) {
                    if ($this->shouldTriggerStop($order)) {
                        if ($this->executeStopOrder($order)) {
                            $processedCount++;
                        }
                    }
                }
            }
            
            if ($processedCount > 0) {
                $this->log("Processed $processedCount stop orders");
            }
            
            echo json_encode(['success' => true, 'processed' => $processedCount]);
            
        } catch (Exception $e) {
            $this->log("ERROR: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    private function shouldTriggerStop($order) {
        $currentPrice = floatval($order['current_price_usd']);
        $triggerPrice = floatval($order['trigger_price'] ?? $order['price_per_unit_usd']);
        $orderSide = strtoupper($order['order_side']);
        
        if ($orderSide === 'BUY') {
            return $currentPrice >= $triggerPrice;
        } else {
            return $currentPrice <= $triggerPrice;
        }
    }
    
    private function executeStopOrder($order) {
        try {
            $this->connection->begin_transaction();
            
            $userId = intval($order['user_id']);
            $coinId = $order['coin_id'];
            $orderId = $order['order_id'];
            $amount = floatval($order['remaining_quantity']);
            $currentPrice = floatval($order['current_price_usd']);
            $orderSide = strtoupper($order['order_side']);
            
            $newOrderId = 'STOP_MARKET_' . time() . '_' . rand(1000, 9999);
            
            if ($orderSide === 'BUY') {
                $totalCost = $amount * $currentPrice;
                
                $this->connection->query("UPDATE users SET balance = balance - $totalCost WHERE uid = $userId");
                
                $this->connection->query("
                    INSERT INTO user_coin_balances (user_id, coin_id, total_balance, available_balance) 
                    VALUES ($userId, '$coinId', $amount, $amount) 
                    ON DUPLICATE KEY UPDATE 
                    total_balance = total_balance + $amount, 
                    available_balance = available_balance + $amount
                ");
                
            } else {
                $totalReceived = $amount * $currentPrice;
                
                $this->connection->query("UPDATE users SET balance = balance + $totalReceived WHERE uid = $userId");
                
                $this->connection->query("
                    UPDATE user_coin_balances 
                    SET total_balance = total_balance - $amount, available_balance = available_balance - $amount 
                    WHERE user_id = $userId AND coin_id = '$coinId'
                ");
            }
            
            $this->connection->query("
                UPDATE user_transactions 
                SET status = 'COMPLETED', filled_quantity = remaining_quantity, remaining_quantity = 0,
                    executed_at = NOW(), notes = 'Stop order triggered and executed as market order'
                WHERE order_id = '$orderId'
            ");
            
            PushSend($userId, 'Stop Order Executed', 
                'Your stop order has been triggered and executed at $' . number_format($currentPrice, 2), 'trade');
            
            $this->connection->commit();
            return true;
            
        } catch (Exception $e) {
            $this->connection->rollback();
            $this->log("ERROR executing stop order {$order['order_id']}: " . $e->getMessage());
            return false;
        }
    }
}

$processor = new StopLossTakeProfit($spexcon);
$processor->processStopOrders();
?>