<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $query = "
        SELECT pp.*, p.coin_name, p.coin_symbol, p.coin_logo, p.status as presale_status
        FROM presale_purchases pp
        JOIN presales p ON pp.presale_id = p.id
        WHERE pp.user_id = ?
        ORDER BY pp.created_at DESC
    ";
    
    $stmt = $spexcon->prepare($query);
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $purchases = [];
    while ($row = $result->fetch_assoc()) {
        $purchases[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $purchases
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>