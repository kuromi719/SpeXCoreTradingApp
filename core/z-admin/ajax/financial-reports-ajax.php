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
        case 'getReportData':
            getReportData();
            break;
        case 'exportReport':
            exportReport();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getReportData() {
    global $spexcon;
    
    $dateRange = $_POST['date_range'] ?? '30';
    $transactionType = $_POST['transaction_type'] ?? 'all';
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    
    // Calculate date range
    if ($dateRange === 'custom' && $startDate && $endDate) {
        $start = $startDate;
        $end = $endDate;
    } else {
        $days = (int)$dateRange;
        $start = date('Y-m-d', strtotime("-$days days"));
        $end = date('Y-m-d');
    }
    
    $typeFilter = $transactionType !== 'all' ? "AND transaction_type = '$transactionType'" : '';
    
    // Get summary data
    $summary = getSummaryData($start, $end, $typeFilter);
    
    // Get trend data
    $trends = getTrendData($start, $end, $typeFilter);
    
    // Get status data
    $status = getStatusData($start, $end, $typeFilter);
    
    // Get monthly data
    $monthly = getMonthlyData($start, $end, $typeFilter);
    
    // Get daily data
    $daily = getDailyData($start, $end, $typeFilter);
    
    // Get recent transactions
    $recent = getRecentTransactions($start, $end, $typeFilter);
    
    echo json_encode([
        'success' => true,
        'summary' => $summary,
        'trends' => $trends,
        'status' => $status,
        'monthly' => $monthly,
        'daily' => $daily,
        'recent' => $recent
    ]);
}

function getSummaryData($start, $end, $typeFilter) {
    global $spexcon;
    
    $sql = "SELECT 
                transaction_type,
                status,
                COUNT(*) as count,
                SUM(total_value_usd) as total
            FROM user_transactions 
            WHERE (transaction_type = 'DEPOSIT' OR transaction_type = 'WITHDRAWAL')
            AND DATE(created_at) BETWEEN '$start' AND '$end'
            $typeFilter
            GROUP BY transaction_type, status";
    
    $result = $spexcon->query($sql);
    $data = [
        'total_deposits' => 0,
        'deposit_count' => 0,
        'total_withdrawals' => 0,
        'withdrawal_count' => 0,
        'pending_amount' => 0,
        'pending_count' => 0
    ];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if ($row['transaction_type'] === 'DEPOSIT') {
                $data['total_deposits'] += $row['total'];
                $data['deposit_count'] += $row['count'];
            } else {
                $data['total_withdrawals'] += $row['total'];
                $data['withdrawal_count'] += $row['count'];
            }
            
            if ($row['status'] === 'PENDING') {
                $data['pending_amount'] += $row['total'];
                $data['pending_count'] += $row['count'];
            }
        }
    }
    
    return $data;
}

function getTrendData($start, $end, $typeFilter) {
    global $spexcon;
    
    $sql = "SELECT 
                DATE(created_at) as date,
                transaction_type,
                SUM(total_value_usd) as total
            FROM user_transactions 
            WHERE (transaction_type = 'DEPOSIT' OR transaction_type = 'WITHDRAWAL')
            AND DATE(created_at) BETWEEN '$start' AND '$end'
            $typeFilter
            GROUP BY DATE(created_at), transaction_type
            ORDER BY DATE(created_at)";
    
    $result = $spexcon->query($sql);
    $data = ['labels' => [], 'deposits' => [], 'withdrawals' => []];
    $temp = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if (!isset($temp[$row['date']])) {
                $temp[$row['date']] = ['deposits' => 0, 'withdrawals' => 0];
            }
            
            if ($row['transaction_type'] === 'DEPOSIT') {
                $temp[$row['date']]['deposits'] = $row['total'];
            } else {
                $temp[$row['date']]['withdrawals'] = $row['total'];
            }
        }
    }
    
    foreach ($temp as $date => $values) {
        $data['labels'][] = date('M d', strtotime($date));
        $data['deposits'][] = $values['deposits'];
        $data['withdrawals'][] = $values['withdrawals'];
    }
    
    return $data;
}

function getStatusData($start, $end, $typeFilter) {
    global $spexcon;
    
    $sql = "SELECT 
                status,
                COUNT(*) as count
            FROM user_transactions 
            WHERE (transaction_type = 'DEPOSIT' OR transaction_type = 'WITHDRAWAL')
            AND DATE(created_at) BETWEEN '$start' AND '$end'
            $typeFilter
            GROUP BY status";
    
    $result = $spexcon->query($sql);
    $data = ['completed' => 0, 'pending' => 0, 'failed' => 0];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if ($row['status'] === 'COMPLETED') {
                $data['completed'] = $row['count'];
            } elseif ($row['status'] === 'PENDING') {
                $data['pending'] = $row['count'];
            } elseif ($row['status'] === 'FAILED') {
                $data['failed'] = $row['count'];
            }
        }
    }
    
    return $data;
}

