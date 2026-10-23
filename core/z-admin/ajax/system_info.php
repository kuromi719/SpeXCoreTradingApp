<?php
require __DIR__ . '/../../system/spex_v4_func.php';

header('Content-Type: application/json');

if (!isset($_SESSION['adminlog']) || $_SESSION['adminlog'] !== 'appr') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    switch($action) {
        case 'getSystemInfo':
            getSystemInfo();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getSystemInfo() {
    global $spexcon;
    
    $response = [
        'success' => true,
        'database_stats' => getDatabaseStats(),
        'user_stats' => getUserStats(),
        'trading_stats' => getTradingStats(),
        'bot_stats' => getBotStats(),
        'server_info' => getServerInfo(),
        'system_settings' => getSystemSettings(),
        'table_info' => getTableInfo()
    ];
    
    echo json_encode($response);
}

function getDatabaseStats() {
    global $spexcon;
    
    $databaseName = '';
    $query = $spexcon->query("SELECT DATABASE() as db_name");
    if ($query) {
        $result = $query->fetch_assoc();
        $databaseName = $result['db_name'];
    }
    
    $tablesQuery = $spexcon->query("SHOW TABLES");
    $totalTables = $tablesQuery ? $tablesQuery->num_rows : 0;
    
    $sizeQuery = $spexcon->query("
        SELECT 
            ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS total_size_mb,
            ROUND(SUM(data_length) / 1024 / 1024, 1) AS data_size_mb,
            ROUND(SUM(index_length) / 1024 / 1024, 1) AS index_size_mb
        FROM information_schema.tables 
        WHERE table_schema = DATABASE()
    ");
    
    $sizeData = ['total_size_mb' => 0, 'data_size_mb' => 0, 'index_size_mb' => 0];
    if ($sizeQuery) {
        $sizeData = $sizeQuery->fetch_assoc();
    }
    
    return [
        'database_name' => $databaseName,
        'total_tables' => $totalTables,
        'total_size' => $sizeData['total_size_mb'] . ' MB',
        'data_size' => $sizeData['data_size_mb'] . ' MB',
        'index_size' => $sizeData['index_size_mb'] . ' MB',
        'mysql_version' => $spexcon->server_info
    ];
}

function getUserStats() {
    global $spexcon;
    
    $totalUsersQuery = $spexcon->query("SELECT COUNT(*) as count FROM users");
    $totalUsers = $totalUsersQuery ? $totalUsersQuery->fetch_assoc()['count'] : 0;
    
    $activeUsersQuery = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE disablead = 0");
    $activeUsers = $activeUsersQuery ? $activeUsersQuery->fetch_assoc()['count'] : 0;
    
    $blockedUsersQuery = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE disablead = 1");
    $blockedUsers = $blockedUsersQuery ? $blockedUsersQuery->fetch_assoc()['count'] : 0;
    
    $verifiedUsersQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_identifications WHERE status = 'APPROVED'");
    $verifiedUsers = $verifiedUsersQuery ? $verifiedUsersQuery->fetch_assoc()['count'] : 0;
    
    $totalLoginsQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_login_history");
    $totalLogins = $totalLoginsQuery ? $totalLoginsQuery->fetch_assoc()['count'] : 0;
    
    $failedLoginsQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_login_history WHERE status = 'FAILED'");
    $failedLogins = $failedLoginsQuery ? $failedLoginsQuery->fetch_assoc()['count'] : 0;
    
    return [
        'total_users' => $totalUsers,
        'active_users' => $activeUsers,
        'blocked_users' => $blockedUsers,
        'verified_users' => $verifiedUsers,
        'total_logins' => $totalLogins,
        'failed_logins' => $failedLogins
    ];
}

function getTradingStats() {
    global $spexcon;
    
    $totalCoinsQuery = $spexcon->query("SELECT COUNT(*) as count FROM coins");
    $totalCoins = $totalCoinsQuery ? $totalCoinsQuery->fetch_assoc()['count'] : 0;
    
    $openPositionsQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_futures_positions WHERE status = 'OPEN'");
    $openPositions = $openPositionsQuery ? $openPositionsQuery->fetch_assoc()['count'] : 0;
    
    $closedPositionsQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_futures_positions WHERE status = 'CLOSED'");
    $closedPositions = $closedPositionsQuery ? $closedPositionsQuery->fetch_assoc()['count'] : 0;
    
    $volumeQuery = $spexcon->query("SELECT SUM(margin_amount) as total FROM user_futures_positions");
    $totalVolume = $volumeQuery ? $volumeQuery->fetch_assoc()['total'] ?? 0 : 0;
    
    $activeStakingQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_staking_positions WHERE status = 'ACTIVE'");
    $activeStaking = $activeStakingQuery ? $activeStakingQuery->fetch_assoc()['count'] : 0;
    
    $stakedValueQuery = $spexcon->query("SELECT SUM(stake_value_usd) as total FROM user_staking_positions WHERE status = 'ACTIVE'");
    $stakedValue = $stakedValueQuery ? $stakedValueQuery->fetch_assoc()['total'] ?? 0 : 0;
    
    return [
        'total_coins' => $totalCoins,
        'open_positions' => $openPositions,
        'closed_positions' => $closedPositions,
        'total_volume' => number_format($totalVolume, 2),
        'active_staking' => $activeStaking,
        'staked_value' => number_format($stakedValue, 2)
    ];
}

function getBotStats() {
    global $spexcon;
    
    $totalBotsQuery = $spexcon->query("SELECT COUNT(*) as count FROM trading_bots");
    $totalBots = $totalBotsQuery ? $totalBotsQuery->fetch_assoc()['count'] : 0;
    
    $activeBotsQuery = $spexcon->query("SELECT COUNT(*) as count FROM trading_bots WHERE is_active = 1");
    $activeBots = $activeBotsQuery ? $activeBotsQuery->fetch_assoc()['count'] : 0;
    
    $totalSubscriptionsQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_bot_subscriptions");
    $totalSubscriptions = $totalSubscriptionsQuery ? $totalSubscriptionsQuery->fetch_assoc()['count'] : 0;
    
    $activeSubscriptionsQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_bot_subscriptions WHERE status = 'ACTIVE'");
    $activeSubscriptions = $activeSubscriptionsQuery ? $activeSubscriptionsQuery->fetch_assoc()['count'] : 0;
    
    $totalEarningsQuery = $spexcon->query("SELECT SUM(earning_amount) as total FROM user_bot_earnings");
    $totalEarnings = $totalEarningsQuery ? $totalEarningsQuery->fetch_assoc()['total'] ?? 0 : 0;
    
    $todayEarningsQuery = $spexcon->query("SELECT SUM(earning_amount) as total FROM user_bot_earnings WHERE DATE(earning_hour) = CURDATE()");
    $todayEarnings = $todayEarningsQuery ? $todayEarningsQuery->fetch_assoc()['total'] ?? 0 : 0;
    
    return [
        'total_bots' => $totalBots,
        'active_bots' => $activeBots,
        'total_subscriptions' => $totalSubscriptions,
        'active_subscriptions' => $activeSubscriptions,
        'total_earnings' => number_format($totalEarnings, 2),
        'today_earnings' => number_format($todayEarnings, 2)
    ];
}

function getServerInfo() {
    return [
        'php_version' => PHP_VERSION,
        'server_os' => php_uname('s') . ' ' . php_uname('r'),
        'memory_usage' => formatBytes(memory_get_usage(true)),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
        'upload_max_filesize' => ini_get('upload_max_filesize')
    ];
}

function getSystemSettings() {
    global $spexcon;
    
    $query = $spexcon->query("SELECT * FROM settings WHERE id = 1");
    $settings = $query ? $query->fetch_assoc() : [];
    
    return $settings;
}

function getTableInfo() {
    global $spexcon;
    
    $query = $spexcon->query("
        SELECT 
            table_name as name,
            table_rows as row_count,
            ROUND(data_length / 1024, 2) as data_size_kb,
            ROUND(index_length / 1024, 2) as index_size_kb,
            ROUND((data_length + index_length) / 1024, 2) as total_size_kb,
            engine,
            table_collation as collation
        FROM information_schema.tables 
        WHERE table_schema = DATABASE()
        ORDER BY (data_length + index_length) DESC
    ");
    
    $tables = [];
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            $tables[] = [
                'name' => $row['name'],
                'rows' => (int)$row['row_count'],
                'data_size' => formatBytes($row['data_size_kb'] * 1024),
                'index_size' => formatBytes($row['index_size_kb'] * 1024),
                'total_size' => formatBytes($row['total_size_kb'] * 1024),
                'engine' => $row['engine'],
                'collation' => $row['collation']
            ];
        }
    }
    
    return $tables;
}

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
?>