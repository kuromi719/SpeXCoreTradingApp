<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../system/spex_v4_func.php';

if (!getSession('Login')) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$UserUID = getSession('UID');

try {
    $statsQuery = "
        SELECT 
            SUM(payment_amount) as total_operating,
            SUM(CASE WHEN DATE(last_earning_time) = CURDATE() THEN total_earned ELSE 0 END) as today_profit,
            SUM(total_earned) as total_profit
        FROM user_bot_subscriptions 
        WHERE user_id = ? AND status = 'ACTIVE'
    ";

    $stmt = $spexcon->prepare($statsQuery);
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    $statsResult = $stmt->get_result()->fetch_assoc();

    echo json_encode([
        'success' => true,
        'data' => [
            'total_operating' => $statsResult['total_operating'] ?? 0,
            'today_profit' => $statsResult['today_profit'] ?? 0,
            'total_profit' => $statsResult['total_profit'] ?? 0
        ]
    ]);

} catch (Exception $e) {
    error_log("Bot Stats Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error loading bot statistics'
    ]);
}
?>