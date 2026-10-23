<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';

header('Content-Type: application/json');

try {
    $UID = getSession('UID');
    $UserUID = $UID;
    $UserData = FetchUserData($UserUID);
    if ($UserData['balance_blocked'] == 1) {
        echo json_encode(['success' => false, 'message' => 'Your Balance is Locked by System']);
        exit;
    }
    
    if (!$UID) {
        throw new Exception('User not logged in');
    }
    
    global $spexcon;
    
    $settingsQuery = $spexcon->query("SELECT DailyBonus FROM settings WHERE id = 1");
    $settings = $settingsQuery->fetch_assoc();
    $dailyBonus = $settings['DailyBonus'] ?? 0;
    
    if ($dailyBonus <= 0) {
        throw new Exception('Daily bonus not available');
    }
    
    $lastClaimQuery = $spexcon->prepare("SELECT id FROM user_logs WHERE uid = ? AND type = 'daily_bonus' AND DATE(date) = CURDATE()");
    $lastClaimQuery->bind_param("i", $UID);
    $lastClaimQuery->execute();
    $lastClaim = $lastClaimQuery->get_result()->fetch_assoc();
    
    if ($lastClaim) {
        throw new Exception('Daily bonus already claimed today');
    }
    
    $spexcon->autocommit(false);
    
    $updateBalanceQuery = $spexcon->prepare("UPDATE users SET balance = balance + ? WHERE uid = ?");
    $updateBalanceQuery->bind_param("di", $dailyBonus, $UID);
    
    if (!$updateBalanceQuery->execute()) {
        throw new Exception('Failed to update balance');
    }
    
    $logQuery = $spexcon->prepare("INSERT INTO user_logs (uid, type, msg, amount, cat, date) VALUES (?, 'daily_bonus', 'Daily bonus claimed', ?, 'reward', NOW())");
    $logQuery->bind_param("id", $UID, $dailyBonus);
    
    if (!$logQuery->execute()) {
        throw new Exception('Failed to log transaction');
    }
    
    $spexcon->commit();
    $spexcon->autocommit(true);
    
    echo json_encode([
        'success' => true,
        'message' => 'Daily bonus claimed successfully',
        'amount' => $dailyBonus
    ]);
    
} catch (Exception $e) {
    $spexcon->rollback();
    $spexcon->autocommit(true);
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>