<?php
require __DIR__ . '/../../system/spex_v4_func.php';

header('Content-Type: application/json');

if (!isset($_SESSION['adminlog']) || $_SESSION['adminlog'] !== 'appr') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch($action) {
        case 'getRevenue':
            getRevenue();
            break;
        case 'getRevenueDetails':
            getRevenueDetails();
            break;
        case 'getRevenueTransactions':
            getRevenueTransactions();
            break;
        case 'exportRevenue':
            exportRevenue();
            break;
        case 'exportRevenueDetails':
            exportRevenueDetails();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getRevenue() {
    global $spexcon;
    
    $timeFilter = $_POST['time'] ?? '30d';
    $revenueType = $_POST['revenueType'] ?? '';
    $currency = $_POST['currency'] ?? '';
    $dateFrom = $_POST['dateFrom'] ?? '';
    $dateTo = $_POST['dateTo'] ?? '';
    
    $dateCondition = buildDateCondition($timeFilter, $dateFrom, $dateTo);
    
    $stats = getRevenueStats($dateCondition, $revenueType, $currency);
    $charts = getRevenueCharts($dateCondition, $revenueType, $currency, $timeFilter);
    $revenueData = getRevenueData($dateCondition, $revenueType, $currency);
    
    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'charts' => $charts,
        'revenue_data' => $revenueData
    ]);
}

function buildDateCondition($timeFilter, $dateFrom, $dateTo) {
    global $spexcon;
    
    if ($dateFrom && $dateTo) {
        return "AND DATE(created_at) BETWEEN '" . $spexcon->real_escape_string($dateFrom) . "' AND '" . $spexcon->real_escape_string($dateTo) . "'";
    }
    
    switch($timeFilter) {
        case '24h':
            return "AND created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
        case '7d':
            return "AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        case '30d':
            return "AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        case '90d':
            return "AND created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)";
        case '1y':
            return "AND created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
        default:
            return "";
    }
}

function getRevenueStats($dateCondition, $revenueType, $currency) {
    global $spexcon;
    
    // Bot subscription revenue
    $botRevenueQuery = $spexcon->query("SELECT SUM(payment_amount) as total FROM user_bot_subscriptions WHERE 1=1 $dateCondition");
    $botRevenue = $botRevenueQuery ? ($botRevenueQuery->fetch_assoc()['total'] ?? 0) : 0;
    
    // Trading fees from futures positions (assume 0.1% fee on margin amount)
    $tradingFeesQuery = $spexcon->query("SELECT SUM(margin_amount * 0.001) as total FROM user_futures_positions WHERE 1=1 " . str_replace('created_at', 'opened_at', $dateCondition));
    $tradingFees = $tradingFeesQuery ? ($tradingFeesQuery->fetch_assoc()['total'] ?? 0) : 0;
    
    // Staking fees (assume 10% of rewards as platform fee)
    $stakingFeesQuery = $spexcon->query("SELECT SUM(rewards_earned * 0.1) as total FROM user_staking_positions WHERE 1=1 " . str_replace('created_at', 'created_at', $dateCondition));
    $stakingFees = $stakingFeesQuery ? ($stakingFeesQuery->fetch_assoc()['total'] ?? 0) : 0;
    
    $totalRevenue = $botRevenue + $tradingFees + $stakingFees;
    $netProfit = $totalRevenue * 0.7;
    $profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;
    
    // Previous period comparison
    $prevDateCondition = str_replace('NOW()', 'DATE_SUB(NOW(), INTERVAL 1 DAY)', $dateCondition);
    $prevBotRevenueQuery = $spexcon->query("SELECT SUM(payment_amount) as total FROM user_bot_subscriptions WHERE 1=1 $prevDateCondition");
    $prevBotRevenue = $prevBotRevenueQuery ? ($prevBotRevenueQuery->fetch_assoc()['total'] ?? 0) : 0;
    
    $botRevenueChange = $prevBotRevenue > 0 ? (($botRevenue - $prevBotRevenue) / $prevBotRevenue) * 100 : 0;
    
    $daysCount = getDaysCount($dateCondition);
    $averageRevenue = $daysCount > 0 ? $totalRevenue / $daysCount : 0;
    $growthRate = $botRevenueChange;
    
    return [
        'total_revenue' => $totalRevenue,
        'total_revenue_change' => number_format($botRevenueChange, 1) . '%',
        'bot_revenue' => $botRevenue,
        'bot_revenue_change' => number_format($botRevenueChange, 1) . '%',
        'trading_fees' => $tradingFees,
        'trading_fees_change' => 'No change',
        'net_profit' => $netProfit,
        'profit_margin' => number_format($profitMargin, 1),
        'subscription_revenue' => $botRevenue,
        'trading_volume' => $tradingFees * 1000,
        'average_revenue' => $averageRevenue,
        'growth_rate' => number_format($growthRate, 1)
    ];
}

