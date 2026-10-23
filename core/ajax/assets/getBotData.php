<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

try {
    $botsQuery = "
        SELECT 
            id,
            name,
            description,
            logo,
            level,
            hourly_profit_percentage,
            success_rate,
            risk_level,
            bot_type,
            rental_prices,
            total_users,
            total_profit_generated,
            is_active
        FROM trading_bots 
        WHERE is_active = 1
        ORDER BY total_users DESC, total_profit_generated DESC
    ";

    $result = $spexcon->query($botsQuery);
    $bots = [];

    while ($row = $result->fetch_assoc()) {
        $bots[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'logo' => $row['logo'],
            'level' => $row['level'],
            'hourly_profit_percentage' => $row['hourly_profit_percentage'],
            'success_rate' => $row['success_rate'],
            'risk_level' => $row['risk_level'],
            'bot_type' => $row['bot_type'],
            'rental_prices' => $row['rental_prices'],
            'total_users' => $row['total_users'],
            'total_profit_generated' => $row['total_profit_generated']
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $bots
    ]);

} catch (Exception $e) {
    error_log("Bot Data Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error loading bot data'
    ]);
}
?>