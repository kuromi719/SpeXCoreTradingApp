<?php
/*
 * SpeX-PnLUpdater CRON-JOB
 * 
 * Time : Every 5s
 */

require_once __DIR__ . '/../../system/spex_v4_func.php';
global $spexcon;
class PnLUpdater {
    private $connection;
    private $logFile;
    
    public function __construct($spexcon) {
        $this->connection = $spexcon;
        $this->logFile = __DIR__ . '/../logs/pnl_updater.log';
        
        if (!file_exists(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }
    }
    
    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($this->logFile, "[$timestamp] $message\n", FILE_APPEND);
    }
    
    public function updatePnL() {
        try {
            $openPositions = $this->connection->query("
                SELECT ut.*, cmd.current_price_usd, c.symbol
                FROM user_transactions ut
                LEFT JOIN coin_market_data cmd ON ut.coin_id = cmd.coin_id
                LEFT JOIN coins c ON ut.coin_id = c.id
                WHERE ut.status IN ('PENDING', 'PARTIAL') 
                AND ut.order_type = 'limit'
                AND cmd.current_price_usd IS NOT NULL
            ");
            
            $updatedCount = 0;
            
            if ($openPositions) {
                while ($position = $openPositions->fetch_assoc()) {
                    if ($this->calculateAndUpdatePnL($position)) {
                        $updatedCount++;
                    }
                }
            }
            
            if ($updatedCount > 0) {
                $this->log("Updated P&L for $updatedCount positions");
            }
            
            echo json_encode(['success' => true, 'updated' => $updatedCount]);
            
        } catch (Exception $e) {
            $this->log("ERROR: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    private function calculateAndUpdatePnL($position) {
        try {
            $currentPrice = floatval($position['current_price_usd']);
            $orderPrice = floatval($position['price_per_unit_usd']);
            $amount = floatval($position['filled_quantity']);
            $orderSide = strtoupper($position['order_side']);
            
            if ($amount <= 0) return false;
            
            $pnl = 0;
            $pnlPercentage = 0;
            
            if ($orderSide === 'BUY') {
                $pnl = ($currentPrice - $orderPrice) * $amount;
                $pnlPercentage = (($currentPrice - $orderPrice) / $orderPrice) * 100;
            } else {
                $pnl = ($orderPrice - $currentPrice) * $amount;
                $pnlPercentage = (($orderPrice - $currentPrice) / $orderPrice) * 100;
            }
            
            $this->connection->query("
                UPDATE user_transactions 
                SET unrealized_pnl = $pnl, pnl_percentage = $pnlPercentage
                WHERE id = {$position['id']}
            ");
            
            return true;
            
        } catch (Exception $e) {
            $this->log("ERROR calculating P&L for position {$position['id']}: " . $e->getMessage());
            return false;
        }
    }
}

$updater = new PnLUpdater($spexcon);
$updater->updatePnL();
?>