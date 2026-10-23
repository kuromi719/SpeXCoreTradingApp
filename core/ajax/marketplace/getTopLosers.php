<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';
require_once __DIR__ . '/../../api/crypto/MarketManager.php';

header('Content-Type: application/json');

try {
    $market = new MarketManager();
    
    $timeframe = $_GET['timeframe'] ?? '24h';
    $limit = min(20, max(5, (int)($_GET['limit'] ?? 10)));
    
    $leaders = $market->getPerformanceLeaders($timeframe, $limit);
    
    if (!$leaders || empty($leaders['losers'])) {
        throw new Exception('No losers data available');
    }
    
    $losers = array_map(function($coin) {
        return [
            'id' => $coin['id'],
            'name' => $coin['name'],
            'symbol' => $coin['symbol'],
            'current_price_usd' => (float)$coin['current_price_usd'],
            'change_percent' => (float)$coin['change_percent'],
            'market_cap_rank' => (int)$coin['market_cap_rank'],
            'logo' => $coin['logo']
        ];
    }, $leaders['losers']);
    
    echo json_encode([
        'success' => true,
        'data' => $losers,
        'timeframe' => $timeframe,
        'last_updated' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>