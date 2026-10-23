<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(0);

require_once __DIR__ . '/../../system/spex_v4_func.php';

$favorites = $_GET['favorites'] ?? '';

try {
    if (!empty($favorites)) {
        $favoriteIds = explode(',', $favorites);
        $safeIds = [];
        
        foreach ($favoriteIds as $id) {
            $safeId = $spexcon->real_escape_string(trim($id));
            if (!empty($safeId)) {
                $safeIds[] = strtoupper("'$safeId'");
            }
        }
        
        if (!empty($safeIds)) {
            $idList = implode(',', $safeIds);
            $query = "
                SELECT c.id, c.symbol, 
                       COALESCE(cmd.current_price_usd, 0) as price,
                       COALESCE(cmd.price_change_percentage_24h, 0) as price_change
                FROM coins c
                LEFT JOIN coin_market_data cmd ON c.id = cmd.coin_id
                WHERE c.symbol IN ($idList)
                ORDER BY cmd.market_cap_rank ASC
                LIMIT 50
            ";
        } else {
            $query = "
                SELECT c.id, c.symbol, 
                       COALESCE(cmd.current_price_usd, 0) as price,
                       COALESCE(cmd.price_change_percentage_24h, 0) as price_change
                FROM coins c
                LEFT JOIN coin_market_data cmd ON c.id = cmd.coin_id
                WHERE cmd.market_cap_rank <= 100
                ORDER BY cmd.market_cap_rank ASC
                LIMIT 50
            ";
        }
    } else {
        $query = "
            SELECT c.id, c.symbol, 
                   COALESCE(cmd.current_price_usd, 0) as price,
                   COALESCE(cmd.price_change_percentage_24h, 0) as price_change
            FROM coins c
            LEFT JOIN coin_market_data cmd ON c.id = cmd.coin_id
            WHERE cmd.market_cap_rank <= 100
            ORDER BY cmd.market_cap_rank ASC
            LIMIT 50
        ";
    }
    
    $result = $spexcon->query($query);
    
    if (!$result) {
        throw new Exception("Query failed: " . $spexcon->error);
    }
    
    $pairs = [];
    while ($row = $result->fetch_assoc()) {
        $pairs[] = [
            'id' => $row['id'],
            'symbol' => strtoupper($row['symbol']),
            'price' => floatval($row['price']),
            'change' => floatval($row['price_change'])
        ];
    }
    
    echo json_encode([
        'success' => true,
        'pairs' => $pairs
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'pairs' => []
    ]);
}
?>