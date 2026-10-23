<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
$type = isset($_GET['type']) ? $_GET['type'] : 'all';
$offset = ($page - 1) * $limit;

try {
    $whereClause = "WHERE ut.user_id = ?";
    $params = [$UserUID];
    $types = "i";
    
    $validTypes = ['BUY', 'SELL', 'DEPOSIT', 'WITHDRAWAL'];
    
    if ($type !== 'all' && in_array($type, $validTypes)) {
        $whereClause .= " AND ut.transaction_type = ?";
        $params[] = $type;
        $types .= "s";
    }
    
    $countQuery = "SELECT COUNT(*) as total FROM user_transactions ut $whereClause";
    $stmt = $spexcon->prepare($countQuery);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $totalResult = $stmt->get_result();
    $total = $totalResult->fetch_assoc()['total'];
    
    $query = "SELECT 
                ut.id,
                ut.transaction_type,
                ut.amount,
                ut.price_per_unit_usd,
                ut.total_value_usd,
                ut.fee_amount,
                ut.fee_value_usd,
                ut.status,
                ut.transaction_hash,
                ut.from_address,
                ut.to_address,
                ut.notes,
                ut.created_at,
                ut.coin_id,
                c.symbol as coin_symbol,
                c.name as coin_name,
                ci.small as coin_logo
              FROM user_transactions ut
              LEFT JOIN coins c ON ut.coin_id = c.id
              LEFT JOIN coin_images ci ON c.id = ci.coin_id
              $whereClause
              ORDER BY ut.created_at DESC
              LIMIT ? OFFSET ?";
    
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";
    
    $stmt = $spexcon->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        $method = 'N/A';
        if (strpos($row['notes'], 'Converted to USD') !== false) {
            $method = 'Conversion';
        } elseif ($row['transaction_type'] == 'DEPOSIT') {
            $method = 'Deposit';
        } elseif ($row['transaction_type'] == 'WITHDRAWAL') {
            $method = 'Withdrawal';
        } elseif (in_array($row['transaction_type'], ['BUY', 'SELL'])) {
            $method = 'Trade';
        }
        
        $tx = [
            'id' => $row['id'],
            'transaction_type' => $row['transaction_type'],
            'amount' => $row['amount'],
            'price_per_unit' => $row['price_per_unit_usd'],
            'total_value' => $row['total_value_usd'],
            'fee' => $row['fee_value_usd'],
            'status' => $row['status'],
            'method' => $method,
            'notes' => $row['notes'],
            'created_at' => $row['created_at']
        ];
        
        if ($row['coin_symbol']) {
            $tx['coin'] = [
                'id' => $row['coin_id'],
                'symbol' => $row['coin_symbol'],
                'name' => $row['coin_name'],
                'logo' => $row['coin_logo']
            ];
        }
        
        if ($row['transaction_hash']) {
            $tx['transaction_hash'] = $row['transaction_hash'];
        }
        
        if ($row['from_address']) {
            $tx['from_address'] = $row['from_address'];
        }
        if ($row['to_address']) {
            $tx['to_address'] = $row['to_address'];
        }
        
        $transactions[] = $tx;
    }
    
    echo json_encode([
        'success' => true,
        'transactions' => $transactions,
        'pagination' => [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit)
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Transaction fetch error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch transactions'
    ]);
}
?>