<?php
/*
 * SpeX-FundingFeeCalculator CRON-JOB
 * 
 * Time : Every 8h
 * 
*/
require_once __DIR__ . '/../../system/spex_v4_func.php';

global $spexcon;
try {
    $openPositions = $spexcon->query("
        SELECT * FROM user_futures_positions 
        WHERE status = 'OPEN'
    ");
    
    $totalFees = 0;
    
    while ($position = $openPositions->fetch_assoc()) {
        $marginAmount = floatval($position['margin_amount']);
        $leverage = floatval($position['leverage']);
        $positionValue = $marginAmount * $leverage;
        
        $fundingRate = 0.0001;
        $fundingFee = $positionValue * $fundingRate;
        
        $spexcon->query("
            UPDATE user_futures_positions 
            SET funding_fee = funding_fee + $fundingFee 
            WHERE id = " . $position['id']
        );
        
        $spexcon->query("
            UPDATE users 
            SET balance = balance - $fundingFee 
            WHERE uid = " . $position['user_id']
        );
        
        $spexcon->query("
            INSERT INTO user_logs (uid, type, msg, amount, cat, date) 
            VALUES ('" . $position['user_id'] . "', 'Funding Fee', 
            'Funding fee charged for " . $position['coin_id'] . " position', 
            '$fundingFee', 'fee', NOW())
        ");
        
        $totalFees += $fundingFee;
    }
    
    echo json_encode(['success' => true, 'total_fees' => $totalFees]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>