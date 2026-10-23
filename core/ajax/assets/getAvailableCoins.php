<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

if (!getSession('Login')) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

try {
    global $spexcon;
    
    $coinsQuery = "
        SELECT 
            c.id,
            c.symbol,
            c.name,
            cmd.current_price_usd,
            ci.small as logo
        FROM coins c
        LEFT JOIN coin_market_data cmd ON c.id = cmd.coin_id
        LEFT JOIN coin_images ci ON c.id = ci.coin_id
        WHERE cmd.market_cap_rank <= 100
        ORDER BY cmd.market_cap_rank ASC
    ";
    
    $stmt = $spexcon->prepare($coinsQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $coins = [];
    while ($row = $result->fetch_assoc()) {
        $coins[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $coins
    ]);
    
} catch (Exception $e) {
    error_log("Get Available Coins Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error loading coins']);
}
?>