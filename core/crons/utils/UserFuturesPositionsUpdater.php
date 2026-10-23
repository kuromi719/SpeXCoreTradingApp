<?php
/*
 * SpeX-UserFuturesPositionsUpdater CRON-JOB
 * 
 * Time : Every 30s
 * Updates user futures positions with current market prices and PnL calculations
*/
require_once __DIR__ . '/../../system/spex_v4_func.php';
global $spexcon;
class UserFuturesPositionsUpdater {
    private $connection;
    private $logFile;
    
    public function __construct($spexcon) {
        $this->connection = $spexcon;
        $this->logFile = __DIR__ . '/../logs/futures_positions_updater.log';
        
        if (!file_exists(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }
    }
    
    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($this->logFile, "[$timestamp] $message\n", FILE_APPEND);
    }
    
    public function updatePositions() {
        try {
            $openPositions = $this->connection->query("
                SELECT 
                    ufp.*,
                    cmd.current_price_usd,
                    c.symbol
                FROM user_futures_positions ufp
                LEFT JOIN coin_market_data cmd ON ufp.coin_id = cmd.coin_id
                LEFT JOIN coins c ON ufp.coin_id = c.id
                WHERE ufp.status = 'OPEN'
                AND cmd.current_price_usd IS NOT NULL
            ");
            
            $updatedCount = 0;
            $liquidatedCount = 0;
            
            if ($openPositions) {
                while ($position = $openPositions->fetch_assoc()) {
                    $updateResult = $this->updateSinglePosition($position);
                    
                    if ($updateResult['updated']) {
                        $updatedCount++;
                    }
                    
                    if ($updateResult['liquidated']) {
                        $liquidatedCount++;
                    }
                }
            }
            
            if ($updatedCount > 0 || $liquidatedCount > 0) {
                $this->log("Updated: $updatedCount positions, Liquidated: $liquidatedCount positions");
            }
            
            echo json_encode([
                'success' => true, 
                'updated' => $updatedCount,
                'liquidated' => $liquidatedCount
            ]);
            
        } catch (Exception $e) {
            $this->log("ERROR: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    private function updateSinglePosition($position) {
        $result = ['updated' => false, 'liquidated' => false];
        
        try {
            $positionId = $position['id'];
            $userId = $position['user_id'];
            $coinId = $position['coin_id'];
            $positionType = $position['position_type'];
            $positionSize = floatval($position['position_size']);
            $entryPrice = floatval($position['entry_price']);
            $currentPrice = floatval($position['current_price_usd']);
            $leverage = floatval($position['leverage']);
            $marginAmount = floatval($position['margin_amount']);
            $liquidationPrice = floatval($position['liquidation_price']);
            
            $this->connection->query("
                UPDATE user_futures_positions 
                SET current_price = $currentPrice 
                WHERE id = $positionId
            ");
            
            $unrealizedPnl = $this->calculateUnrealizedPnL(
                $positionType, 
                $positionSize, 
                $entryPrice, 
                $currentPrice
            );
            
            $this->connection->query("
                UPDATE user_futures_positions 
                SET unrealized_pnl = $unrealizedPnl 
                WHERE id = $positionId
            ");
            
            $result['updated'] = true;
            
            if ($this->shouldLiquidatePosition($position, $currentPrice)) {
                $this->liquidatePosition($position);
                $result['liquidated'] = true;
                $this->log("Position liquidated: User $userId, Coin: $coinId, Size: $positionSize");
            }
            
        } catch (Exception $e) {
            $this->log("ERROR updating position {$position['id']}: " . $e->getMessage());
        }
        
        return $result;
    }
    
    private function calculateUnrealizedPnL($positionType, $positionSize, $entryPrice, $currentPrice) {
        if ($positionType === 'LONG') {
            return ($currentPrice - $entryPrice) * $positionSize;
        } else {
            return ($entryPrice - $currentPrice) * $positionSize;
        }
    }
    
    private function shouldLiquidatePosition($position, $currentPrice) {
        $liquidationPrice = floatval($position['liquidation_price']);
        $positionType = $position['position_type'];
        
        if ($liquidationPrice <= 0) {
            return false;
        }
        
        if ($positionType === 'LONG') {
            return $currentPrice <= $liquidationPrice;
        } else {
            return $currentPrice >= $liquidationPrice;
        }
    }
    
    private function liquidatePosition($position) {
        try {
            $this->connection->begin_transaction();
            
            $positionId = $position['id'];
            $userId = $position['user_id'];
            $coinId = $position['coin_id'];
            $positionSize = floatval($position['position_size']);
            $marginAmount = floatval($position['margin_amount']);
            $currentPrice = floatval($position['current_price_usd']);
            
            $this->connection->query("
                UPDATE user_futures_positions 
                SET 
                    status = 'LIQUIDATED',
                    closed_at = NOW(),
                    notes = 'Position liquidated due to reaching liquidation price'
                WHERE id = $positionId
            ");
            
            $this->connection->query("
                INSERT INTO user_logs (uid, type, msg, amount, cat, date) 
                VALUES (
                    '$userId', 
                    'Position Liquidated', 
                    'Your {$position['coin_id']} {$position['position_type']} position has been liquidated', 
                    '$marginAmount', 
                    'liquidation', 
                    NOW()
                )
            ");
            
            $this->connection->commit();
            
        } catch (Exception $e) {
            $this->connection->rollback();
            $this->log("ERROR liquidating position {$position['id']}: " . $e->getMessage());
            throw $e;
        }
    }
}


$updater = new UserFuturesPositionsUpdater($spexcon);
$updater->updatePositions();
?>