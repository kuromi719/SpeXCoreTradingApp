<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$UserData = FetchUserData($UserUID);
if ($UserData['balance_blocked'] == 1) {
    echo json_encode(['success' => false, 'message' => 'Your Balance is Locked by System']);
    exit;
}

try {
    $query = "SELECT 
                ucb.coin_id,
                c.symbol,
                c.name,
                ucb.total_balance,
                ucb.available_balance,
                ucb.locked_balance,
                cmd.current_price_usd,
                ci.small as logo
              FROM user_coin_balances ucb
              LEFT JOIN coins c ON ucb.coin_id = c.id
              LEFT JOIN coin_market_data cmd ON c.id = cmd.coin_id
              LEFT JOIN coin_images ci ON c.id = ci.coin_id
              WHERE ucb.user_id = ? AND ucb.total_balance > 0
              ORDER BY (ucb.total_balance * cmd.current_price_usd) DESC";
    
    $stmt = $spexcon->prepare($query);
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $coins = [];
    while ($row = $result->fetch_assoc()) {
        $coins[] = [
            'coin_id' => $row['coin_id'],
            'symbol' => $row['symbol'],
            'name' => $row['name'],
            'total_balance' => $row['total_balance'],
            'available_balance' => $row['available_balance'],
            'locked_balance' => $row['locked_balance'],
            'current_price' => $row['current_price_usd'],
            'usd_value' => floatval($row['total_balance']) * floatval($row['current_price_usd']),
            'logo' => $row['logo']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'coins' => $coins
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch coin balances'
    ]);
}
?>