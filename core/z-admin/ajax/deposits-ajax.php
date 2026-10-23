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
            getDepositStats();
            break;
        case 'getDeposits':
            getDeposits();
            break;
        case 'getDepositDetails':
            getDepositDetails();
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
            exportDepositsCSV();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getDepositStats() {
    global $spexcon;
    
    // Total deposits
    $totalQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_transactions WHERE transaction_type = 'DEPOSIT'");
    $totalDeposits = $totalQuery ? $totalQuery->fetch_assoc()['count'] : 0;
    
    // Pending deposits
    $pendingQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_transactions WHERE transaction_type = 'DEPOSIT' AND status = 'PENDING'");
    $pendingDeposits = $pendingQuery ? $pendingQuery->fetch_assoc()['count'] : 0;
    
    // Approved deposits
    $approvedQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_transactions WHERE transaction_type = 'DEPOSIT' AND status = 'COMPLETED'");
    $approvedDeposits = $approvedQuery ? $approvedQuery->fetch_assoc()['count'] : 0;
    
    // Total volume
    $volumeQuery = $spexcon->query("SELECT SUM(total_value_usd) as total FROM user_transactions WHERE transaction_type = 'DEPOSIT' AND status = 'COMPLETED'");
    $totalVolume = $volumeQuery ? ($volumeQuery->fetch_assoc()['total'] ?? 0) : 0;
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_deposits' => (int)$totalDeposits,
            'pending_deposits' => (int)$pendingDeposits,
            'approved_deposits' => (int)$approvedDeposits,
            'total_volume' => (float)$totalVolume
        ]
    ]);
}

function getDeposits() {
    global $spexcon;
    
    $search = Sanitize($_POST['search'] ?? '');
    $status = Sanitize($_POST['status'] ?? '');
    $method = Sanitize($_POST['method'] ?? '');
    $date = Sanitize($_POST['date'] ?? '');
    $amount = Sanitize($_POST['amount'] ?? '');
    
    $sql = "
        SELECT 
            ut.*,
            u.mail as user_email
        FROM user_transactions ut
        LEFT JOIN users u ON ut.user_id = u.uid
        WHERE ut.transaction_type = 'DEPOSIT'
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
    
    $deposits = [];
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
            $row['payment_method'] = 'Bank Transfer';
            $row['proof_image'] = null;
            $deposits[] = $row;
        }
    }
    
    echo json_encode(['success' => true, 'deposits' => $deposits]);
}

