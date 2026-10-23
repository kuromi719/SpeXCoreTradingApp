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
        case 'getStats':
            getTransactionStats();
            break;
        case 'getTransactions':
            getTransactions();
            break;
        case 'getUsersAndCoins':
            getUsersAndCoins();
            break;
        case 'createTransaction':
            createTransaction();
            break;
        case 'updateTransaction':
            updateTransaction();
            break;
        case 'deleteTransaction':
            deleteTransaction();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getTransactionStats() {
    global $spexcon;
    
    // Total transactions
    $totalQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_transactions");
    $totalTransactions = $totalQuery ? $totalQuery->fetch_assoc()['count'] : 0;
    
    // Total volume (USD)
    $volumeQuery = $spexcon->query("SELECT SUM(total_value_usd) as total FROM user_transactions WHERE status = 'COMPLETED'");
    $totalVolume = $volumeQuery ? ($volumeQuery->fetch_assoc()['total'] ?? 0) : 0;
    
    // Today's transactions
    $todayQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_transactions WHERE DATE(created_at) = CURDATE()");
    $todayTransactions = $todayQuery ? $todayQuery->fetch_assoc()['count'] : 0;
    
    // Pending transactions
    $pendingQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_transactions WHERE status = 'PENDING'");
    $pendingTransactions = $pendingQuery ? $pendingQuery->fetch_assoc()['count'] : 0;
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_transactions' => $totalTransactions,
            'total_volume' => $totalVolume,
            'today_transactions' => $todayTransactions,
            'pending_transactions' => $pendingTransactions
        ]
    ]);
}

