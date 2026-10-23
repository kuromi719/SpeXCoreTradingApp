<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../system/spex_v4_func.php';

if (!getSession('Login')) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$UserUID = getSession('UID');

try {
    $userBotsQuery = "
        SELECT 
            ubs.id,
            ubs.payment_amount,
            ubs.rental_duration_hours,
            ubs.hourly_profit_rate,
            ubs.total_earned,
            ubs.subscription_start,
            ubs.subscription_end,
            ubs.status,
            tb.name as bot_name,
            tb.logo as bot_logo,
            tb.level as bot_level
        FROM user_bot_subscriptions ubs
        LEFT JOIN trading_bots tb ON ubs.bot_id = tb.id
        WHERE ubs.user_id = ? AND ubs.status = 'ACTIVE'
        ORDER BY ubs.subscription_start DESC
    ";

    $stmt = $spexcon->prepare($userBotsQuery);
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    $result = $stmt->get_result();

    $userBots = [];
    while ($row = $result->fetch_assoc()) {
        $userBots[] = [
            'id' => $row['id'],
            'bot_name' => $row['bot_name'],
            'bot_logo' => $row['bot_logo'],
            'bot_level' => $row['bot_level'],
            'payment_amount' => $row['payment_amount'],
            'rental_duration_hours' => $row['rental_duration_hours'],
            'hourly_profit_rate' => $row['hourly_profit_rate'],
            'total_earned' => $row['total_earned'],
            'subscription_start' => $row['subscription_start'],
            'subscription_end' => $row['subscription_end'],
            'status' => $row['status']
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $userBots
    ]);

} catch (Exception $e) {
    error_log("User Bots Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error loading user bots'
    ]);
}
?>