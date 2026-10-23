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
        case 'getAnalytics':
            getAnalytics();
            break;
        case 'getUserDetails':
            getUserDetails();
            break;
        case 'getUserActivities':
            getUserActivities();
            break;
        case 'exportAnalytics':
            exportAnalytics();
            break;
        case 'exportUserData':
            exportUserData();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getAnalytics() {
    global $spexcon;
    
    $timeFilter = $_POST['time'] ?? '30d';
    $userType = $_POST['userType'] ?? '';
    $metric = $_POST['metric'] ?? 'overview';
    $dateFrom = $_POST['dateFrom'] ?? '';
    $dateTo = $_POST['dateTo'] ?? '';
    
    // Build date condition
    $dateCondition = buildDateCondition($timeFilter, $dateFrom, $dateTo);
    $userCondition = buildUserCondition($userType);
    
    // Get basic stats
    $stats = getBasicStats($dateCondition, $userCondition);
    
    // Get chart data
    $charts = getChartsData($dateCondition, $userCondition, $timeFilter);
    
    // Get user activity
    $userActivity = getUserActivity($dateCondition, $userCondition);
    
    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'charts' => $charts,
        'user_activity' => $userActivity
    ]);
}

function buildDateCondition($timeFilter, $dateFrom, $dateTo) {
    global $spexcon;
    
    if ($dateFrom && $dateTo) {
        return "AND DATE(register_date) BETWEEN '" . $spexcon->real_escape_string($dateFrom) . "' AND '" . $spexcon->real_escape_string($dateTo) . "'";
    }
    
    switch($timeFilter) {
        case '24h':
            return "AND register_date >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
        case '7d':
            return "AND register_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        case '30d':
            return "AND register_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        case '90d':
            return "AND register_date >= DATE_SUB(NOW(), INTERVAL 90 DAY)";
        case '1y':
            return "AND register_date >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
        default:
            return "";
    }
}

function buildUserCondition($userType) {
    switch($userType) {
        case 'active':
            return "AND disablead = 0";
        case 'disabled':
            return "AND disablead = 1";
        case 'verified':
            return "AND identification_verify = 1";
        case 'unverified':
            return "AND identification_verify = 0";
        default:
            return "";
    }
}

function getBasicStats($dateCondition, $userCondition) {
    global $spexcon;
    
    // Total users
    $totalUsersQuery = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE 1=1 $userCondition $dateCondition");
    $totalUsers = $totalUsersQuery ? $totalUsersQuery->fetch_assoc()['count'] : 0;
    
    // Active users
    $activeUsersQuery = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE disablead = 0 $dateCondition");
    $activeUsers = $activeUsersQuery ? $activeUsersQuery->fetch_assoc()['count'] : 0;
    
    // New registrations (today)
    $newRegQuery = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE DATE(register_date) = CURDATE() $userCondition");
    $newRegistrations = $newRegQuery ? $newRegQuery->fetch_assoc()['count'] : 0;
    
    // Verified users
    $verifiedQuery = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE identification_verify = 1 $dateCondition $userCondition");
    $verifiedUsers = $verifiedQuery ? $verifiedQuery->fetch_assoc()['count'] : 0;
    $verifiedPercentage = $totalUsers > 0 ? round(($verifiedUsers / $totalUsers) * 100, 1) : 0;
    
    // Login stats
    $successfulLoginsQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_login_history WHERE status = 'SUCCESS'");
    $successfulLogins = $successfulLoginsQuery ? $successfulLoginsQuery->fetch_assoc()['count'] : 0;
    
    $failedLoginsQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_login_history WHERE status = 'FAILED' ");
    $failedLogins = $failedLoginsQuery ? $failedLoginsQuery->fetch_assoc()['count'] : 0;
    
    // Balance stats
    $usersWithBalanceQuery = $spexcon->query("SELECT COUNT(DISTINCT user_id) as count FROM user_coin_balances WHERE total_balance > 0");
    $usersWithBalance = $usersWithBalanceQuery ? $usersWithBalanceQuery->fetch_assoc()['count'] : 0;
    
    $totalValueQuery = $spexcon->query("SELECT SUM(total_invested_usd) as total FROM user_coin_balances WHERE total_balance > 0");
    $totalValueUSD = $totalValueQuery ? ($totalValueQuery->fetch_assoc()['total'] ?? 0) : 0;
    
    // Calculate changes (previous period comparison)
    $changes = calculateChanges($dateCondition, $userCondition);
    
    return [
        'total_users' => $totalUsers,
        'total_users_change' => $changes['total_users'],
        'active_users' => $activeUsers,
        'active_users_change' => $changes['active_users'],
        'new_registrations' => $newRegistrations,
        'new_registrations_change' => $changes['new_registrations'],
        'verified_users' => $verifiedUsers,
        'verified_percentage' => $verifiedPercentage,
        'successful_logins' => $successfulLogins,
        'failed_logins' => $failedLogins,
        'users_with_balance' => $usersWithBalance,
        'total_value_usd' => number_format($totalValueUSD, 2)
    ];
}

