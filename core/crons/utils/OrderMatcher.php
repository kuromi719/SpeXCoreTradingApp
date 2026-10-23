<?php
/*
 * SpeX-OrderMatcher CRON-JOB
 * 
 * Time : Every 10s
 */

require_once __DIR__ . '/../../system/spex_v4_func.php';
global $spexcon;
class OrderMatcher {
    private $connection;
    private $logFile;
    
    public function __construct($spexcon) {
        $this->connection = $spexcon;
        $this->logFile = __DIR__ . '/../logs/order_matcher.log';
        
        if (!file_exists(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }
    }
    
    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($this->logFile, "[$timestamp] $message\n", FILE_APPEND);
    }
    
    public function matchOrders() {
        try {
            $buyOrders = $this->connection->query("
                SELECT ut.*, cmd.current_price_usd, c.symbol
                FROM user_transactions ut
                LEFT JOIN coin_market_data cmd ON ut.coin_id = cmd.coin_id
                LEFT JOIN coins c ON ut.coin_id = c.id
                WHERE ut.status = 'PENDING' 
                AND ut.order_type = 'limit' 
                AND ut.order_side = 'BUY'
                ORDER BY ut.price_per_unit_usd DESC, ut.created_at ASC
            ");
            
            $sellOrders = $this->connection->query("
                SELECT ut.*, cmd.current_price_usd, c.symbol
                FROM user_transactions ut
                LEFT JOIN coin_market_data cmd ON ut.coin_id = cmd.coin_id
                LEFT JOIN coins c ON ut.coin_id = c.id
                WHERE ut.status = 'PENDING' 
                AND ut.order_type = 'limit' 
                AND ut.order_side = 'SELL'
                ORDER BY ut.price_per_unit_usd ASC, ut.created_at ASC
            ");
            
            $matchedCount = 0;
            
            if ($buyOrders && $sellOrders) {
                while ($buyOrder = $buyOrders->fetch_assoc()) {
                    $sellOrders->data_seek(0);
                    while ($sellOrder = $sellOrders->fetch_assoc()) {
                        if ($this->canMatch($buyOrder, $sellOrder)) {
                            if ($this->executeMatch($buyOrder, $sellOrder)) {
                                $matchedCount++;
                                break;
                            }
                        }
                    }
                }
            }
            
            if ($matchedCount > 0) {
                $this->log("Matched $matchedCount orders");
            }
            
            echo json_encode(['success' => true, 'matched' => $matchedCount]);
            
        } catch (Exception $e) {
            $this->log("ERROR: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    private function canMatch($buyOrder, $sellOrder) {
        return $buyOrder['coin_id'] === $sellOrder['coin_id'] &&
               $buyOrder['user_id'] !== $sellOrder['user_id'] &&
               floatval($buyOrder['price_per_unit_usd']) >= floatval($sellOrder['price_per_unit_usd']);
    }
    
    private function executeMatch($buyOrder, $sellOrder) {
        try {
            $this->connection->begin_transaction();
            
            $matchPrice = floatval($sellOrder['price_per_unit_usd']);
            $matchQuantity = min(
                floatval($buyOrder['remaining_quantity']),
                floatval($sellOrder['remaining_quantity'])
            );
            $matchValue = $matchPrice * $matchQuantity;
            
            $this->connection->query("
                UPDATE user_transactions 
                SET filled_quantity = filled_quantity + $matchQuantity,
                    remaining_quantity = remaining_quantity - $matchQuantity,
                    status = CASE WHEN remaining_quantity - $matchQuantity <= 0 THEN 'COMPLETED' ELSE 'PARTIAL' END
                WHERE order_id = '{$buyOrder['order_id']}'
            ");
            
            $this->connection->query("
                UPDATE user_transactions 
                SET filled_quantity = filled_quantity + $matchQuantity,
                    remaining_quantity = remaining_quantity - $matchQuantity,
                    status = CASE WHEN remaining_quantity - $matchQuantity <= 0 THEN 'COMPLETED' ELSE 'PARTIAL' END
                WHERE order_id = '{$sellOrder['order_id']}'
            ");
            
            $this->connection->query("
                UPDATE users SET balance = balance + $matchValue 
                WHERE uid = {$sellOrder['user_id']}
            ");
            
            $this->connection->query("
                INSERT INTO user_coin_balances (user_id, coin_id, total_balance, available_balance) 
                VALUES ({$buyOrder['user_id']}, '{$buyOrder['coin_id']}', $matchQuantity, $matchQuantity) 
                ON DUPLICATE KEY UPDATE 
                total_balance = total_balance + $matchQuantity, 
                available_balance = available_balance + $matchQuantity
            ");
            
            PushSend($buyOrder['user_id'], 'Order Filled', 
                'Your buy order has been filled at $' . number_format($matchPrice, 2), 'trade');
            PushSend($sellOrder['user_id'], 'Order Filled', 
                'Your sell order has been filled at $' . number_format($matchPrice, 2), 'trade');
            
            $this->connection->commit();
            return true;
            
        } catch (Exception $e) {
            $this->connection->rollback();
            $this->log("ERROR matching orders: " . $e->getMessage());
            return false;
        }
    }
}

$matcher = new OrderMatcher($spexcon);
$matcher->matchOrders();
?>