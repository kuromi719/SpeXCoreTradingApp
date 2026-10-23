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
        case 'getStats':
            getWithdrawalStats();
            break;
        case 'getWithdrawals':
            getWithdrawals();
            break;
        case 'getWithdrawalDetails':
            getWithdrawalDetails();
            break;
        case 'getUserHistory':
            getUserHistory();
            break;
        case 'processAction':
            processAction();
            break;
        case 'bulkApprove':
            bulkApprove();
            break;
        case 'exportCSV':
            exportWithdrawalsCSV();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getWithdrawalStats() {
    global $spexcon;
    
    // Total withdrawals
    $totalQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_transactions WHERE transaction_type = 'WITHDRAWAL'");
    $totalWithdrawals = $totalQuery ? $totalQuery->fetch_assoc()['count'] : 0;
    
    // Pending withdrawals
    $pendingQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_transactions WHERE transaction_type = 'WITHDRAWAL' AND status = 'PENDING'");
    $pendingWithdrawals = $pendingQuery ? $pendingQuery->fetch_assoc()['count'] : 0;
    
    // Approved withdrawals
    $approvedQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_transactions WHERE transaction_type = 'WITHDRAWAL' AND status = 'COMPLETED'");
    $approvedWithdrawals = $approvedQuery ? $approvedQuery->fetch_assoc()['count'] : 0;
    
    // Total volume
    $volumeQuery = $spexcon->query("SELECT SUM(total_value_usd) as total FROM user_transactions WHERE transaction_type = 'WITHDRAWAL' AND status = 'COMPLETED'");
    $totalVolume = $volumeQuery ? ($volumeQuery->fetch_assoc()['total'] ?? 0) : 0;
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_withdrawals' => (int)$totalWithdrawals,
            'pending_withdrawals' => (int)$pendingWithdrawals,
            'approved_withdrawals' => (int)$approvedWithdrawals,
            'total_volume' => (float)$totalVolume
        ]
    ]);
}

function getWithdrawals() {
    global $spexcon;
    
    $search = Sanitize($_POST['search'] ?? '');
    $status = Sanitize($_POST['status'] ?? '');
    $gateway = Sanitize($_POST['gateway'] ?? '');
    $date = Sanitize($_POST['date'] ?? '');
    $amount = Sanitize($_POST['amount'] ?? '');
    
    $sql = "
        SELECT 
            ut.*,
            u.mail as user_email
        FROM user_transactions ut
        LEFT JOIN users u ON ut.user_id = u.uid
        WHERE ut.transaction_type = 'WITHDRAWAL'
    ";
    
    $params = [];
    $types = "";
    
    if (!empty($search)) {
        $sql .= " AND (u.mail LIKE ? OR ut.user_id LIKE ? OR ut.id LIKE ?)";
        $searchParam = "%$search%";
        $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        $types .= "sss";
    }
    
    if (!empty($status)) {
        if ($status === 'pending') {
            $sql .= " AND ut.status = 'PENDING'";
        } elseif ($status === 'approved') {
            $sql .= " AND ut.status = 'COMPLETED'";
        } elseif ($status === 'rejected') {
            $sql .= " AND ut.status = 'FAILED'";
        }
    }
    
    if (!empty($gateway)) {
        $sql .= " AND ut.notes LIKE ?";
        $params[] = "%$gateway%";
        $types .= "s";
    }
    
    if (!empty($date)) {
        $sql .= " AND DATE(ut.created_at) = ?";
        $params[] = $date;
        $types .= "s";
    }
    
    if (!empty($amount)) {
        switch($amount) {
            case '0-100':
                $sql .= " AND ut.total_value_usd BETWEEN 0 AND 100";
                break;
            case '100-1000':
                $sql .= " AND ut.total_value_usd BETWEEN 100 AND 1000";
                break;
            case '1000-10000':
                $sql .= " AND ut.total_value_usd BETWEEN 1000 AND 10000";
                break;
            case '10000+':
                $sql .= " AND ut.total_value_usd > 10000";
                break;
        }
    }
    
    $sql .= " ORDER BY ut.created_at DESC";
    
    if (!empty($params)) {
        $stmt = $spexcon->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $spexcon->query($sql);
    }
    
    $withdrawals = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Map status
            if ($row['status'] === 'PENDING') {
                $row['status'] = 'pending';
            } elseif ($row['status'] === 'COMPLETED') {
                $row['status'] = 'approved';
            } elseif ($row['status'] === 'FAILED') {
                $row['status'] = 'rejected';
            }
            
            $row['amount'] = $row['total_value_usd'];
            $row['gateway'] = $row['notes']; // Gateway info is in notes
            $withdrawals[] = $row;
        }
    }
    
    echo json_encode(['success' => true, 'withdrawals' => $withdrawals]);
}