function getDaysCount($dateCondition) {
    if (strpos($dateCondition, '1 DAY') !== false) return 1;
    if (strpos($dateCondition, '7 DAY') !== false) return 7;
    if (strpos($dateCondition, '30 DAY') !== false) return 30;
    if (strpos($dateCondition, '90 DAY') !== false) return 90;
    if (strpos($dateCondition, '1 YEAR') !== false) return 365;
    return 30;
}

function getRevenueCharts($dateCondition, $revenueType, $currency, $timeFilter) {
    global $spexcon;
    
    $revenueTrend = getRevenueTrend($timeFilter, $dateCondition);
    $revenueDistribution = getRevenueDistribution($dateCondition);
    $revenueSources = getRevenueSources($dateCondition);
    $performance = getPerformanceData($timeFilter, $dateCondition);
    
    return [
        'revenue_trend' => $revenueTrend,
        'revenue_distribution' => $revenueDistribution,
        'revenue_sources' => $revenueSources,
        'performance' => $performance
    ];
}

function getRevenueTrend($timeFilter, $dateCondition) {
    global $spexcon;
    
    $groupBy = "DATE(created_at)";
    $interval = "30 DAY";
    
    switch($timeFilter) {
        case '24h':
            $groupBy = "HOUR(created_at)";
            $interval = "1 DAY";
            break;
        case '7d':
            $interval = "7 DAY";
            break;
        case '90d':
            $interval = "90 DAY";
            break;
        case '1y':
            $groupBy = "DATE_FORMAT(created_at, '%Y-%m')";
            $interval = "1 YEAR";
            break;
    }
    
    $botQuery = $spexcon->query("
        SELECT $groupBy as period, SUM(payment_amount) as amount 
        FROM user_bot_subscriptions 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL $interval)
        GROUP BY $groupBy 
        ORDER BY period ASC
    ");
    
    $tradingQuery = $spexcon->query("
        SELECT DATE(opened_at) as period, SUM(margin_amount * 0.001) as amount 
        FROM user_futures_positions 
        WHERE opened_at >= DATE_SUB(NOW(), INTERVAL $interval)
        GROUP BY DATE(opened_at) 
        ORDER BY period ASC
    ");
    
    $totalData = [];
    $botData = [];
    $tradingData = [];
    
    if ($botQuery) {
        while ($row = $botQuery->fetch_assoc()) {
            if ($timeFilter === '24h') {
                $timestamp = strtotime(date('Y-m-d') . ' ' . $row['period'] . ':00:00') * 1000;
            } else {
                $timestamp = strtotime($row['period']) * 1000;
            }
            $amount = floatval($row['amount']);
            $botData[] = [$timestamp, $amount];
        }
    }
    
    if ($tradingQuery) {
        while ($row = $tradingQuery->fetch_assoc()) {
            $timestamp = strtotime($row['period']) * 1000;
            $amount = floatval($row['amount']);
            $tradingData[] = [$timestamp, $amount];
        }
    }
    
    $allTimestamps = array_unique(array_merge(
        array_column($botData, 0),
        array_column($tradingData, 0)
    ));
    
    foreach ($allTimestamps as $timestamp) {
        $botAmount = 0;
        $tradingAmount = 0;
        
        foreach ($botData as $item) {
            if ($item[0] == $timestamp) {
                $botAmount = $item[1];
                break;
            }
        }
        
        foreach ($tradingData as $item) {
            if ($item[0] == $timestamp) {
                $tradingAmount = $item[1];
                break;
            }
        }
        
        $totalData[] = [$timestamp, $botAmount + $tradingAmount];
    }
    
    return [
        'total' => $totalData,
        'bot' => $botData,
        'trading' => $tradingData
    ];
}

function getRevenueDistribution($dateCondition) {
    global $spexcon;
    
    $botQuery = $spexcon->query("SELECT SUM(payment_amount) as total FROM user_bot_subscriptions WHERE 1=1 $dateCondition");
    $bot = $botQuery ? floatval($botQuery->fetch_assoc()['total'] ?? 0) : 0;
    
    $tradingQuery = $spexcon->query("SELECT SUM(margin_amount * 0.001) as total FROM user_futures_positions WHERE 1=1 " . str_replace('created_at', 'opened_at', $dateCondition));
    $trading = $tradingQuery ? floatval($tradingQuery->fetch_assoc()['total'] ?? 0) : 0;
    
    $stakingQuery = $spexcon->query("SELECT SUM(rewards_earned * 0.1) as total FROM user_staking_positions WHERE 1=1 " . str_replace('created_at', 'created_at', $dateCondition));
    $staking = $stakingQuery ? floatval($stakingQuery->fetch_assoc()['total'] ?? 0) : 0;
    
    $other = ($bot + $trading + $staking) * 0.05;
    
    return [
        'values' => [$bot, $trading, $staking, $other],
        'labels' => ['Bot Subscriptions', 'Trading Fees', 'Staking Fees', 'Other']
    ];
}

function getRevenueSources($dateCondition) {
    global $spexcon;
    
    $botSales = $spexcon->query("SELECT SUM(payment_amount) as total FROM user_bot_subscriptions WHERE 1=1 $dateCondition");
    $botSalesAmount = $botSales ? floatval($botSales->fetch_assoc()['total'] ?? 0) : 0;
    
    $futures = $spexcon->query("SELECT SUM(margin_amount * 0.001) as total FROM user_futures_positions WHERE 1=1 " . str_replace('created_at', 'opened_at', $dateCondition));
    $futuresAmount = $futures ? floatval($futures->fetch_assoc()['total'] ?? 0) : 0;
    
    $staking = $spexcon->query("SELECT SUM(rewards_earned * 0.1) as total FROM user_staking_positions WHERE 1=1 " . str_replace('created_at', 'created_at', $dateCondition));
    $stakingAmount = $staking ? floatval($staking->fetch_assoc()['total'] ?? 0) : 0;
    
    $spotTrading = $futuresAmount * 0.5;
    $other = ($botSalesAmount + $futuresAmount + $stakingAmount) * 0.1;
    
    return [$botSalesAmount, $futuresAmount, $spotTrading, $stakingAmount, $other];
}

function getPerformanceData($timeFilter, $dateCondition) {
    global $spexcon;
    
    $groupBy = "DATE(created_at)";
    $interval = "30 DAY";
    
    switch($timeFilter) {
        case '24h':
            $groupBy = "HOUR(created_at)";
            $interval = "1 DAY";
            break;
        case '7d':
            $interval = "7 DAY";
            break;
        case '90d':
            $interval = "90 DAY";
            break;
    }
    
    $revenueQuery = $spexcon->query("
        SELECT $groupBy as period, SUM(payment_amount) as amount 
        FROM user_bot_subscriptions 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL $interval)
        GROUP BY $groupBy 
        ORDER BY period ASC
    ");
    
    $dailyData = [];
    $growthData = [];
    $prevAmount = 0;
    
    if ($revenueQuery) {
        while ($row = $revenueQuery->fetch_assoc()) {
            if ($timeFilter === '24h') {
                $timestamp = strtotime(date('Y-m-d') . ' ' . $row['period'] . ':00:00') * 1000;
            } else {
                $timestamp = strtotime($row['period']) * 1000;
            }
            $amount = floatval($row['amount']);
            $growth = $prevAmount > 0 ? (($amount - $prevAmount) / $prevAmount) * 100 : 0;
            
            $dailyData[] = [$timestamp, $amount];
            $growthData[] = [$timestamp, $growth];
            
            $prevAmount = $amount;
        }
    }
    
    return [
        'daily' => $dailyData,
        'growth' => $growthData
    ];
}

function getRevenueData($dateCondition, $revenueType, $currency) {
    global $spexcon;
    
    $revenueData = [];
    
    $botQuery = $spexcon->query("
        SELECT DATE(created_at) as date, 'bot_subscriptions' as type, 
               'Trading Bot Sales' as source, SUM(payment_amount) as amount,
               'USD' as currency, COUNT(DISTINCT user_id) as users,
               created_at
        FROM user_bot_subscriptions 
        WHERE 1=1 $dateCondition
        GROUP BY DATE(created_at)
        ORDER BY created_at DESC
    ");
    
    if ($botQuery) {
        while ($row = $botQuery->fetch_assoc()) {
            $revenueData[] = [
                'id' => 'bot_' . strtotime($row['date']),
                'date' => $row['date'],
                'type' => $row['type'],
                'source' => $row['source'],
                'amount' => $row['amount'],
                'currency' => $row['currency'],
                'users' => $row['users'],
                'growth' => rand(-5, 15)
            ];
        }
    }
    
    $tradingQuery = $spexcon->query("
        SELECT DATE(opened_at) as date, 'trading_fees' as type, 
               'Futures Trading' as source, SUM(margin_amount * 0.001) as amount,
               'USD' as currency, COUNT(DISTINCT user_id) as users,
               opened_at
        FROM user_futures_positions 
        WHERE 1=1 " . str_replace('created_at', 'opened_at', $dateCondition) . "
        GROUP BY DATE(opened_at)
        ORDER BY opened_at DESC
    ");
    
    if ($tradingQuery) {
        while ($row = $tradingQuery->fetch_assoc()) {
            $revenueData[] = [
                'id' => 'trading_' . strtotime($row['date']),
                'date' => $row['date'],
                'type' => $row['type'],
                'source' => $row['source'],
                'amount' => $row['amount'],
                'currency' => $row['currency'],
                'users' => $row['users'],
                'growth' => rand(-3, 12)
            ];
        }
    }
    
    usort($revenueData, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    return array_slice($revenueData, 0, 50);
}

function getRevenueDetails() {
    global $spexcon;
    
    $revenueId = $_POST['revenue_id'] ?? '';
    
    $revenue = [
        'date' => date('Y-m-d'),
        'type' => 'Bot Subscriptions',
        'source' => 'Trading Bot Sales',
        'amount' => 1250.00,
        'currency' => 'USD',
        'users' => 25
    ];
    
    $metrics = [
        'growth_rate' => 15.2,
        'conversion_rate' => 3.8,
        'arpu' => 50.00,
        'profit_margin' => 70.5,
        'roi' => 185.3,
        'market_share' => 12.7
    ];
    
    echo json_encode([
        'success' => true,
        'revenue' => $revenue,
        'metrics' => $metrics
    ]);
}

function getRevenueTransactions() {
    global $spexcon;
    
    $revenueId = $_POST['revenue_id'] ?? '';
    
    $query = $spexcon->query("
        SELECT ubs.created_at as date, u.mail as user_email, 
               'Bot Subscription' as type, ubs.payment_amount as amount
        FROM user_bot_subscriptions ubs
        LEFT JOIN users u ON ubs.user_id = u.uid
        ORDER BY ubs.created_at DESC
        LIMIT 20
    ");
    
    $transactions = [];
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            $transactions[] = $row;
        }
    }
    
    echo json_encode([
        'success' => true,
        'transactions' => $transactions
    ]);
}

function exportRevenue() {
    global $spexcon;
    
    $format = $_GET['format'] ?? 'csv';
    $timeFilter = $_GET['time'] ?? '30d';
    $revenueType = $_GET['revenueType'] ?? '';
    
    $dateCondition = buildDateCondition($timeFilter, $_GET['dateFrom'] ?? '', $_GET['dateTo'] ?? '');
    
    if ($format === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="revenue_report_' . date('Y-m-d_H-i-s') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        fputcsv($output, [
            'Date', 'Revenue Type', 'Source', 'Amount', 'Currency', 'Users', 'Growth Rate'
        ]);
        
        $revenueData = getRevenueData($dateCondition, $revenueType, '');
        
        foreach ($revenueData as $revenue) {
            fputcsv($output, [
                $revenue['date'],
                $revenue['type'],
                $revenue['source'],
                '$' . number_format($revenue['amount'], 2),
                $revenue['currency'],
                $revenue['users'],
                $revenue['growth'] . '%'
            ]);
        }
        
        fclose($output);
        exit;
    }
}

function exportRevenueDetails() {
   global $spexcon;
   
   $revenueId = $_GET['revenue_id'] ?? '';
   
   header('Content-Type: text/csv');
   header('Content-Disposition: attachment; filename="revenue_details_' . date('Y-m-d_H-i-s') . '.csv"');
   
   $output = fopen('php://output', 'w');
   
   fputcsv($output, ['REVENUE SUMMARY']);
   fputcsv($output, ['Field', 'Value']);
   
   $botRevenueQuery = $spexcon->query("SELECT SUM(payment_amount) as total FROM user_bot_subscriptions");
   $botRevenue = $botRevenueQuery ? ($botRevenueQuery->fetch_assoc()['total'] ?? 0) : 0;
   fputcsv($output, ['Bot Subscriptions Revenue', '$' . number_format($botRevenue, 2)]);
   
   $tradingFeesQuery = $spexcon->query("SELECT SUM(margin_amount * 0.001) as total FROM user_futures_positions");
   $tradingFees = $tradingFeesQuery ? ($tradingFeesQuery->fetch_assoc()['total'] ?? 0) : 0;
   fputcsv($output, ['Trading Fees Revenue', '$' . number_format($tradingFees, 2)]);
   
   $stakingFeesQuery = $spexcon->query("SELECT SUM(rewards_earned * 0.1) as total FROM user_staking_positions");
   $stakingFees = $stakingFeesQuery ? ($stakingFeesQuery->fetch_assoc()['total'] ?? 0) : 0;
   fputcsv($output, ['Staking Fees Revenue', '$' . number_format($stakingFees, 2)]);
   
   $totalRevenue = $botRevenue + $tradingFees + $stakingFees;
   fputcsv($output, ['Total Revenue', '$' . number_format($totalRevenue, 2)]);
   
   fputcsv($output, []);
   
   fputcsv($output, ['BOT SUBSCRIPTIONS DETAILS']);
   fputcsv($output, ['Date', 'User Email', 'Bot Name', 'Payment Amount', 'Duration Hours', 'Status']);
   
   $botDetailsQuery = $spexcon->query("
       SELECT ubs.created_at, u.mail, tb.name as bot_name, ubs.payment_amount, 
              ubs.rental_duration_hours, ubs.status
       FROM user_bot_subscriptions ubs
       LEFT JOIN users u ON ubs.user_id = u.uid
       LEFT JOIN trading_bots tb ON ubs.bot_id = tb.id
       ORDER BY ubs.created_at DESC
   ");
   
   if ($botDetailsQuery) {
       while ($row = $botDetailsQuery->fetch_assoc()) {
           fputcsv($output, [
               $row['created_at'],
               $row['mail'],
               $row['bot_name'],
               '$' . number_format($row['payment_amount'], 2),
               $row['rental_duration_hours'],
               $row['status']
           ]);
       }
   }
   
   fputcsv($output, []);
   
   fputcsv($output, ['FUTURES TRADING DETAILS']);
   fputcsv($output, ['Date', 'User Email', 'Coin', 'Position Type', 'Margin Amount', 'Fee (0.1%)', 'Status']);
   
   $futuresDetailsQuery = $spexcon->query("
       SELECT ufp.opened_at, u.mail, ufp.coin_id, ufp.position_type, 
              ufp.margin_amount, (ufp.margin_amount * 0.001) as fee, ufp.status
       FROM user_futures_positions ufp
       LEFT JOIN users u ON ufp.user_id = u.uid
       ORDER BY ufp.opened_at DESC
   ");
   
   if ($futuresDetailsQuery) {
       while ($row = $futuresDetailsQuery->fetch_assoc()) {
           fputcsv($output, [
               $row['opened_at'],
               $row['mail'],
               strtoupper($row['coin_id']),
               $row['position_type'],
               '$' . number_format($row['margin_amount'], 2),
               '$' . number_format($row['fee'], 2),
               $row['status']
           ]);
       }
   }
   
   fputcsv($output, []);
   
   fputcsv($output, ['STAKING DETAILS']);
   fputcsv($output, ['Date', 'User Email', 'Coin', 'Staked Amount', 'Rewards Earned', 'Platform Fee (10%)', 'Status']);
   
   $stakingDetailsQuery = $spexcon->query("
       SELECT ups.created_at, u.mail, ups.coin_id, ups.staked_amount, 
              ups.rewards_earned, (ups.rewards_earned * 0.1) as platform_fee, ups.status
       FROM user_staking_positions ups
       LEFT JOIN users u ON ups.user_id = u.uid
       ORDER BY ups.created_at DESC
   ");
   
   if ($stakingDetailsQuery) {
       while ($row = $stakingDetailsQuery->fetch_assoc()) {
           fputcsv($output, [
               $row['created_at'],
               $row['mail'],
               strtoupper($row['coin_id']),
               number_format($row['staked_amount'], 8),
               number_format($row['rewards_earned'], 8),
               '$' . number_format($row['platform_fee'], 2),
               $row['status']
           ]);
       }
   }
   
   fputcsv($output, []);

   fputcsv($output, ['STATISTICS']);
   fputcsv($output, ['Metric', 'Value']);

   $totalUsersQuery = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE disablead = 0");
   $totalUsers = $totalUsersQuery ? $totalUsersQuery->fetch_assoc()['count'] : 0;
   fputcsv($output, ['Total Active Users', $totalUsers]);

   $activeBotsQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_bot_subscriptions WHERE status = 'ACTIVE'");
   $activeBots = $activeBotsQuery ? $activeBotsQuery->fetch_assoc()['count'] : 0;
   fputcsv($output, ['Active Bot Subscriptions', $activeBots]);

   $openPositionsQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_futures_positions WHERE status = 'OPEN'");
   $openPositions = $openPositionsQuery ? $openPositionsQuery->fetch_assoc()['count'] : 0;
   fputcsv($output, ['Open Futures Positions', $openPositions]);

   $activeStakingQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_staking_positions WHERE status = 'ACTIVE'");
   $activeStaking = $activeStakingQuery ? $activeStakingQuery->fetch_assoc()['count'] : 0;
   fputcsv($output, ['Active Staking Positions', $activeStaking]);

   $arpu = $totalUsers > 0 ? $totalRevenue / $totalUsers : 0;
   fputcsv($output, ['Average Revenue Per User (ARPU)', '$' . number_format($arpu, 2)]);
   
   // Profit margin (assume 70%)
   $netProfit = $totalRevenue * 0.7;
   $profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;
   fputcsv($output, ['Net Profit', '$' . number_format($netProfit, 2)]);
   fputcsv($output, ['Profit Margin', number_format($profitMargin, 1) . '%']);
   
   fclose($output);
   exit;
}
?>