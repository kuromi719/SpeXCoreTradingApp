<?php
/*
 * SpeX-NotificationCleanUp CRON-JOB
 * 
 * Time : Every 1h
 */

require_once __DIR__ . '/../../system/spex_v4_func.php';
global $spexcon;
class NotificationCleanUp {
    private $connection;
    private $logFile;
    
    public function __construct($spexcon) {
        $this->connection = $spexcon;
        $this->logFile = __DIR__ . '/../logs/notification_cleanup.log';
        
        if (!file_exists(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }
    }
    
    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($this->logFile, "[$timestamp] $message\n", FILE_APPEND);
    }
    
    public function cleanOldNotifications() {
        try {
            $oldTransactions = $this->connection->query("
                DELETE FROM user_transactions 
                WHERE status IN ('COMPLETED', 'CANCELLED', 'EXPIRED', 'LIQUIDATED') 
                AND created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)
            ");
            
            $deletedTransactions = $this->connection->affected_rows;

            $userNotify = $this->connection->query("
                DELETE FROM user_pushs 
                WHERE date < DATE_SUB(NOW(), INTERVAL 30 DAY)
            ");
            
            $deletedLogs = $this->connection->affected_rows;
            
            $emptyBalances = $this->connection->query("
                DELETE FROM user_coin_balances 
                WHERE total_balance <= 0 AND available_balance <= 0 AND locked_balance <= 0
            ");
            
            $deletedBalances = $this->connection->affected_rows;
            
            $totalCleaned = $deletedTransactions + $deletedLogs + $deletedBalances;
            
            if ($totalCleaned > 0) {
                $this->log("Cleaned $totalCleaned old records ($deletedTransactions transactions, $deletedLogs logs, $deletedBalances balances)");
            }
            
            echo json_encode([
                'success' => true, 
                'cleaned' => $totalCleaned,
                'transactions' => $deletedTransactions,
                'userNotifications' => $deletedLogs,
                'balances' => $deletedBalances
            ]);
            
        } catch (Exception $e) {
            $this->log("ERROR: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}

$cleaner = new NotificationCleanUp($spexcon);
$cleaner->cleanOldNotifications();
?>