function getDepositDetails() {
    global $spexcon;
    
    $depositId = (int)$_POST['deposit_id'];
    if (!$depositId) {
        throw new Exception("Deposit ID is required");
    }
    
    $stmt = $spexcon->prepare("
        SELECT 
            ut.*,
            u.mail as user_email
        FROM user_transactions ut
        LEFT JOIN users u ON ut.user_id = u.uid
        WHERE ut.id = ? AND ut.transaction_type = 'DEPOSIT'
    ");
    $stmt->bind_param("i", $depositId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Deposit not found");
    }
    
    $deposit = $result->fetch_assoc();
    
    // Map status
    if ($deposit['status'] === 'PENDING') {
        $deposit['status'] = 'pending';
    } elseif ($deposit['status'] === 'COMPLETED') {
        $deposit['status'] = 'approved';
    } elseif ($deposit['status'] === 'FAILED') {
        $deposit['status'] = 'rejected';
    }
    
    $deposit['amount'] = $deposit['total_value_usd'];
    $deposit['payment_method'] = 'Bank Transfer';
    $deposit['transaction_id'] = $deposit['transaction_hash'];
    $deposit['admin_notes'] = $deposit['notes'];
    
    echo json_encode(['success' => true, 'deposit' => $deposit]);
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
    
    // Get deposits
    $depositsStmt = $spexcon->prepare("
        SELECT * FROM user_transactions 
        WHERE user_id = ? AND transaction_type = 'DEPOSIT'
        ORDER BY created_at DESC
    ");
    $depositsStmt->bind_param("i", $userId);
    $depositsStmt->execute();
    $result = $depositsStmt->get_result();
    
    $deposits = [];
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
        $row['payment_method'] = 'Bank Transfer';
        $row['admin_notes'] = $row['notes'];
        $deposits[] = $row;
    }
    
    echo json_encode(['success' => true, 'user' => $user, 'deposits' => $deposits]);
}

function processAction() {
    global $spexcon;
    
    $depositId = (int)$_POST['deposit_id'];
    $actionType = Sanitize($_POST['action_type']);
    $adminNotes = Sanitize($_POST['admin_notes'] ?? '');
    $rejectionReason = Sanitize($_POST['rejection_reason'] ?? '');
    
    if (!$depositId || !in_array($actionType, ['approve', 'reject'])) {
        throw new Exception("Invalid parameters");
    }
    
    // Get deposit details
    $depositStmt = $spexcon->prepare("
        SELECT ut.*, u.mail as user_email 
        FROM user_transactions ut 
        LEFT JOIN users u ON ut.user_id = u.uid 
        WHERE ut.id = ? AND ut.transaction_type = 'DEPOSIT' AND ut.status = 'PENDING'
    ");
    $depositStmt->bind_param("i", $depositId);
    $depositStmt->execute();
    $deposit = $depositStmt->get_result()->fetch_assoc();
    
    if (!$deposit) {
        throw new Exception("Deposit not found or already processed");
    }
    
    $spexcon->begin_transaction();
    
    try {
        if ($actionType === 'approve') {
            // Update deposit status
            $notes = $adminNotes ? $adminNotes : 'Deposit approved by admin';
            $updateStmt = $spexcon->prepare("
                UPDATE user_transactions 
                SET status = 'COMPLETED', notes = ? 
                WHERE id = ?
            ");
            $updateStmt->bind_param("si", $notes, $depositId);
            $updateStmt->execute();
            
            // Add balance to user
            $balanceStmt = $spexcon->prepare("UPDATE users SET balance = balance + ? WHERE uid = ?");
            $balanceStmt->bind_param("di", $deposit['total_value_usd'], $deposit['user_id']);
            $balanceStmt->execute();
            
            // Send push notification
            PushSend($deposit['user_id'], 'Deposit Approved', 'Your deposit of $' . number_format($deposit['total_value_usd'], 2) . ' has been approved and credited to your account.', 'deposit');
            
            $message = 'Deposit approved successfully';
            
        } else { // reject
            // Update deposit status
            $notes = $rejectionReason . ($adminNotes ? ' - ' . $adminNotes : '');
            $updateStmt = $spexcon->prepare("
                UPDATE user_transactions 
                SET status = 'FAILED', notes = ? 
                WHERE id = ?
            ");
            $updateStmt->bind_param("si", $notes, $depositId);
            $updateStmt->execute();
            
            // Send push notification
            $rejectionMsg = $rejectionReason ? " Reason: " . $rejectionReason : "";
            PushSend($deposit['user_id'], 'Deposit Rejected', 'Your deposit of $' . number_format($deposit['total_value_usd'], 2) . ' has been rejected.' . $rejectionMsg, 'deposit');
            
            $message = 'Deposit rejected successfully';
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
    
    $depositIds = $_POST['deposit_ids'] ?? [];
    if (empty($depositIds)) {
        throw new Exception("No deposits selected");
    }
    
    $processed = 0;
    $spexcon->begin_transaction();
    
    try {
        foreach ($depositIds as $depositId) {
            $depositId = (int)$depositId;
            
            // Get deposit details
            $depositStmt = $spexcon->prepare("
                SELECT * FROM user_transactions 
                WHERE id = ? AND transaction_type = 'DEPOSIT' AND status = 'PENDING'
            ");
            $depositStmt->bind_param("i", $depositId);
            $depositStmt->execute();
            $deposit = $depositStmt->get_result()->fetch_assoc();
            
            if ($deposit) {
                // Update deposit status
                $updateStmt = $spexcon->prepare("
                    UPDATE user_transactions 
                    SET status = 'COMPLETED', notes = 'Bulk approved by admin' 
                    WHERE id = ?
                ");
                $updateStmt->bind_param("i", $depositId);
                $updateStmt->execute();
                
                // Add balance to user
                $balanceStmt = $spexcon->prepare("UPDATE users SET balance = balance + ? WHERE uid = ?");
                $balanceStmt->bind_param("di", $deposit['total_value_usd'], $deposit['user_id']);
                $balanceStmt->execute();
                
                // Send push notification
                PushSend($deposit['user_id'], 'Deposit Approved', 'Your deposit of $' . number_format($deposit['total_value_usd'], 2) . ' has been approved and credited to your account.', 'deposit');
                
                $processed++;
            }
        }
        
        $spexcon->commit();
        echo json_encode(['success' => true, 'processed' => $processed, 'message' => "$processed deposits approved successfully"]);
        
    } catch (Exception $e) {
        $spexcon->rollback();
        throw new Exception("Failed to process bulk approval: " . $e->getMessage());
    }
}

function exportDepositsCSV() {
    global $spexcon;
    
    $search = Sanitize($_GET['search'] ?? '');
    $status = Sanitize($_GET['status'] ?? '');
    $date = Sanitize($_GET['date'] ?? '');
    $amount = Sanitize($_GET['amount'] ?? '');
    
    $sql = "
        SELECT 
            ut.*,
            u.mail as user_email
        FROM user_transactions ut
        LEFT JOIN users u ON ut.user_id = u.uid
        WHERE ut.transaction_type = 'DEPOSIT'
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
    header('Content-Disposition: attachment; filename="deposits_export_' . date('Y-m-d_H-i-s') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    fputcsv($output, [
        'Transaction ID', 'User ID', 'Email', 'Amount USD', 'Coin ID', 'Status', 'Transaction Hash', 'Notes', 'Date'
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
                $row['transaction_hash'] ?: 'N/A',
                $row['notes'] ?: 'N/A',
                $row['created_at']
            ]);
        }
    }
    
    fclose($output);
    exit;
}
?>