function calculateChanges($dateCondition, $userCondition) {
    global $spexcon;
    
    // Previous day data for comparison
    $prevTotalQuery = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE DATE(register_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) $userCondition");
    $prevTotal = $prevTotalQuery ? $prevTotalQuery->fetch_assoc()['count'] : 0;
    
    $prevActiveQuery = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE disablead = 0 AND DATE(register_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)");
    $prevActive = $prevActiveQuery ? $prevActiveQuery->fetch_assoc()['count'] : 0;
    
    $prevNewRegQuery = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE DATE(register_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) $userCondition");
    $prevNewReg = $prevNewRegQuery ? $prevNewRegQuery->fetch_assoc()['count'] : 0;
    
    return [
        'total_users' => $prevTotal > 0 ? ($prevTotal > 0 ? "+$prevTotal yesterday" : "No change") : "No change",
        'active_users' => $prevActive > 0 ? "+$prevActive yesterday" : "No change",
        'new_registrations' => $prevNewReg > 0 ? "+$prevNewReg yesterday" : "No change"
    ];
}

function getChartsData($dateCondition, $userCondition, $timeFilter) {
    global $spexcon;
    
    // Registration trend
    $registrationTrend = getRegistrationTrend($timeFilter, $userCondition);
    
    // User status distribution
    $userStatus = getUserStatusDistribution($userCondition);
    
    // Login activity
    $loginActivity = getLoginActivity($timeFilter);
    
    // Balance distribution
    $balanceDistribution = getBalanceDistribution();
    
    return [
        'registration_trend' => $registrationTrend,
        'user_status' => $userStatus,
        'login_activity' => $loginActivity,
        'balance_distribution' => $balanceDistribution
    ];
}

function getRegistrationTrend($timeFilter, $userCondition) {
    global $spexcon;
    
    $groupBy = "DATE(register_date)";
    $interval = "30 DAY";
    
    switch($timeFilter) {
        case '24h':
            $groupBy = "HOUR(register_date)";
            $interval = "1 DAY";
            break;
        case '7d':
            $interval = "7 DAY";
            break;
        case '90d':
            $interval = "90 DAY";
            break;
        case '1y':
            $groupBy = "DATE_FORMAT(register_date, '%Y-%m')";
            $interval = "1 YEAR";
            break;
    }
    
    $query = $spexcon->query("
        SELECT $groupBy as period, COUNT(*) as count 
        FROM users 
        WHERE register_date >= DATE_SUB(NOW(), INTERVAL $interval) $userCondition
        GROUP BY $groupBy 
        ORDER BY period ASC
    ");
    
    $data = [];
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            if ($timeFilter === '24h') {
                $timestamp = strtotime(date('Y-m-d') . ' ' . $row['period'] . ':00:00') * 1000;
            } else {
                $timestamp = strtotime($row['period']) * 1000;
            }
            $data[] = [$timestamp, intval($row['count'])];
        }
    }
    
    return $data;
}

