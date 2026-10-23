<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../system/spex_v4_func.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $pair = isset($_GET['pair']) ? trim($_GET['pair']) : '';
    $side = isset($_GET['side']) ? trim($_GET['side']) : '';
    
    $whereConditions = ["user_id = ? AND status = 'COMPLETED'"];
    $params = [$UserUID];
    $types = "i";
    
    if (!empty($search)) {
        $whereConditions[] = "UPPER(coin_id) LIKE ?";
        $params[] = '%' . strtoupper($search) . '%';
        $types .= "s";
    }
    
    if (!empty($side)) {
        $whereConditions[] = "transaction_type = ?";
        $params[] = $side;
        $types .= "s";
    }
    
    if (!empty($pair)) {
        $coinId = strtolower(str_replace('USDT', '', $pair));
        $whereConditions[] = "coin_id = ?";
        $params[] = $coinId;
        $types .= "s";
    }
    
    $whereClause = implode(' AND ', $whereConditions);
    
    $query = "
        SELECT id, CONCAT(UPPER(coin_id), 'USDT') as pair, 
               transaction_type as side,
               price_per_unit_usd as price, amount,
               fee_amount as fee, order_id, created_at
        FROM user_transactions 
        WHERE $whereClause
        ORDER BY created_at DESC 
        LIMIT 100
    ";
    
    $stmt = $spexcon->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $trades = $result->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $trades,
        'count' => count($trades)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'data' => []
    ]);
}
?>