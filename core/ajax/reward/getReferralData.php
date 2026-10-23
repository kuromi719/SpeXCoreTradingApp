<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';

header('Content-Type: application/json');

try {
    $UID = getSession('UID');
    
    if (!$UID) {
        throw new Exception('User not logged in');
    }
    
    global $spexcon;
    
    $userData = FetchUserData($UID);
    $referralCode = $userData['invitation_code'];
    
    $countQuery = $spexcon->prepare("SELECT COUNT(*) as count FROM invitations WHERE user_uid = ?");
    $countQuery->bind_param("i", $UID);
    $countQuery->execute();
    $countResult = $countQuery->get_result()->fetch_assoc();
    $referralCount = $countResult['count'];
    
    $earningsQuery = $spexcon->prepare("SELECT SUM(amount) as total FROM user_logs WHERE uid = ? AND type = 'referral_bonus'");
    $earningsQuery->bind_param("i", $UID);
    $earningsQuery->execute();
    $earningsResult = $earningsQuery->get_result()->fetch_assoc();
    $totalEarnings = $earningsResult['total'] ?? 0;
    
    $settingsQuery = $spexcon->query("SELECT ref_lvl_percentage1, ref_lvl_percentage2, ref_lvl_percentage3, ref_lvl_percentage4, ref_lvl_percentage5 FROM settings WHERE id = 1");
    $settings = $settingsQuery->fetch_assoc();
    
    $commissionRates = [
        0,
        $settings['ref_lvl_percentage1'], $settings['ref_lvl_percentage1'],
        $settings['ref_lvl_percentage2'], $settings['ref_lvl_percentage2'], $settings['ref_lvl_percentage2'],
        $settings['ref_lvl_percentage3'], $settings['ref_lvl_percentage3'], $settings['ref_lvl_percentage3'], $settings['ref_lvl_percentage3'],
        $settings['ref_lvl_percentage4'], $settings['ref_lvl_percentage4'], $settings['ref_lvl_percentage4'],
        $settings['ref_lvl_percentage5']
    ];
    
    $level = min($referralCount, 13);
    $commissionRate = $commissionRates[$level] ?? $settings['ref_lvl_percentage5'];
    
    if ($referralCount >= 13) {
        $commissionRate = $settings['ref_lvl_percentage5'] + 5;
    }
    
    $baseUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    $referralLink = $baseUrl . '?r=register&c=' . $referralCode;
    
    echo json_encode([
        'success' => true,
        'data' => [
            'referral_count' => $referralCount,
            'total_earnings' => $totalEarnings,
            'referral_link' => $referralLink,
            'referral_code' => $referralCode,
            'commission_rate' => $commissionRate,
            'level' => $level
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>