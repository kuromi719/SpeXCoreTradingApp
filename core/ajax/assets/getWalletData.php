<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../system/spex_v4_func.php';

if (!getSession('Login')) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$UserUID = getSession('UID');

try {
    $spotQuery = "SELECT balance FROM users WHERE uid = ?";
    $stmt = $spexcon->prepare($spotQuery);
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    
    $spotBalance = $userData ? floatval($userData['balance']) : 0;
    
    $coinBalancesQuery = "
        SELECT 
            SUM(ucb.total_balance * cmd.current_price_usd) as total_coin_value
        FROM user_coin_balances ucb
        LEFT JOIN coin_market_data cmd ON ucb.coin_id = cmd.coin_id
        WHERE ucb.user_id = ? AND ucb.total_balance > 0
    ";
    $stmt = $spexcon->prepare($coinBalancesQuery);
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    $result = $stmt->get_result();
    $coinData = $result->fetch_assoc();
    
    $spotCoinValue = $coinData ? floatval($coinData['total_coin_value']) : 0;
    $totalSpotBalance = $spotBalance + $spotCoinValue;
    
    $futuresQuery = "
        SELECT 
            SUM(ufp.margin_amount) as total_margin,
            SUM(ufp.unrealized_pnl) as total_pnl
        FROM user_futures_positions ufp
        WHERE ufp.user_id = ? AND ufp.status = 'OPEN'
    ";
    $stmt = $spexcon->prepare($futuresQuery);
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    $result = $stmt->get_result();
    $futuresData = $result->fetch_assoc();
    
    $futuresBalance = $futuresData ? (floatval($futuresData['total_margin']) + floatval($futuresData['total_pnl'])) : 0;
    
    $stakingQuery = "
        SELECT 
            SUM(usp.stake_value_usd) as total_staked,
            SUM(usp.rewards_earned * cmd.current_price_usd) as total_rewards
        FROM user_staking_positions usp
        LEFT JOIN coin_market_data cmd ON usp.coin_id = cmd.coin_id
        WHERE usp.user_id = ? AND usp.status = 'ACTIVE'
    ";
    $stmt = $spexcon->prepare($stakingQuery);
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    $result = $stmt->get_result();
    $stakingData = $result->fetch_assoc();
    
    $earnBalance = $stakingData ? (floatval($stakingData['total_staked']) + floatval($stakingData['total_rewards'])) : 0;
    
    $botQuery = "
        SELECT 
            SUM(payment_amount) as total_invested,
            SUM(total_earned) as total_earned
        FROM user_bot_subscriptions 
        WHERE user_id = ? AND status = 'ACTIVE'
    ";
    $stmt = $spexcon->prepare($botQuery);
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    $result = $stmt->get_result();
    $botData = $result->fetch_assoc();
    
    $botBalance = $botData ? (floatval($botData['total_invested']) + floatval($botData['total_earned'])) : 0;
    
    $marginBalance = 0;
    
    $totalBalance = $totalSpotBalance + $marginBalance + $futuresBalance + $earnBalance + $botBalance;
    
    $spotAssetsQuery = "
        SELECT 
            ucb.coin_id,
            c.symbol,
            c.name,
            ucb.total_balance,
            ucb.available_balance,
            ucb.locked_balance,
            cmd.current_price_usd,
            (ucb.total_balance * cmd.current_price_usd) as usd_value,
            ci.small as logo
        FROM user_coin_balances ucb
        LEFT JOIN coins c ON ucb.coin_id = c.id
        LEFT JOIN coin_market_data cmd ON c.id = cmd.coin_id
        LEFT JOIN coin_images ci ON c.id = ci.coin_id
        WHERE ucb.user_id = ? AND ucb.total_balance > 0
        ORDER BY (ucb.total_balance * cmd.current_price_usd) DESC
    ";
    $stmt = $spexcon->prepare($spotAssetsQuery);
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $spotAssets = [];
    while ($row = $result->fetch_assoc()) {
        $spotAssets[] = [
            'coin_id' => $row['coin_id'],
            'symbol' => $row['symbol'],
            'name' => $row['name'],
            'balance' => $row['total_balance'],
            'available' => $row['available_balance'],
            'locked' => $row['locked_balance'],
            'price' => $row['current_price_usd'],
            'usd_value' => $row['usd_value'],
            'logo' => $row['logo']
        ];
    }
    
    if ($spotBalance > 0) {
        array_unshift($spotAssets, [
            'coin_id' => 'tether',
            'symbol' => 'USDT',
            'name' => 'Tether USD',
            'balance' => $spotBalance,
            'available' => $spotBalance,
            'locked' => 0,
            'price' => 1.0,
            'usd_value' => $spotBalance,
            'logo' => 'https://coin-images.coingecko.com/coins/images/325/large/Tether.png'
        ]);
    }
    
    $popularCoinsQuery = "
        SELECT 
            c.id,
            c.symbol,
            c.name,
            cmd.current_price_usd,
            cmd.price_change_percentage_24h,
            cmd.market_cap_usd,
            ci.small as logo,
            COALESCE(ucb.total_balance, 0) as user_balance,
            COALESCE(ucb.total_balance * cmd.current_price_usd, 0) as user_balance_usd
        FROM coins c
        LEFT JOIN coin_market_data cmd ON c.id = cmd.coin_id
        LEFT JOIN coin_images ci ON c.id = ci.coin_id
        LEFT JOIN user_coin_balances ucb ON c.id = ucb.coin_id AND ucb.user_id = ?
        WHERE cmd.market_cap_rank <= 20 AND cmd.market_cap_rank > 0
        ORDER BY cmd.market_cap_rank ASC
    ";
    $stmt = $spexcon->prepare($popularCoinsQuery);
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $popularCoins = [];
    while ($row = $result->fetch_assoc()) {
        $popularCoins[] = [
            'coin_id' => $row['id'],
            'symbol' => $row['symbol'],
            'name' => $row['name'],
            'balance' => $row['user_balance'],
            'current_price' => $row['current_price_usd'],
            'change_24h' => $row['price_change_percentage_24h'],
            'market_cap' => $row['market_cap_usd'],
            'usd_value' => $row['user_balance_usd'],
            'logo' => $row['logo']
        ];
    }
    
    $usdtData = ['symbol' => 'USDT', 'name' => 'Tether USD', 'balance' => $spotBalance, 'current_price' => 1.0, 'change_24h' => 0, 'market_cap' => 120000000000, 'usd_value' => $spotBalance, 'logo' => 'https://coin-images.coingecko.com/coins/images/325/large/Tether.png', 'coin_id' => 'tether'];
    if ($spotBalance > 0) {
        array_unshift($popularCoins, $usdtData);
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'spot_balance' => $totalSpotBalance,
            'spot_balance_usd' => $totalSpotBalance,
            'margin_balance' => $marginBalance,
            'margin_balance_usd' => $marginBalance,
            'futures_balance' => $futuresBalance,
            'futures_balance_usd' => $futuresBalance,
            'earn_balance' => $earnBalance,
            'earn_balance_usd' => $earnBalance,
            'bot_balance' => $botBalance,
            'bot_balance_usd' => $botBalance,
            'total_balance' => $totalBalance,
            'spot_assets' => $spotAssets,
            'popular_coins' => $popularCoins
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Wallet Data Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error loading wallet data']);
}
?>