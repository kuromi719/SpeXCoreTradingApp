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
    $type = isset($_GET['type']) ? trim($_GET['type']) : '';
    $pair = isset($_GET['pair']) ? trim($_GET['pair']) : '';
    $side = isset($_GET['side']) ? trim($_GET['side']) : '';
    $onlyActive = isset($_GET['onlyActive']) && $_GET['onlyActive'] === 'true';
    
    $whereConditions = ["user_id = ?"];
    $params = [$UserUID];
    $types = "i";
    
    if ($onlyActive) {
        $whereConditions[] = "status IN ('PENDING', 'PARTIALLY_FILLED')";
    }
    
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
               CASE 
                   WHEN status = 'COMPLETED' THEN 'MARKET'
                   ELSE 'LIMIT'
               END as type,
               price_per_unit_usd as price, 
               price_per_unit_usd as avg_price,
               CASE 
                   WHEN status = 'COMPLETED' THEN amount
                   ELSE 0
               END as executed_qty,
               amount, order_id, created_at, status
        FROM user_transactions 
        WHERE $whereClause
        ORDER BY created_at DESC 
        LIMIT 200
    ";
    
    $stmt = $spexcon->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = $result->fetch_all(MYSQLI_ASSOC);
    
    // Status mapping
    foreach ($orders as &$order) {
        switch ($order['status']) {
            case 'COMPLETED':
                $order['status'] = 'FILLED';
                break;
            case 'CANCELLED':
                $order['status'] = 'CANCELLED';
                break;
            case 'PENDING':
                $order['status'] = 'NEW';
                break;
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => $orders,
        'count' => count($orders)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'data' => []
    ]);
}
?>