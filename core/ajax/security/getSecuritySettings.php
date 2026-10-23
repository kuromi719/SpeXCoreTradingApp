<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $settingsQuery = $spexcon->prepare("SELECT * FROM user_security_settings WHERE user_id = ?");
    $settingsQuery->bind_param("i", $UserUID);
    $settingsQuery->execute();
    $settings = $settingsQuery->get_result()->fetch_assoc();
    
    if (!$settings) {
        $insertQuery = $spexcon->prepare("
            INSERT INTO user_security_settings 
            (user_id, no_data_sharing, marketing_emails, analytics_cookies, login_notifications, two_factor_enabled) 
            VALUES (?, 1, 0, 0, 1, 0)
        ");
        $insertQuery->bind_param("i", $UserUID);
        $insertQuery->execute();
        
        $settings = [
            'user_id' => $UserUID,
            'no_data_sharing' => 1,
            'marketing_emails' => 0,
            'analytics_cookies' => 0,
            'login_notifications' => 1,
            'data_retention' => 90,
            'two_factor_enabled' => 0
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $settings
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>