function getWithdrawalDetails() {
    global $spexcon;
    
    $withdrawalId = (int)$_POST['withdrawal_id'];
    if (!$withdrawalId) {
        throw new Exception("Withdrawal ID is required");
    }
    
    $stmt = $spexcon->prepare("
        SELECT 
            ut.*,
            u.mail as user_email
        FROM user_transactions ut
        LEFT JOIN users u ON ut.user_id = u.uid
        WHERE ut.id = ? AND ut.transaction_type = 'WITHDRAWAL'
    ");
    $stmt->bind_param("i", $withdrawalId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Withdrawal not found");
    }
    
    $withdrawal = $result->fetch_assoc();
    
    // Map status
    if ($withdrawal['status'] === 'PENDING') {
        $withdrawal['status'] = 'pending';
    } elseif ($withdrawal['status'] === 'COMPLETED') {
        $withdrawal['status'] = 'approved';
    } elseif ($withdrawal['status'] === 'FAILED') {
        $withdrawal['status'] = 'rejected';
    }
    
    $withdrawal['amount'] = $withdrawal['total_value_usd'];
    $withdrawal['gateway'] = $withdrawal['notes'];
    $withdrawal['admin_notes'] = null; // Will be updated when processed
    
    echo json_encode(['success' => true, 'withdrawal' => $withdrawal]);
}

function getUserHistory() {
    global $spexcon;
    
    $userId = (int)$_POST['user_id'];
    if (!$userId) {
        throw new Exception("User ID is required");
    }
    
    // Get user info
    $userStmt = $spexcon->prepare("SELECT uid, mail, balance FROM users WHERE uid = ?");
    $userStmt->bind_param("i", $userId);
    $userStmt->execute();
    $user = $userStmt->get_result()->fetch_assoc();
    
    if (!$user) {
        throw new Exception("User not found");
    }
    
    // Get withdrawals
    $withdrawalsStmt = $spexcon->prepare("
        SELECT * FROM user_transactions 
        WHERE user_id = ? AND transaction_type = 'WITHDRAWAL'
        ORDER BY created_at DESC
    ");
    $withdrawalsStmt->bind_param("i", $userId);
    $withdrawalsStmt->execute();
    $result = $withdrawalsStmt->get_result();
    
    $withdrawals = [];
    while ($row = $result->fetch_assoc()) {
        // Map status
        if ($row['status'] === 'PENDING') {
            $row['status'] = 'pending';
        } elseif ($row['status'] === 'COMPLETED') {
            $row['status'] = 'approved';
        } elseif ($row['status'] === 'FAILED') {
            $row['status'] = 'rejected';
        }
        
        $row['amount'] = $row['total_value_usd'];
        $row['gateway'] = $row['notes'];
        $withdrawals[] = $row;
    }
    
    echo json_encode(['success' => true, 'user' => $user, 'withdrawals' => $withdrawals]);
}

function processAction() {
    global $spexcon;
    
    $withdrawalId = (int)$_POST['withdrawal_id'];
    $actionType = Sanitize($_POST['action_type']);
    $adminNotes = Sanitize($_POST['admin_notes'] ?? '');
    $rejectionReason = Sanitize($_POST['rejection_reason'] ?? '');
    $transactionHash = Sanitize($_POST['transaction_hash'] ?? '');
    
    if (!$withdrawalId || !in_array($actionType, ['approve', 'reject'])) {
        throw new Exception("Invalid parameters");
    }
    
    // Get withdrawal details
    $withdrawalStmt = $spexcon->prepare("
        SELECT ut.*, u.mail as user_email 
        FROM user_transactions ut 
        LEFT JOIN users u ON ut.user_id = u.uid 
        WHERE ut.id = ? AND ut.transaction_type = 'WITHDRAWAL' AND ut.status = 'PENDING'
    ");
    $withdrawalStmt->bind_param("i", $withdrawalId);
    $withdrawalStmt->execute();
    $withdrawal = $withdrawalStmt->get_result()->fetch_assoc();
    
    if (!$withdrawal) {
        throw new Exception("Withdrawal not found or already processed");
    }
    
    $spexcon->begin_transaction();
    
    try {
        if ($actionType === 'approve') {
            // Update withdrawal status
            $updateStmt = $spexcon->prepare("
                UPDATE user_transactions 
                SET status = 'COMPLETED', notes = CONCAT(notes, ' - APPROVED: ', ?), transaction_hash = ? 
                WHERE id = ?
            ");
            $updateStmt->bind_param("ssi", $adminNotes, $transactionHash, $withdrawalId);
            $updateStmt->execute();
            
            // Send push notification
            PushSend($withdrawal['user_id'], 'Withdrawal Approved', 'Your withdrawal of $' . number_format($withdrawal['total_value_usd'], 2) . ' has been approved and processed.', 'withdrawal');
            
            $message = 'Withdrawal approved successfully';
            
        } else { // reject
            // Update withdrawal status
            $notes = $rejectionReason . ($adminNotes ? ' - ' . $adminNotes : '');
            $updateStmt = $spexcon->prepare("
                UPDATE user_transactions 
                SET status = 'FAILED', notes = CONCAT(notes, ' - REJECTED: ', ?) 
                WHERE id = ?
            ");
            $updateStmt->bind_param("si", $notes, $withdrawalId);
            $updateStmt->execute();
            
            // Add money back to user balance
            $balanceStmt = $spexcon->prepare("UPDATE users SET balance = balance + ? WHERE uid = ?");
            $balanceStmt->blind_param("di", $withdrawal['total_value_usd'], $withdrawal['user_id']);
            $balanceStmt->execute();
            
            // Send push notification
            $rejectionMsg = $rejectionReason ? " Reason: " . $rejectionReason : "";
            PushSend($withdrawal['user_id'], 'Withdrawal Rejected', 'Your withdrawal of $' . number_format($withdrawal['total_value_usd'], 2) . ' has been rejected and the amount has been refunded to your account.' . $rejectionMsg, 'withdrawal');
            
            $message = 'Withdrawal rejected successfully and amount refunded';
        }
        
        $spexcon->commit();
        echo json_encode(['success' => true, 'message' => $message]);
        
    } catch (Exception $e) {
        $spexcon->rollback();
        throw new Exception("Failed to process action: " . $e->getMessage());
    }
}

function bulkApprove() {
    global $spexcon;
    
    $withdrawalIds = $_POST['withdrawal_ids'] ?? [];
    if (empty($withdrawalIds)) {
        throw new Exception("No withdrawals selected");
    }
    
    $processed = 0;
    $spexcon->begin_transaction();
    
    try {
        foreach ($withdrawalIds as $withdrawalId) {
            $withdrawalId = (int)$withdrawalId;
            
            // Get withdrawal details
            $withdrawalStmt = $spexcon->prepare("
                SELECT * FROM user_transactions 
                WHERE id = ? AND transaction_type = 'WITHDRAWAL' AND status = 'PENDING'
            ");
            $withdrawalStmt->bind_param("i", $withdrawalId);
            $withdrawalStmt->execute();
            $withdrawal = $withdrawalStmt->get_result()->fetch_assoc();
            
            if ($withdrawal) {
                // Update withdrawal status
                $updateStmt = $spexcon->prepare("
                    UPDATE user_transactions 
                    SET status = 'COMPLETED', notes = CONCAT(notes, ' - BULK APPROVED by admin') 
                    WHERE id = ?
                ");
                $updateStmt->bind_param("i", $withdrawalId);
                $updateStmt->execute();
                
                // Send push notification
                PushSend($withdrawal['user_id'], 'Withdrawal Approved', 'Your withdrawal of $' . number_format($withdrawal['total_value_usd'], 2) . ' has been approved and processed.', 'withdrawal');
                
                $processed++;
            }
        }
        
        $spexcon->commit();
        echo json_encode(['success' => true, 'processed' => $processed, 'message' => "$processed withdrawals approved successfully"]);
        
    } catch (Exception $e) {
        $spexcon->rollback();
        throw new Exception("Failed to process bulk approval: " . $e->getMessage());
    }
}

function exportWithdrawalsCSV() {
    global $spexcon;
    
    $search = Sanitize($_GET['search'] ?? '');
    $status = Sanitize($_GET['status'] ?? '');
    $gateway = Sanitize($_GET['gateway'] ?? '');
    $date = Sanitize($_GET['date'] ?? '');
    $amount = Sanitize($_GET['amount'] ?? '');
    
    $sql = "
        SELECT 
            ut.*,
            u.mail as user_email
        FROM user_transactions ut
        LEFT JOIN users u ON ut.user_id = u.uid
        WHERE ut.transaction_type = 'WITHDRAWAL'
    ";
    
    $params = [];
    $types = "";
    
    if (!empty($search)) {
        $sql .= " AND (u.mail LIKE ? OR ut.user_id LIKE ? OR ut.id LIKE ?)";
        $searchParam = "%$search%";
        $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        $types .= "sss";
    }
    
    if (!empty($status)) {
        if ($status === 'pending') {
            $sql .= " AND ut.status = 'PENDING'";
        } elseif ($status === 'approved') {
            $sql .= " AND ut.status = 'COMPLETED'";
        } elseif ($status === 'rejected') {
            $sql .= " AND ut.status = 'FAILED'";
        }
    }
    
    if (!empty($gateway)) {
        $sql .= " AND ut.notes LIKE ?";
        $params[] = "%$gateway%";
        $types .= "s";
    }
    
    if (!empty($date)) {
        $sql .= " AND DATE(ut.created_at) = ?";
        $params[] = $date;
        $types .= "s";
    }
    
    $sql .= " ORDER BY ut.created_at DESC";
    
    if (!empty($params)) {
        $stmt = $spexcon->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $spexcon->query($sql);
    }
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="withdrawals_export_' . date('Y-m-d_H-i-s') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    fputcsv($output, [
        'Transaction ID', 'User ID', 'Email', 'Amount USD', 'Coin ID', 'Status', 'To Address', 'Gateway', 'Transaction Hash', 'Date'
    ]);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['id'],
                $row['user_id'],
                $row['user_email'] ?: 'Not Available',
                $row['total_value_usd'],
                $row['coin_id'],
                $row['status'],
                $row['to_address'] ?: 'N/A',
                $row['notes'] ?: 'N/A',
                $row['transaction_hash'] ?: 'N/A',
                $row['created_at']
            ]);
        }
    }
    
    fclose($output);
    exit;
}
?>