function getTransactions() {
    global $spexcon;
    
    $query = $spexcon->query("
        SELECT 
            ut.*,
            u.mail as user_email
        FROM user_transactions ut
        LEFT JOIN users u ON ut.user_id = u.uid
        WHERE transaction_type IN ('BUY','SELL','TRANSFER_IN','TRANSFER_OUT','REWARD','STAKING_REWARD','AIRDROP')
        ORDER BY ut.created_at DESC 
        LIMIT 1000
    ");
    
    if (!$query) {
        throw new Exception("Database error: " . $spexcon->error);
    }
    
    $transactions = [];
    while ($row = $query->fetch_assoc()) {
        $transactions[] = $row;
    }
    
    echo json_encode(['success' => true, 'transactions' => $transactions]);
}

function getUsersAndCoins() {
    global $spexcon;
    
    // Get users
    $usersQuery = $spexcon->query("SELECT uid, mail FROM users WHERE disablead = 0 ORDER BY mail");
    $users = [];
    if ($usersQuery) {
        while ($row = $usersQuery->fetch_assoc()) {
            $users[] = $row;
        }
    }
    
    // Get coins
    $coinsQuery = $spexcon->query("SELECT id, name, symbol FROM coins ORDER BY name");
    $coins = [];
    if ($coinsQuery) {
        while ($row = $coinsQuery->fetch_assoc()) {
            $coins[] = $row;
        }
    }
    
    echo json_encode([
        'success' => true,
        'users' => $users,
        'coins' => $coins
    ]);
}

function createTransaction() {
    global $spexcon;
    
    $user_id = (int)($_POST['user_id'] ?? 0);
    $coin_id = Sanitize($_POST['coin_id'] ?? '');
    $transaction_type = Sanitize($_POST['transaction_type'] ?? '');
    $amount = Sanitize($_POST['amount'] ?? '0');
    $price_per_unit_usd = Sanitize($_POST['price_per_unit_usd'] ?? '0');
    $total_value_usd = Sanitize($_POST['total_value_usd'] ?? '0');
    $fee_amount = Sanitize($_POST['fee_amount'] ?? '0');
    $fee_coin_id = Sanitize($_POST['fee_coin_id'] ?? 'usd');
    $fee_value_usd = Sanitize($_POST['fee_value_usd'] ?? '0');
    $from_address = Sanitize($_POST['from_address'] ?? '');
    $to_address = Sanitize($_POST['to_address'] ?? '');
    $transaction_hash = Sanitize($_POST['transaction_hash'] ?? '');
    $order_id = Sanitize($_POST['order_id'] ?? '');
    $status = Sanitize($_POST['status'] ?? 'COMPLETED');
    $notes = Sanitize($_POST['notes'] ?? '');
    
    if (!$user_id || empty($coin_id) || empty($transaction_type)) {
        throw new Exception("User, coin, and transaction type are required");
    }
    
    // Valid transaction types
    $validTypes = ['BUY', 'SELL', 'DEPOSIT', 'WITHDRAWAL', 'TRANSFER_IN', 'TRANSFER_OUT', 'REWARD', 'STAKING_REWARD', 'AIRDROP'];
    if (!in_array($transaction_type, $validTypes)) {
        throw new Exception("Invalid transaction type");
    }
    
    // Valid statuses
    $validStatuses = ['PENDING', 'COMPLETED', 'FAILED', 'CANCELLED'];
    if (!in_array($status, $validStatuses)) {
        throw new Exception("Invalid status");
    }
    
    $stmt = $spexcon->prepare("
        INSERT INTO user_transactions (
            user_id, coin_id, transaction_type, amount, price_per_unit_usd, 
            total_value_usd,
            from_address, to_address, transaction_hash, order_id, status, notes, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->bind_param("issssssssss", 
        $user_id, $coin_id, $transaction_type, $amount, $price_per_unit_usd,
        $total_value_usd,
        $from_address, $to_address, $transaction_hash, $order_id, $status, $notes
    );
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Transaction created successfully']);
    } else {
        throw new Exception("Failed to create transaction: " . $stmt->error);
    }
}

function updateTransaction() {
    global $spexcon;
    
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        throw new Exception("Transaction ID is required");
    }
    
    $user_id = (int)($_POST['user_id'] ?? 0);
    $coin_id = Sanitize($_POST['coin_id'] ?? '');
    $transaction_type = Sanitize($_POST['transaction_type'] ?? '');
    $amount = Sanitize($_POST['amount'] ?? '0');
    $price_per_unit_usd = Sanitize($_POST['price_per_unit_usd'] ?? '0');
    $total_value_usd = Sanitize($_POST['total_value_usd'] ?? '0');
    $fee_amount = Sanitize($_POST['fee_amount'] ?? '0');
    $fee_coin_id = Sanitize($_POST['fee_coin_id'] ?? 'usd');
    $fee_value_usd = Sanitize($_POST['fee_value_usd'] ?? '0');
    $from_address = Sanitize($_POST['from_address'] ?? '');
    $to_address = Sanitize($_POST['to_address'] ?? '');
    $transaction_hash = Sanitize($_POST['transaction_hash'] ?? '');
    $order_id = Sanitize($_POST['order_id'] ?? '');
    $status = Sanitize($_POST['status'] ?? 'COMPLETED');
    $notes = Sanitize($_POST['notes'] ?? '');
    
    if (!$user_id || empty($coin_id) || empty($transaction_type)) {
        throw new Exception("User, coin, and transaction type are required");
    }
    
    $stmt = $spexcon->prepare("
        UPDATE user_transactions SET 
            user_id = ?, coin_id = ?, transaction_type = ?, amount = ?, price_per_unit_usd = ?, 
            total_value_usd = ?, fee_amount = ?, fee_coin_id = ?, fee_value_usd = ?, 
            from_address = ?, to_address = ?, transaction_hash = ?, order_id = ?, status = ?, notes = ?
        WHERE id = ?
    ");
    
    $stmt->bind_param("issssssssssssssi", 
        $user_id, $coin_id, $transaction_type, $amount, $price_per_unit_usd,
        $total_value_usd, $fee_amount, $fee_coin_id, $fee_value_usd,
        $from_address, $to_address, $transaction_hash, $order_id, $status, $notes, $id
    );
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Transaction updated successfully']);
    } else {
        throw new Exception("Failed to update transaction: " . $stmt->error);
    }
}

function deleteTransaction() {
    global $spexcon;
    
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        throw new Exception("Transaction ID is required");
    }
    
    $stmt = $spexcon->prepare("DELETE FROM user_transactions WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Transaction deleted successfully']);
        } else {
            throw new Exception("Transaction not found");
        }
    } else {
        throw new Exception("Failed to delete transaction: " . $stmt->error);
    }
}
?>