<?php
/*
 * SpeX-LiquidationEngine CRON-JOB
 * 
 * Time : Every 30s
 */

require_once __DIR__ . '/../../system/spex_v4_func.php';
global $spexcon;
class LiquidationEngine {
    private $connection;
    private $logFile;
    
    public function __construct($spexcon) {
        $this->connection = $spexcon;
        $this->logFile = __DIR__ . '/../logs/liquidation.log';
        
        if (!file_exists(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }
    }
    
    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($this->logFile, "[$timestamp] $message\n", FILE_APPEND);
    }
    
    public function processLiquidations() {
        try {
            $positionsAtRisk = $this->connection->query("
                SELECT ut.*, cmd.current_price_usd, c.symbol, u.balance
                FROM user_transactions ut
                LEFT JOIN coin_market_data cmd ON ut.coin_id = cmd.coin_id
                LEFT JOIN coins c ON ut.coin_id = c.id
                LEFT JOIN users u ON ut.user_id = u.uid
                WHERE ut.status = 'PENDING' 
                AND ut.order_type = 'limit'
                AND cmd.current_price_usd IS NOT NULL
            ");
            
            $liquidatedCount = 0;
            
            if ($positionsAtRisk) {
                while ($position = $positionsAtRisk->fetch_assoc()) {
                    if ($this->shouldLiquidate($position)) {
                        if ($this->liquidatePosition($position)) {
                            $liquidatedCount++;
                        }
                    }
                }
            }
            
            if ($liquidatedCount > 0) {
                $this->log("Liquidated $liquidatedCount positions");
            }
            
            echo json_encode(['success' => true, 'liquidated' => $liquidatedCount]);
            
        } catch (Exception $e) {
            $this->log("ERROR: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    private function shouldLiquidate($position) {
        $currentPrice = floatval($position['current_price_usd']);
        $orderPrice = floatval($position['price_per_unit_usd']);
        $userBalance = floatval($position['balance']);
        $orderSide = strtoupper($position['order_side']);
        
        if ($orderSide === 'BUY') {
            $priceChange = ($currentPrice - $orderPrice) / $orderPrice;
            return $priceChange < -0.5 && $userBalance < 100;
        } else {
            $priceChange = ($orderPrice - $currentPrice) / $orderPrice;
            return $priceChange < -0.5;
        }
    }
    
    private function liquidatePosition($position) {
        try {
            $this->connection->begin_transaction();
            
            $userId = intval($position['user_id']);
            $orderId = $position['order_id'];
            $coinId = $position['coin_id'];
            $amount = floatval($position['remaining_quantity']);
            $currentPrice = floatval($position['current_price_usd']);
            
            $this->connection->query("
                UPDATE user_transactions 
                SET status = 'LIQUIDATED', 
                    notes = 'Position liquidated due to adverse price movement'
                WHERE order_id = '$orderId'
            ");
            
            if (strtoupper($position['order_side']) === 'BUY') {
                $refundAmount = $amount * floatval($position['price_per_unit_usd']);
                $this->connection->query("UPDATE users SET balance = balance + $refundAmount WHERE uid = $userId");
            } else {
                $this->connection->query("
                    UPDATE user_coin_balances 
                    SET available_balance = available_balance + $amount 
                    WHERE user_id = $userId AND coin_id = '$coinId'
                ");
            }
            
            PushSend($userId, 'Position Liquidated', 
                'Your position has been liquidated due to adverse market conditions', 'trade');
            
            $this->connection->commit();
            return true;
            
        } catch (Exception $e) {
            $this->connection->rollback();
            $this->log("ERROR liquidating position {$position['order_id']}: " . $e->getMessage());
            return false;
        }
    }
}

$engine = new LiquidationEngine($spexcon);
$engine->processLiquidations();
?>