<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $historyQuery = $spexcon->prepare("
        SELECT ip_address, device, browser, location, status, login_time 
        FROM user_login_history 
        WHERE user_id = ? 
        ORDER BY login_time DESC 
        LIMIT 20
    ");
    $historyQuery->bind_param("i", $UserUID);
    $historyQuery->execute();
    $history = $historyQuery->get_result()->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $history
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>