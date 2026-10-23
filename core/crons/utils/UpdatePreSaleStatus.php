<?php
/*
 * SpeX-PreSaleStatusUpdater CRON-JOB
 * 
 * Time : Every 30m
 * 
*/
require_once __DIR__ . '/../../system/spex_v4_func.php';
global $spexcon;
try {
    $spexcon->query("
        UPDATE presales 
        SET status = 'active' 
        WHERE status = 'upcoming' AND start_date <= NOW()
    ");
    
    $spexcon->query("
        UPDATE presales 
        SET status = 'ended' 
        WHERE status = 'active' AND end_date <= NOW()
    ");
    
    $spexcon->query("
        UPDATE presales 
        SET claim_enabled = 1 
        WHERE status = 'ended' AND claim_enabled = 0 AND end_date <= DATE_SUB(NOW(), INTERVAL 1 HOUR)
    ");
    
    $result = $spexcon->query("
        SELECT id FROM presales 
        WHERE status = 'ended' AND trading_enabled = 0 AND claim_enabled = 1
    ");
    
    while ($row = $result->fetch_assoc()) {
        $spexcon->query("UPDATE presales SET trading_enabled = 1 WHERE id = " . $row['id']);
    }
    
    echo json_encode(['success' => true, 'message' => 'Presale statuses updated']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>