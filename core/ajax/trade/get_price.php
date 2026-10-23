<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

$symbol = $_GET['symbol'] ?? '';
if (empty($symbol)) {
    echo json_encode(['success' => false, 'message' => 'Symbol required']);
    exit;
}

try {
    $stmt = $spexcon->prepare("
        SELECT c.id, c.symbol, cmd.current_price_usd 
        FROM coins c 
        LEFT JOIN coin_market_data cmd ON c.id = cmd.coin_id 
        WHERE c.id = ?
    ");
    $stmt->bind_param("s", $symbol);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $price = floatval($row['current_price_usd'] ?? 118110.00);
        if ($price <= 0) $price = 118110.00;
        
        echo json_encode([
            'success' => true,
            'price' => $price,
            'symbol' => $row['id'],
            'trading_symbol' => strtoupper($row['symbol'])
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Symbol not found'
        ]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>