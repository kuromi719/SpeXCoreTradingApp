<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../system/spex_v4_func.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $userQuery = $spexcon->prepare("SELECT * FROM users WHERE uid = ?");
    $userQuery->bind_param("i", $UserUID);
    $userQuery->execute();
    $user = $userQuery->get_result()->fetch_assoc();
    
    // Check if user_settings table exists and get settings
    $settingsQuery = $spexcon->prepare("
        SELECT * FROM user_settings WHERE user_id = ?
    ");
    $settingsQuery->bind_param("i", $UserUID);
    $settingsQuery->execute();
    $settings = $settingsQuery->get_result()->fetch_assoc();
    
    // Default settings if none exist
    $defaultSettings = [
        'user_id' => $UserUID,
        'notification_alert' => 1,
        'notification_prefs' => 0,
        'price_alert' => 0,
        'shortcuts' => 1,
        'theme_color' => '#facc15',
        'timezone' => '24h',
        'chart_indicator' => 'Green Up / Red Down',
        'nickname' => 'User' . $UserUID
    ];
    
    if (!$settings) {
        // Create default settings
        $insertSettings = $spexcon->prepare("
            INSERT INTO user_settings 
            (user_id, notification_alert, notification_prefs, price_alert, shortcuts, 
             theme_color, timezone, chart_indicator, nickname, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $insertSettings->bind_param("iiiiissss", 
            $UserUID, $defaultSettings['notification_alert'], $defaultSettings['notification_prefs'],
            $defaultSettings['price_alert'], $defaultSettings['shortcuts'], $defaultSettings['theme_color'],
            $defaultSettings['timezone'], $defaultSettings['chart_indicator'], $defaultSettings['nickname']
        );
        $insertSettings->execute();
        
        $settings = $defaultSettings;
    }
    
    $response = array_merge($settings, [
        'email' => $user['mail'],
        'profile_img' => $user['profile_img']
    ]);
    
    echo json_encode([
        'success' => true,
        'data' => $response
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>