function getUserStatusDistribution($userCondition) {
    global $spexcon;
    
    $activeQuery = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE disablead = 0 $userCondition");
    $active = $activeQuery ? intval($activeQuery->fetch_assoc()['count']) : 0;
    
    $disabledQuery = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE disablead = 1 $userCondition");
    $disabled = $disabledQuery ? intval($disabledQuery->fetch_assoc()['count']) : 0;
    
    $verifiedQuery = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE identification_verify = 1 $userCondition");
    $verified = $verifiedQuery ? intval($verifiedQuery->fetch_assoc()['count']) : 0;
    
    $pendingQuery = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE identification_verify = 0 $userCondition");
    $pending = $pendingQuery ? intval($pendingQuery->fetch_assoc()['count']) : 0;
    
    return [
        'values' => [$active, $disabled, $verified, $pending],
        'labels' => ['Active', 'Disabled', 'Verified', 'Unverified']
    ];
}

function getLoginActivity($timeFilter) {
    global $spexcon;
    
    $interval = "7 DAY";
    switch($timeFilter) {
        case '24h':
            $interval = "1 DAY";
            break;
        case '30d':
            $interval = "30 DAY";
            break;
        case '90d':
            $interval = "90 DAY";
            break;
    }
    
    // Direct query for successful logins
    $successQuery = $spexcon->query("
        SELECT DATE(login_time) as date, COUNT(*) as count 
        FROM user_login_history 
        WHERE status = 'SUCCESS' AND login_time >= DATE_SUB(NOW(), INTERVAL $interval)
        GROUP BY DATE(login_time) 
        ORDER BY date ASC
    ");
    
    // Direct query for failed logins
    $failedQuery = $spexcon->query("
        SELECT DATE(login_time) as date, COUNT(*) as count 
        FROM user_login_history 
        WHERE status = 'FAILED' AND login_time >= DATE_SUB(NOW(), INTERVAL $interval)
        GROUP BY DATE(login_time) 
        ORDER BY date ASC
    ");
    
    $successful = [];
    $failed = [];
    
    if ($successQuery) {
        while ($row = $successQuery->fetch_assoc()) {
            $timestamp = strtotime($row['date']) * 1000;
            $successful[] = [$timestamp, intval($row['count'])];
        }
    }
    
    if ($failedQuery) {
        while ($row = $failedQuery->fetch_assoc()) {
            $timestamp = strtotime($row['date']) * 1000;
            $failed[] = [$timestamp, intval($row['count'])];
        }
    }
    
    return [
        'successful' => $successful,
        'failed' => $failed
    ];
}

function getBalanceDistribution() {
    global $spexcon;
    
    $ranges = [
        ['min' => 0, 'max' => 10],
        ['min' => 10, 'max' => 100],
        ['min' => 100, 'max' => 1000],
        ['min' => 1000, 'max' => 10000],
        ['min' => 10000, 'max' => 999999999]
    ];
    
    $distribution = [];
    
    foreach ($ranges as $range) {
        $query = $spexcon->query("
            SELECT COUNT(DISTINCT user_id) as count 
            FROM user_coin_balances 
            WHERE total_invested_usd >= {$range['min']} AND total_invested_usd < {$range['max']}
        ");
        
        $count = $query ? $query->fetch_assoc()['count'] : 0;
        $distribution[] = intval($count);
    }
    
    return $distribution;
}

function getUserActivity($dateCondition, $userCondition) {
    global $spexcon;
    
    $query = $spexcon->query("
        SELECT u.uid, u.mail, u.phone, u.register_date, u.disablead, u.last_login,
               ui.status as kyc_status,
               COALESCE(SUM(ucb.total_invested_usd), 0) as balance_value,
               (SELECT COUNT(*) FROM user_futures_positions ufp WHERE ufp.user_id = u.uid) as total_trades
        FROM users u
        LEFT JOIN user_identifications ui ON u.uid = ui.user_id
        LEFT JOIN user_coin_balances ucb ON u.uid = ucb.user_id
        WHERE 1=1 $userCondition $dateCondition
        GROUP BY u.uid
        ORDER BY u.register_date DESC
        LIMIT 50
    ");
    
    $users = [];
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            $users[] = $row;
        }
    }
    
    return $users;
}

function getUserDetails() {
    global $spexcon;
    
    $userId = (int)$_POST['user_id'];
    if (!$userId) {
        throw new Exception("User ID is required");
    }
    
    // Get user info
    $userQuery = $spexcon->query("SELECT * FROM users WHERE uid = $userId");
    if (!$userQuery || $userQuery->num_rows === 0) {
        throw new Exception("User not found");
    }
    $user = $userQuery->fetch_assoc();
    
    // Get user stats
    $stats = [];
    
    // Total logins
    $loginQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_login_history WHERE user_id = $userId");
    $stats['total_logins'] = $loginQuery ? $loginQuery->fetch_assoc()['count'] : 0;
    
    // Total balances
    $balanceQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_coin_balances WHERE user_id = $userId AND total_balance > 0");
    $stats['total_balances'] = $balanceQuery ? $balanceQuery->fetch_assoc()['count'] : 0;
    
    // Total trades
    $tradesQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_futures_positions WHERE user_id = $userId");
    $stats['total_trades'] = $tradesQuery ? $tradesQuery->fetch_assoc()['count'] : 0;
    
    // KYC status
    $kycQuery = $spexcon->query("SELECT status FROM user_identifications WHERE user_id = $userId");
    $stats['kyc_status'] = $kycQuery ? ($kycQuery->fetch_assoc()['status'] ?? 'Not submitted') : 'Not submitted';
    
    // 2FA status
    $securityQuery = $spexcon->query("SELECT two_factor_enabled FROM user_security_settings WHERE user_id = $userId");
    $stats['two_factor_enabled'] = $securityQuery ? ($securityQuery->fetch_assoc()['two_factor_enabled'] == 1) : false;
    
    // Referrals (using users table - assuming there's a ref column)
    $referralQuery = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE uid != $userId LIMIT 1");
    $stats['referrals'] = 0; // Set to 0 as ref column structure is not clear
    
    echo json_encode([
        'success' => true,
        'user' => $user,
        'stats' => $stats
    ]);
}

function getUserActivities() {
    global $spexcon;
    
    $userId = (int)$_POST['user_id'];
    if (!$userId) {
        throw new Exception("User ID is required");
    }
    
    $query = $spexcon->query("
        SELECT type, msg, amount, date 
        FROM user_logs 
        WHERE uid = '$userId' 
        ORDER BY date DESC 
        LIMIT 20
    ");
    
    $activities = [];
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            $activities[] = $row;
        }
    }
    
    echo json_encode([
        'success' => true,
        'activities' => $activities
    ]);
}

function exportAnalytics() {
    global $spexcon;
    
    $format = $_GET['format'] ?? 'csv';
    $timeFilter = $_GET['time'] ?? '30d';
    $userType = $_GET['userType'] ?? '';
    
    $dateCondition = buildDateCondition($timeFilter, $_GET['dateFrom'] ?? '', $_GET['dateTo'] ?? '');
    $userCondition = buildUserCondition($userType);
    
    if ($format === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="user_analytics_' . date('Y-m-d_H-i-s') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'User ID', 'Email', 'Phone', 'Registration Date', 'Last Login', 
            'Status', 'KYC Status', 'Balance Value', 'Total Trades', 'Total Logins'
        ]);
        
        // Get data
        $query = $spexcon->query("
            SELECT u.uid, u.mail, u.phone, u.register_date, u.disablead, u.last_login,
                   ui.status as kyc_status,
                   COALESCE(SUM(ucb.total_invested_usd), 0) as balance_value,
                   (SELECT COUNT(*) FROM user_futures_positions ufp WHERE ufp.user_id = u.uid) as total_trades,
                   (SELECT COUNT(*) FROM user_login_history ulh2 WHERE ulh2.user_id = u.uid) as total_logins
            FROM users u
            LEFT JOIN user_identifications ui ON u.uid = ui.user_id
            LEFT JOIN user_coin_balances ucb ON u.uid = ucb.user_id
            WHERE 1=1 $userCondition $dateCondition
            GROUP BY u.uid
            ORDER BY u.register_date DESC
        ");
        
        if ($query) {
            while ($row = $query->fetch_assoc()) {
                fputcsv($output, [
                    $row['uid'],
                    $row['mail'],
                    $row['phone'] ?? '',
                    $row['register_date'],
                    $row['last_login'] ?? 'Never',
                    $row['disablead'] == 0 ? 'Active' : 'Disabled',
                    $row['kyc_status'] ?? 'Not submitted',
                    '$' . number_format($row['balance_value'], 2),
                    $row['total_trades'],
                    $row['total_logins']
                ]);
            }
        }
        
        fclose($output);
        exit;
    }
}

