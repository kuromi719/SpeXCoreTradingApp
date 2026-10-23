<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

if (!getSession('Login')) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$UserUID = getSession('UID');

try {
    $usdtQuery = "SELECT balance FROM users WHERE uid = ?";
    $stmt = $spexcon->prepare($usdtQuery);
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    $usdtBalance = $userData ? floatval($userData['balance']) : 0;

    $assetsQuery = "
        SELECT 
            c.id as coin_id,
            c.symbol,
            c.name,
            COALESCE(ucb.total_balance, 0) as balance,
            COALESCE(ucb.total_balance * cmd.current_price_usd, 0) as balance_usd,
            cmd.current_price_usd as price,
            cmd.price_change_percentage_24h as change_24h,
            cmd.total_volume_usd as volume_24h,
            cmd.market_cap_usd as market_cap,
            ci.small as logo,
            0.001 as funding_rate
        FROM coins c
        LEFT JOIN coin_market_data cmd ON c.id = cmd.coin_id
        LEFT JOIN coin_images ci ON c.id = ci.coin_id
        LEFT JOIN user_coin_balances ucb ON c.id = ucb.coin_id AND ucb.user_id = ?
        WHERE cmd.market_cap_rank <= 50 AND cmd.market_cap_rank > 0
        ORDER BY 
            CASE WHEN ucb.total_balance > 0 THEN 0 ELSE 1 END,
            cmd.market_cap_rank ASC
    ";
    $stmt = $spexcon->prepare($assetsQuery);
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $assets = [];
    $totalBalanceUSD = $usdtBalance;
    
    while ($row = $result->fetch_assoc()) {
        $balanceUSD = floatval($row['balance_usd']);
        $totalBalanceUSD += $balanceUSD;
        
        $assets[] = [
            'coin_id' => $row['coin_id'],
            'symbol' => $row['symbol'],
            'name' => $row['name'],
            'balance' => $row['balance'],
            'balance_usd' => $balanceUSD,
            'price' => $row['price'],
            'change_24h' => $row['change_24h'] ?: 0,
            'volume_24h' => $row['volume_24h'] ?: 0,
            'market_cap' => $row['market_cap'] ?: 0,
            'logo' => $row['logo'],
            'funding_rate' => $row['funding_rate']
        ];
    }
    
    if ($usdtBalance > 0) {
        array_unshift($assets, [
            'coin_id' => 'tether',
            'symbol' => 'USDT',
            'name' => 'Tether USD',
            'balance' => $usdtBalance,
            'balance_usd' => $usdtBalance,
            'price' => 1.0,
            'change_24h' => 0,
            'volume_24h' => 50000000000,
            'market_cap' => 120000000000,
            'logo' => 'https://coin-images.coingecko.com/coins/images/325/large/Tether.png',
            'funding_rate' => 0
        ]);
    }
    
    $todayPnL = 0;
    $todayPnLPercentage = 0;
    
    echo json_encode([
        'success' => true,
        'data' => [
            'total_balance' => $totalBalanceUSD / (float)1, // Convert to selected currency
            'total_balance_usd' => $totalBalanceUSD,
            'usdt_balance' => $usdtBalance,
            'today_pnl' => $todayPnL,
            'today_pnl_percentage' => $todayPnLPercentage,
            'assets' => $assets
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Spot Data Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error loading spot data']);
}
?>