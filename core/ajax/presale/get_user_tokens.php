<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $query = "
        SELECT ut.*, 
               p.price, 
               p.trading_enabled,
               COALESCE(p.trading_enabled, 0) as trading_enabled,
               p.volume_24h,
               p.market_cap
        FROM user_tokens ut
        LEFT JOIN presales p ON ut.coin_symbol = p.coin_symbol
        WHERE ut.user_id = ? AND ut.balance > 0
        ORDER BY ut.coin_symbol ASC
    ";
    
    $stmt = $spexcon->prepare($query);
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $tokens = [];
    $stats = [
        'wallet_balance' => 0,
        'total_token_value' => 0,
        'tradeable_tokens' => 0,
        'locked_tokens' => 0
    ];
    
    $userData = FetchUserData($UserUID);
    $stats['wallet_balance'] = $userData['balance'];
    
    while ($row = $result->fetch_assoc()) {
        $tokens[] = $row;
        
        $tokenValue = $row['balance'] * $row['price'];
        $stats['total_token_value'] += $tokenValue;
        
        if ($row['trading_enabled'] == 1) {
            $stats['tradeable_tokens']++;
        } else {
            $stats['locked_tokens']++;
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => $tokens,
        'stats' => $stats
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
