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
        SELECT pp.*, p.coin_name, p.coin_symbol, p.coin_logo, 
               p.status as presale_status, p.claim_enabled, p.trading_enabled
        FROM presale_purchases pp
        JOIN presales p ON pp.presale_id = p.id
        WHERE pp.user_id = ?
        ORDER BY pp.created_at DESC
    ";
    
    $stmt = $spexcon->prepare($query);
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $portfolio = [];
    $stats = [
        'total_invested' => 0,
        'total_tokens' => 0,
        'pending_claims' => 0,
        'claimed_tokens' => 0
    ];
    
    while ($row = $result->fetch_assoc()) {
        $portfolio[] = $row;
        
        $stats['total_invested'] += $row['total_cost'];
        $stats['total_tokens'] += $row['amount'];
        
        if ($row['presale_status'] === 'ended' && $row['claim_enabled'] && !$row['claimed']) {
            $stats['pending_claims']++;
        }
        
        if ($row['claimed']) {
            $stats['claimed_tokens'] += $row['amount'];
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => $portfolio,
        'stats' => $stats
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>