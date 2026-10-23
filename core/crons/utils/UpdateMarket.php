<?php
/*
 * SpeX-Market-Update CRON-JOB 
 * 
 * Time : Every 1m
*/
require_once __DIR__ . '/../../system/spex_v4_func.php';
global $spexcon;
require_once __DIR__ . '/../../utils/CryptoDataManager.php';

function MarketlogMessage($message) {
    $timestamp = date('Y-m-d H:i:s');
    echo "[$timestamp] $message\n";
    
    $logFile = __DIR__ . '/../logs/market_update.log';
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX);
}

try {
    MarketlogMessage("=== Market Update Cronjob Started ===");
    
    if (!$spexcon) {
        throw new Exception("Database connection not available");
    }
    
    $cryptoManager = new CryptoDataManager($spexcon);
    
    // First Setup: (only execute one time)
    // $cryptoManager->initializeCoins();
    
    MarketlogMessage("Updating coin prices...");
    $cryptoManager->updateCoinPrices();
    
    MarketlogMessage("Updating coin details...");
    $cryptoManager->updateCoinDetails();
    
    $finalCoinCountQuery = $spexcon->query("SELECT COUNT(*) as count FROM coins");
    $finalCoinCount = $finalCoinCountQuery->fetch_assoc()['count'];
    
    $updateTime = date('Y-m-d H:i:s');
    
    $stmt = $spexcon->prepare("INSERT INTO system_info (key_name, value) VALUES (?, ?) 
                               ON DUPLICATE KEY UPDATE value = VALUES(value)");
    
    $stmt->bind_param("ss", $key, $updateTime);
    $key = 'last_market_update';
    $stmt->execute();
    
    $stmt->bind_param("ss", $key, $finalCoinCount);
    $key = 'total_coins_count';
    $stmt->execute();
    
    
    MarketlogMessage("Total coins in database: $finalCoinCount");
    MarketlogMessage("=== Update Completed Successfully ===");
    
} catch (Exception $e) {
    MarketlogMessage("FATAL ERROR: " . $e->getMessage());
    MarketlogMessage("Stack trace: " . $e->getTraceAsString());
    exit(1);
}

MarketlogMessage("Script Executed Successfully!");
?>