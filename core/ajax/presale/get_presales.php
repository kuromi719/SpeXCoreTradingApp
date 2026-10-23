<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $status = $_GET['status'] ?? 'all';
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = 12;
    $offset = ($page - 1) * $limit;
    
    $whereClause = '';
    $params = [];
    $types = '';
    
    if ($status !== 'all') {
        $whereClause = 'WHERE status = ?';
        $params[] = $status;
        $types .= 's';
    }
    
    $query = "
        SELECT id, coin_name, coin_symbol, coin_logo, description, price, 
               total_supply, sold_amount, min_purchase, max_purchase,
               start_date, end_date, status, featured, blockchain,
               website, whitepaper, telegram, twitter
        FROM presales 
        $whereClause
        ORDER BY featured DESC, start_date ASC 
        LIMIT ? OFFSET ?
    ";
    
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    
    $stmt = $spexcon->prepare($query);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $presales = [];
    while ($row = $result->fetch_assoc()) {
        $presales[] = $row;
    }
    
    $countQuery = "SELECT COUNT(*) as total FROM presales $whereClause";
    if ($status !== 'all') {
        $countStmt = $spexcon->prepare($countQuery);
        $countStmt->bind_param('s', $status);
        $countStmt->execute();
        $totalCount = $countStmt->get_result()->fetch_assoc()['total'];
    } else {
        $totalCount = $spexcon->query($countQuery)->fetch_assoc()['total'];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $presales,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => ceil($totalCount / $limit),
            'total_count' => $totalCount
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>