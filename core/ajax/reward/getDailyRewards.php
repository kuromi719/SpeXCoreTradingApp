<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';

header('Content-Type: application/json');

try {
    $UID = getSession('UID');
    
    if (!$UID) {
        throw new Exception('User not logged in');
    }
    
    global $spexcon;
    
    $settingsQuery = $spexcon->query("SELECT * FROM settings WHERE id = 1");
    $settings = $settingsQuery->fetch_assoc();
    
    $dailyPageOn = $settings['DailyPageOn'] ?? 0;
    $dailyBonus = $settings['DailyBonus'] ?? 0;
    $payBonus = $settings['PayBonus'] ?? 0;
    $refBonus = $settings['RefBonus'] ?? 0;
    
    $canClaimDaily = false;
    if ($dailyBonus > 0) {
        $lastClaimQuery = $spexcon->prepare("SELECT date FROM user_logs WHERE uid = ? AND type = 'daily_bonus' AND DATE(date) = CURDATE() ORDER BY date DESC LIMIT 1");
        $lastClaimQuery->bind_param("i", $UID);
        $lastClaimQuery->execute();
        $lastClaim = $lastClaimQuery->get_result()->fetch_assoc();
        
        $canClaimDaily = !$lastClaim;
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'daily_page_on' => (int)$dailyPageOn,
            'daily_bonus' => (float)$dailyBonus,
            'pay_bonus' => (float)$payBonus,
            'ref_bonus' => (float)$refBonus,
            'can_claim_daily' => $canClaimDaily
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>