<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';
require_once __DIR__ . '/../../api/crypto/MarketManager.php';

header('Content-Type: application/json');

try {
    $market = new MarketManager();
    
    $overview = $market->getMarketOverview();
    
    if (!$overview) {
        throw new Exception('Failed to fetch market overview');
    }
    
    $gainersCount = (int)$overview['gainers_count'];
    $losersCount = (int)$overview['losers_count'];
    $totalCoins = (int)$overview['total_coins'];
    $totalMarketCap = (float)$overview['total_market_cap'];
    $totalVolume = (float)$overview['total_volume_24h'];
    $avgChange = (float)$overview['avg_change_24h'];
    
    $marketDominance = [
        'btc' => 0,
        'eth' => 0,
        'others' => 100
    ];
    
    $btcData = $market->getTopCoins(1, 0, []);
    $ethData = $market->getTopCoins(2, 0, []);
    
    if (!empty($btcData) && $totalMarketCap > 0) {
        $marketDominance['btc'] = ($btcData[0]['market_cap_usd'] / $totalMarketCap) * 100;
    }
    
    if (!empty($ethData) && count($ethData) > 1 && $totalMarketCap > 0) {
        $marketDominance['eth'] = ($ethData[1]['market_cap_usd'] / $totalMarketCap) * 100;
    }
    
    $marketDominance['others'] = 100 - $marketDominance['btc'] - $marketDominance['eth'];
    
    echo json_encode([
        'success' => true,
        'data' => [
            'total_market_cap' => $totalMarketCap,
            'total_volume_24h' => $totalVolume,
            'total_coins' => $totalCoins,
            'gainers_count' => $gainersCount,
            'losers_count' => $losersCount,
            'avg_change_24h' => $avgChange,
            'biggest_gainer' => (float)$overview['biggest_gainer'],
            'biggest_loser' => (float)$overview['biggest_loser'],
            'market_dominance' => $marketDominance,
            'last_updated' => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>