function exportUserData() {
    global $spexcon;
    
    $userId = (int)$_GET['user_id'];
    if (!$userId) {
        throw new Exception("User ID is required");
    }
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="user_' . $userId . '_data_' . date('Y-m-d_H-i-s') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // User info
    fputcsv($output, ['USER INFORMATION']);
    fputcsv($output, ['Field', 'Value']);
    
    $userQuery = $spexcon->query("SELECT * FROM users WHERE uid = $userId");
    if ($userQuery) {
        $user = $userQuery->fetch_assoc();
        foreach ($user as $field => $value) {
            fputcsv($output, [$field, $value]);
        }
    }
    
    fputcsv($output, []);
    
    // Login history
    fputcsv($output, ['LOGIN HISTORY']);
    fputcsv($output, ['Date', 'IP Address', 'Device', 'Browser', 'Status']);
    
    $loginQuery = $spexcon->query("SELECT * FROM user_login_history WHERE user_id = $userId ORDER BY login_time DESC LIMIT 50");
    if ($loginQuery) {
        while ($row = $loginQuery->fetch_assoc()) {
            fputcsv($output, [
                $row['login_time'],
                $row['ip_address'],
                $row['device'],
                $row['browser'],
                $row['status']
            ]);
        }
    }
    
    fputcsv($output, []);
    
    // Balances
    fputcsv($output, ['BALANCES']);
    fputcsv($output, ['Coin', 'Total Balance', 'Available', 'Locked', 'USD Value']);
    
    $balanceQuery = $spexcon->query("SELECT * FROM user_coin_balances WHERE user_id = $userId");
    if ($balanceQuery) {
        while ($row = $balanceQuery->fetch_assoc()) {
            fputcsv($output, [
                $row['coin_id'],
                $row['total_balance'],
                $row['available_balance'],
                $row['locked_balance'],
                '$' . number_format($row['total_invested_usd'], 2)
            ]);
        }
    }
    
    fputcsv($output, []);
    
    // Activities
    fputcsv($output, ['ACTIVITIES']);
    fputcsv($output, ['Date', 'Type', 'Description', 'Amount']);
    
    $activityQuery = $spexcon->query("SELECT * FROM user_logs WHERE uid = '$userId' ORDER BY date DESC LIMIT 100");
    if ($activityQuery) {
        while ($row = $activityQuery->fetch_assoc()) {
            fputcsv($output, [
                $row['date'],
                $row['type'],
                $row['msg'],
                $row['amount']
            ]);
        }
    }
    
    fclose($output);
    exit;
}
?>