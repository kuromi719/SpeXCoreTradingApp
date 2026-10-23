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
            getBalanceStats();
            break;
        case 'getBalances':
            getBalances();
            break;
        case 'getUsersAndCoins':
            getUsersAndCoins();
            break;
        case 'createBalance':
            createBalance();
            break;
        case 'updateBalance':
            updateBalance();
            break;
        case 'deleteBalance':
            deleteBalance();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getBalanceStats() {
    global $spexcon;
    
    // Total users with balances
    $totalUsersQuery = $spexcon->query("SELECT COUNT(DISTINCT user_id) as count FROM user_coin_balances WHERE total_balance > 0");
    $totalUsers = $totalUsersQuery ? $totalUsersQuery->fetch_assoc()['count'] : 0;
    
    // Total different coins
    $totalCoinsQuery = $spexcon->query("SELECT COUNT(DISTINCT coin_id) as count FROM user_coin_balances WHERE total_balance > 0");
    $totalCoins = $totalCoinsQuery ? $totalCoinsQuery->fetch_assoc()['count'] : 0;
    
    // Total USD value
    $totalValueQuery = $spexcon->query("SELECT SUM(total_invested_usd) as total FROM user_coin_balances WHERE total_balance > 0");
    $totalValue = $totalValueQuery ? ($totalValueQuery->fetch_assoc()['total'] ?? 0) : 0;
    
    // Total locked value
    $lockedValueQuery = $spexcon->query("SELECT SUM(total_invested_usd * (locked_balance / total_balance)) as total FROM user_coin_balances WHERE locked_balance > 0 AND total_balance > 0");
    $lockedValue = $lockedValueQuery ? ($lockedValueQuery->fetch_assoc()['total'] ?? 0) : 0;
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_users' => $totalUsers,
            'total_coins' => $totalCoins,
            'total_value' => $totalValue,
            'locked_value' => $lockedValue
        ]
    ]);
}

function getBalances() {
    global $spexcon;
    
    $query = $spexcon->query("
        SELECT 
            ucb.*,
            u.mail as user_email,
            c.name as coin_name,
            c.symbol as coin_symbol
        FROM user_coin_balances ucb
        LEFT JOIN users u ON ucb.user_id = u.uid
        LEFT JOIN coins c ON ucb.coin_id = c.id
        ORDER BY ucb.updated_at DESC
    ");
    
    if (!$query) {
        throw new Exception("Database error: " . $spexcon->error);
    }
    
    $balances = [];
    while ($row = $query->fetch_assoc()) {
        $balances[] = $row;
    }
    
    echo json_encode(['success' => true, 'balances' => $balances]);
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

function createBalance() {
    global $spexcon;
    
    $user_id = (int)($_POST['user_id'] ?? 0);
    $coin_id = Sanitize($_POST['coin_id'] ?? '');
    $total_balance = Sanitize($_POST['total_balance'] ?? '0');
    $available_balance = Sanitize($_POST['available_balance'] ?? '0');
    $locked_balance = Sanitize($_POST['locked_balance'] ?? '0');
    $average_buy_price = Sanitize($_POST['average_buy_price'] ?? '0');
    $total_invested_usd = Sanitize($_POST['total_invested_usd'] ?? '0');
    
    if (!$user_id || empty($coin_id)) {
        throw new Exception("User and coin are required");
    }
    
    // Check if balance already exists
    $checkQuery = $spexcon->prepare("SELECT id FROM user_coin_balances WHERE user_id = ? AND coin_id = ?");
    $checkQuery->bind_param("is", $user_id, $coin_id);
    $checkQuery->execute();
    if ($checkQuery->get_result()->num_rows > 0) {
        throw new Exception("Balance already exists for this user and coin");
    }
    
    $stmt = $spexcon->prepare("
        INSERT INTO user_coin_balances (
            user_id, coin_id, total_balance, available_balance, locked_balance,
            average_buy_price, total_invested_usd, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");
    
    $stmt->bind_param("issssss", 
        $user_id, $coin_id, $total_balance, $available_balance, $locked_balance,
        $average_buy_price, $total_invested_usd
    );
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Balance created successfully']);
    } else {
        throw new Exception("Failed to create balance: " . $stmt->error);
    }
}

function updateBalance() {
    global $spexcon;
    
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        throw new Exception("Balance ID is required");
    }
    
    $user_id = (int)($_POST['user_id'] ?? 0);
    $coin_id = Sanitize($_POST['coin_id'] ?? '');
    $total_balance = Sanitize($_POST['total_balance'] ?? '0');
    $available_balance = Sanitize($_POST['available_balance'] ?? '0');
    $locked_balance = Sanitize($_POST['locked_balance'] ?? '0');
    $average_buy_price = Sanitize($_POST['average_buy_price'] ?? '0');
    $total_invested_usd = Sanitize($_POST['total_invested_usd'] ?? '0');
    
    if (!$user_id || empty($coin_id)) {
        throw new Exception("User and coin are required");
    }
    
    // Check if another balance exists for same user/coin
    $checkQuery = $spexcon->prepare("SELECT id FROM user_coin_balances WHERE user_id = ? AND coin_id = ? AND id != ?");
    $checkQuery->bind_param("isi", $user_id, $coin_id, $id);
    $checkQuery->execute();
    if ($checkQuery->get_result()->num_rows > 0) {
        throw new Exception("Another balance exists for this user and coin");
    }
    
    $stmt = $spexcon->prepare("
        UPDATE user_coin_balances SET 
            user_id = ?, coin_id = ?, total_balance = ?, available_balance = ?, 
            locked_balance = ?, average_buy_price = ?, total_invested_usd = ?,
            updated_at = NOW()
        WHERE id = ?
    ");
    
    $stmt->bind_param("issssssi", 
        $user_id, $coin_id, $total_balance, $available_balance, $locked_balance,
        $average_buy_price, $total_invested_usd, $id
    );
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Balance updated successfully']);
    } else {
        throw new Exception("Failed to update balance: " . $stmt->error);
    }
}

function deleteBalance() {
    global $spexcon;
    
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        throw new Exception("Balance ID is required");
    }
    
    $stmt = $spexcon->prepare("DELETE FROM user_coin_balances WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Balance deleted successfully']);
        } else {
            throw new Exception("Balance not found");
        }
    } else {
        throw new Exception("Failed to delete balance: " . $stmt->error);
    }
}
?>