function getMonthlyData($start, $end, $typeFilter) {
    global $spexcon;
    
    $sql = "SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                transaction_type,
                SUM(total_value_usd) as total
            FROM user_transactions 
            WHERE (transaction_type = 'DEPOSIT' OR transaction_type = 'WITHDRAWAL')
            AND DATE(created_at) BETWEEN '$start' AND '$end'
            $typeFilter
            GROUP BY DATE_FORMAT(created_at, '%Y-%m'), transaction_type
            ORDER BY month";
    
    $result = $spexcon->query($sql);
    $data = ['labels' => [], 'deposits' => [], 'withdrawals' => []];
    $temp = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if (!isset($temp[$row['month']])) {
                $temp[$row['month']] = ['deposits' => 0, 'withdrawals' => 0];
            }
            
            if ($row['transaction_type'] === 'DEPOSIT') {
                $temp[$row['month']]['deposits'] = $row['total'];
            } else {
                $temp[$row['month']]['withdrawals'] = $row['total'];
            }
        }
    }
    
    foreach ($temp as $month => $values) {
        $data['labels'][] = date('M Y', strtotime($month . '-01'));
        $data['deposits'][] = $values['deposits'];
        $data['withdrawals'][] = $values['withdrawals'];
    }
    
    return $data;
}

function getDailyData($start, $end, $typeFilter) {
    global $spexcon;
    
    $sql = "SELECT 
                DATE(created_at) as date,
                SUM(total_value_usd) as total
            FROM user_transactions 
            WHERE (transaction_type = 'DEPOSIT' OR transaction_type = 'WITHDRAWAL')
            AND DATE(created_at) BETWEEN '$start' AND '$end'
            $typeFilter
            GROUP BY DATE(created_at)
            ORDER BY DATE(created_at)";
    
    $result = $spexcon->query($sql);
    $data = ['labels' => [], 'volumes' => []];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data['labels'][] = date('M d', strtotime($row['date']));
            $data['volumes'][] = $row['total'];
        }
    }
    
    return $data;
}

function getRecentTransactions($start, $end, $typeFilter) {
    global $spexcon;
    
    $sql = "SELECT 
                ut.*,
                u.mail as user_email
            FROM user_transactions ut
            LEFT JOIN users u ON ut.user_id = u.uid
            WHERE (ut.transaction_type = 'DEPOSIT' OR ut.transaction_type = 'WITHDRAWAL')
            AND DATE(ut.created_at) BETWEEN '$start' AND '$end'
            $typeFilter
            ORDER BY ut.created_at DESC
            LIMIT 20";
    
    $result = $spexcon->query($sql);
    $transactions = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }
    }
    
    return $transactions;
}

function exportReport() {
    global $spexcon;
    
    $dateRange = $_GET['date_range'] ?? '30';
    $transactionType = $_GET['transaction_type'] ?? 'all';
    $startDate = $_GET['start_date'] ?? '';
    $endDate = $_GET['end_date'] ?? '';
    
    // Calculate date range
    if ($dateRange === 'custom' && $startDate && $endDate) {
        $start = $startDate;
        $end = $endDate;
    } else {
        $days = (int)$dateRange;
        $start = date('Y-m-d', strtotime("-$days days"));
        $end = date('Y-m-d');
    }
    
    $typeFilter = $transactionType !== 'all' ? "AND transaction_type = '$transactionType'" : '';
    
    $sql = "SELECT 
                ut.*,
                u.mail as user_email
            FROM user_transactions ut
            LEFT JOIN users u ON ut.user_id = u.uid
            WHERE (ut.transaction_type = 'DEPOSIT' OR ut.transaction_type = 'WITHDRAWAL')
            AND DATE(ut.created_at) BETWEEN '$start' AND '$end'
            $typeFilter
            ORDER BY ut.created_at DESC";
    
    $result = $spexcon->query($sql);
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="financial_report_' . date('Y-m-d_H-i-s') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    fputcsv($output, [
        'Date', 'Type', 'User ID', 'Email', 'Amount USD', 'Status', 'Transaction Hash'
    ]);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['created_at'],
                $row['transaction_type'],
                $row['user_id'],
                $row['user_email'] ?: 'Not Available',
                $row['total_value_usd'],
                $row['status'],
                $row['transaction_hash'] ?: 'N/A'
            ]);
        }
    }
    
    fclose($output);
    exit;
}
?>