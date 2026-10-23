<?php
require __DIR__ . '/../../system/spex_v4_func.php';

header('Content-Type: application/json');

try {
    // Total users count
    $totalUsersQuery = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE disablead = 0");
    $totalUsers = $totalUsersQuery->fetch_assoc()['count'];
    
    // New users today
    $newUsersTodayQuery = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE DATE(register_date) = CURDATE() AND disablead = 0");
    $newUsersToday = $newUsersTodayQuery->fetch_assoc()['count'];
    
    // Weekly users
    $weeklyUsersQuery = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE register_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND disablead = 0");
    $weeklyUsers = $weeklyUsersQuery->fetch_assoc()['count'];
    
    // Monthly users
    $monthlyUsersQuery = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE register_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND disablead = 0");
    $monthlyUsers = $monthlyUsersQuery->fetch_assoc()['count'];
    
    // Total balance from user_coin_balances
    $totalBalanceQuery = $spexcon->query("SELECT SUM(total_balance) as total FROM user_coin_balances");
    $totalBalanceResult = $totalBalanceQuery->fetch_assoc();
    $totalBalance = $totalBalanceResult['total'] ?? 0;
    
    // Balance change (today vs yesterday)
    $todayBalanceQuery = $spexcon->query("SELECT SUM(total_balance) as total FROM user_coin_balances WHERE DATE(updated_at) = CURDATE()");
    $todayBalanceResult = $todayBalanceQuery->fetch_assoc();
    $todayBalance = $todayBalanceResult['total'] ?? 0;
    
    $yesterdayBalanceQuery = $spexcon->query("SELECT SUM(total_balance) as total FROM user_coin_balances WHERE DATE(updated_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)");
    $yesterdayBalanceResult = $yesterdayBalanceQuery->fetch_assoc();
    $yesterdayBalance = $yesterdayBalanceResult['total'] ?? 0;
    
    $balanceChange = $todayBalance - $yesterdayBalance;
    
    // Today's futures positions (using opened_at not created_at!)
    $todayTransactionsQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_futures_positions WHERE DATE(opened_at) = CURDATE()");
    $todayTransactions = $todayTransactionsQuery->fetch_assoc()['count'];
    
    // Transaction volume from futures positions (using opened_at)
    $transactionVolumeQuery = $spexcon->query("SELECT SUM(margin_amount) as volume FROM user_futures_positions WHERE DATE(opened_at) = CURDATE()");
    $transactionVolumeResult = $transactionVolumeQuery->fetch_assoc();
    $transactionVolume = $transactionVolumeResult['volume'] ?? 0;
    
    // Active bots
    $activeBotsQuery = $spexcon->query("SELECT COUNT(*) as count FROM trading_bots WHERE is_active = 1");
    $activeBots = $activeBotsQuery->fetch_assoc()['count'];
    
    // Bot earnings from user_bot_earnings (using earning_hour not created_at!)
    $botEarningsQuery = $spexcon->query("SELECT SUM(earning_amount) as total FROM user_bot_earnings WHERE DATE(earning_hour) = CURDATE()");
    $botEarningsResult = $botEarningsQuery->fetch_assoc();
    $botEarnings = $botEarningsResult['total'] ?? 0;
    
    // Active bot subscriptions
    $botSubscriptionsQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_bot_subscriptions WHERE status = 'ACTIVE'");
    $botSubscriptions = $botSubscriptionsQuery->fetch_assoc()['count'];
    
    // Active users (logged in within last 24 hours - using login_time)
    $activeUsersQuery = $spexcon->query("SELECT COUNT(DISTINCT user_id) as count FROM user_login_history WHERE login_time >= DATE_SUB(NOW(), INTERVAL 24 HOUR) AND status = 'SUCCESS'");
    $activeUsers = $activeUsersQuery->fetch_assoc()['count'];
    
    // Verified users (using submitted_at)
    $verifiedUsersQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_identifications WHERE status = 'APPROVED'");
    $verifiedUsers = $verifiedUsersQuery->fetch_assoc()['count'];
    
    // Total deposits (sum of positive balances from users table)
    $totalDepositsQuery = $spexcon->query("SELECT SUM(CAST(balance AS DECIMAL(20,8))) as total FROM users WHERE disablead = 0 AND CAST(balance AS DECIMAL(20,8)) > 0");
    $totalDepositsResult = $totalDepositsQuery->fetch_assoc();
    $totalDeposits = $totalDepositsResult['total'] ?? 0;
    
    // Total withdrawals (sum of negative unrealized_pnl from closed positions)
    $totalWithdrawalsQuery = $spexcon->query("SELECT SUM(ABS(unrealized_pnl)) as total FROM user_futures_positions WHERE status = 'CLOSED' AND unrealized_pnl < 0");
    $totalWithdrawalsResult = $totalWithdrawalsQuery->fetch_assoc();
    $totalWithdrawals = $totalWithdrawalsResult['total'] ?? 0;
    
    // Trading volume from all futures positions (using margin_amount)
    $tradingVolumeQuery = $spexcon->query("SELECT SUM(margin_amount) as total FROM user_futures_positions");
    $tradingVolumeResult = $tradingVolumeQuery->fetch_assoc();
    $tradingVolume = $tradingVolumeResult['total'] ?? 0;
    
    // Open futures positions
    $openPositionsQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_futures_positions WHERE status = 'OPEN'");
    $openPositions = $openPositionsQuery->fetch_assoc()['count'];
    
    // Closed positions today (using closed_at not updated_at!)
    $closedTodayQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_futures_positions WHERE status = 'CLOSED' AND DATE(closed_at) = CURDATE()");
    $closedToday = $closedTodayQuery->fetch_assoc()['count'];
    
    // Recent users
    $recentUsersQuery = $spexcon->query("
        SELECT uid, nickname, mail, disablead, register_date 
        FROM users 
        WHERE disablead = 0
        ORDER BY register_date DESC 
        LIMIT 5
    ");
    $recentUsers = [];
    while($row = $recentUsersQuery->fetch_assoc()) {
        $recentUsers[] = [
            'uid' => $row['uid'],
            'name' => $row['nickname'] ?: 'User#' . $row['uid'],
            'mail' => $row['mail'],
            'blocked' => $row['disablead'],
            'created_at' => $row['register_date']
        ];
    }
    
    // Recent transactions (from futures positions - using opened_at)
    $recentTransactionsQuery = $spexcon->query("
        SELECT ufp.user_id, ufp.position_type as type, ufp.margin_amount as amount, ufp.opened_at, ufp.coin_id
        FROM user_futures_positions ufp
        ORDER BY ufp.opened_at DESC 
        LIMIT 5
    ");
    $recentTransactions = [];
    while($row = $recentTransactionsQuery->fetch_assoc()) {
        $recentTransactions[] = [
            'user_id' => $row['user_id'],
            'type' => $row['type'] . ' (' . strtoupper($row['coin_id']) . ')',
            'amount' => $row['amount'],
            'created_at' => $row['opened_at']
        ];
    }
    
    // Staking positions (using created_at - bu tabloda var!)
    $stakingPositionsQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_staking_positions WHERE status = 'ACTIVE'");
    $stakingPositions = $stakingPositionsQuery->fetch_assoc()['count'];
    
    // Total staked value
    $stakedValueQuery = $spexcon->query("SELECT SUM(stake_value_usd) as total FROM user_staking_positions WHERE status = 'ACTIVE'");
    $stakedValueResult = $stakedValueQuery->fetch_assoc();
    $stakedValue = $stakedValueResult['total'] ?? 0;
    
    $response = [
        'success' => true,
        'total_users' => (int)$totalUsers,
        'new_users_today' => (int)$newUsersToday,
        'weekly_users' => (int)$weeklyUsers,
        'monthly_users' => (int)$monthlyUsers,
        'active_users' => (int)$activeUsers,
        'verified_users' => (int)$verifiedUsers,
        
        'total_balance' => number_format($totalBalance, 2),
        'balance_change' => number_format($balanceChange, 2),
        
        'today_transactions' => (int)$todayTransactions,
        'transaction_volume' => number_format($transactionVolume, 2),
        'total_deposits' => number_format($totalDeposits, 2),
        'total_withdrawals' => number_format($totalWithdrawals, 2),
        'trading_volume' => number_format($tradingVolume, 2),
        
        'active_bots' => (int)$activeBots,
        'bot_earnings' => number_format($botEarnings, 2),
        'bot_subscriptions' => (int)$botSubscriptions,
        
        'open_positions' => (int)$openPositions,
        'closed_today' => (int)$closedToday,
        'staking_positions' => (int)$stakingPositions,
        'staked_value' => number_format($stakedValue, 2),
        
        'recent_users' => $recentUsers,
        'recent_transactions' => $recentTransactions,
        
        // Chart data for user registrations (last 12 months)
        'chart_data' => getMonthlyUserData()
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

?>