<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';
header('Content-Type: application/json');

$UID = getSession('UID');
if (empty($UID)) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$action = $_POST['action'] ?? '';

switch($action) {
    case 'getDashboardData': getDashboardData(); break;
    default: echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function getDashboardData() {
    global $spexcon, $UID;
    
    $response = [
        'success' => true,
        'stats' => getStats(),
        'charts' => getChartData(),
        'tables' => getTableData2()
    ];
    
    echo json_encode($response);
}

function getStats() {
    global $spexcon, $UID;
    
    // Get user balance
    $userQuery = $spexcon->query("SELECT balance FROM users WHERE uid = $UID");
    $balance = $userQuery ? (float)$userQuery->fetch_assoc()['balance'] : 0;
    
    // Get trade stats
    $tradeStats = ['total_trades' => 0, 'win_rate' => 0, 'total_volume' => 0];
    $transTableExists = $spexcon->query("SHOW TABLES LIKE 'user_transactions'");
    
    if($transTableExists && $transTableExists->num_rows > 0) {
        $tradeQuery = $spexcon->query("
            SELECT 
                COUNT(*) as total_trades,
                SUM(COALESCE(total_value_usd, 0)) as total_volume,
                COUNT(CASE WHEN transaction_type IN ('SELL', 'MARKET_SELL') THEN 1 END) as sell_trades
            FROM user_transactions 
            WHERE user_id = $UID AND transaction_type IN ('BUY', 'SELL', 'MARKET_BUY', 'MARKET_SELL')
        ");
        
        if($tradeQuery) {
            $stats = $tradeQuery->fetch_assoc();
            $tradeStats['total_trades'] = (int)$stats['total_trades'];
            $tradeStats['total_volume'] = (float)$stats['total_volume'];
            $tradeStats['win_rate'] = $stats['total_trades'] > 0 ? 
                round(($stats['sell_trades'] / $stats['total_trades']) * 100, 1) : 0;
        }
    }
    
    return [
        'total_balance' => $balance,
        'total_trades' => $tradeStats['total_trades'],
        'win_rate' => $tradeStats['win_rate'],
        'total_volume' => $tradeStats['total_volume']
    ];
}

function getChartData() {
    global $spexcon, $UID;
    
    $charts = [
        'portfolio' => getPortfolioData(),
        'activity' => getActivityData(),
        'balance' => getBalanceHistory()
    ];
    
    return $charts;
}

function getPortfolioData() {
    global $spexcon, $UID;
    
    $portfolio = [];
    
    // Check if user_coin_balances exists
    $tokenTableExists = $spexcon->query("SHOW TABLES LIKE 'user_coin_balances'");
    if($tokenTableExists && $tokenTableExists->num_rows > 0) {
        $query = $spexcon->query("
            SELECT coin_id as coin, total_balance as amount, (total_balance * 1) as value 
            FROM user_coin_balances 
            WHERE user_id = $UID AND total_balance > 0 
            ORDER BY total_balance DESC 
            LIMIT 10
        ");
        
        if($query) {
            while($row = $query->fetch_assoc()) {
                $portfolio[] = [
                    'coin' => $row['coin'],
                    'amount' => (float)$row['amount'],
                    'value' => (float)$row['value']
                ];
            }
        }
    }
    
    // If no tokens, add USD balance
    if(empty($portfolio)) {
        $userQuery = $spexcon->query("SELECT balance FROM users WHERE uid = $UID");
        $balance = $userQuery ? (float)$userQuery->fetch_assoc()['balance'] : 0;
        
        if($balance > 0) {
            $portfolio[] = [
                'coin' => 'USD',
                'amount' => $balance,
                'value' => $balance
            ];
        }
    }
    
    return $portfolio;
}

function getActivityData() {
    global $spexcon, $UID;
    
    $activity = [];
    
    $transTableExists = $spexcon->query("SHOW TABLES LIKE 'user_transactions'");
    if($transTableExists && $transTableExists->num_rows > 0) {
        $query = $spexcon->query("
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as count
            FROM user_transactions 
            WHERE user_id = $UID 
            AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date DESC
            LIMIT 30
        ");
        
        if($query) {
            while($row = $query->fetch_assoc()) {
                $activity[] = [
                    'date' => date('M j', strtotime($row['date'])),
                    'count' => (int)$row['count']
                ];
            }
        }
    }
    
    return array_reverse($activity);
}

function getBalanceHistory() {
    global $spexcon, $UID;
    
    $history = [];
    
    // Get current balance
    $userQuery = $spexcon->query("SELECT balance FROM users WHERE uid = $UID");
    $currentBalance = $userQuery ? (float)$userQuery->fetch_assoc()['balance'] : 0;
    
    // Simulate balance history
    $logTableExists = $spexcon->query("SHOW TABLES LIKE 'user_logs'");
    if($logTableExists && $logTableExists->num_rows > 0) {
        $query = $spexcon->query("
            SELECT 
                DATE(date) as date,
                SUM(CASE WHEN amount > 0 THEN amount ELSE 0 END) as balance_change
            FROM user_logs 
            WHERE uid = '$UID' 
            AND date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(date)
            ORDER BY date ASC
            LIMIT 30
        ");
        
        $runningBalance = $currentBalance;
        if($query) {
            while($row = $query->fetch_assoc()) {
                $history[] = [
                    'date' => date('M j', strtotime($row['date'])),
                    'balance' => $runningBalance
                ];
                $runningBalance -= (float)$row['balance_change'];
            }
        }
    }
    
    if(empty($history)) {
        for($i = 7; $i >= 0; $i--) {
            $history[] = [
                'date' => date('M j', strtotime("-$i days")),
                'balance' => $currentBalance
            ];
        }
    }
    
    return $history;
}

function getTableData2() {
    global $spexcon, $UID;
    
    $tables = [
        'transactions' => getRecentTransactions(),
        'holdings' => getTopHoldings()
    ];
    
    return $tables;
}

function getRecentTransactions() {
    global $spexcon, $UID;
    
    $transactions = [];
    
    $transTableExists = $spexcon->query("SHOW TABLES LIKE 'user_transactions'");
    if($transTableExists && $transTableExists->num_rows > 0) {
        $query = $spexcon->query("
            SELECT transaction_type, total_value_usd, created_at
            FROM user_transactions 
            WHERE user_id = $UID 
            ORDER BY created_at DESC 
            LIMIT 10
        ");
        
        if($query) {
            while($row = $query->fetch_assoc()) {
                $transactions[] = $row;
            }
        }
    }
    
    return $transactions;
}

function getTopHoldings() {
    global $spexcon, $UID;
    
    $holdings = [];
    
    $tokenTableExists = $spexcon->query("SHOW TABLES LIKE 'user_coin_balances'");
    if($tokenTableExists && $tokenTableExists->num_rows > 0) {
        $query = $spexcon->query("
            SELECT coin_id as coin, total_balance as amount, (total_balance * 1) as value
            FROM user_coin_balances 
            WHERE user_id = $UID AND total_balance > 0 
            ORDER BY total_balance DESC 
            LIMIT 5
        ");
        
        if($query) {
            while($row = $query->fetch_assoc()) {
                $holdings[] = $row;
            }
        }
    }
    
    return $holdings;
}
?>