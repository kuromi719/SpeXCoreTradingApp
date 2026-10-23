
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

if (!getSession('Login')) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$UserUID = getSession('UID');

try {
    $positionsQuery = "
        SELECT 
            ufp.id,
            ufp.coin_id,
            c.symbol,
            c.name,
            ufp.position_type,
            ufp.position_size,
            ufp.entry_price,
            ufp.current_price,
            ufp.margin_amount,
            ufp.leverage,
            ufp.unrealized_pnl,
            ufp.liquidation_price,
            ufp.opened_at,
            ci.small as logo
        FROM user_futures_positions ufp
        LEFT JOIN coins c ON ufp.coin_id = c.id
        LEFT JOIN coin_images ci ON c.id = ci.coin_id
        WHERE ufp.user_id = ? AND ufp.status = 'OPEN'
        ORDER BY ufp.opened_at DESC
    ";
    $stmt = $spexcon->prepare($positionsQuery);
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $positions = [];
    $totalMargin = 0;
    $totalUnrealizedPnL = 0;
    
    while ($row = $result->fetch_assoc()) {
        $totalMargin += floatval($row['margin_amount']);
        $totalUnrealizedPnL += floatval($row['unrealized_pnl']);
        
        $positions[] = [
            'id' => $row['id'],
            'coin_id' => $row['coin_id'],
            'symbol' => $row['symbol'],
            'name' => $row['name'],
            'position_type' => $row['position_type'],
            'position_size' => $row['position_size'],
            'entry_price' => $row['entry_price'],
            'current_price' => $row['current_price'],
            'margin_amount' => $row['margin_amount'],
            'leverage' => $row['leverage'],
            'unrealized_pnl' => $row['unrealized_pnl'],
            'liquidation_price' => $row['liquidation_price'],
            'opened_at' => $row['opened_at'],
            'logo' => $row['logo']
        ];
    }
    
    $usdtQuery = "SELECT balance FROM users WHERE uid = ?";
    $stmt = $spexcon->prepare($usdtQuery);
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    $availableBalance = $userData ? floatval($userData['balance']) : 0;
    
    echo json_encode([
        'success' => true,
        'data' => [
            'total_margin' => $totalMargin,
            'unrealized_pnl' => $totalUnrealizedPnL,
            'available_balance' => $availableBalance,
            'positions' => $positions
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Futures Data Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error loading futures data']);
}
?>