<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';
require_once __DIR__ . '/../../api/crypto/MarketManager.php';

header('Content-Type: application/json');

try {
    $market = new MarketManager();
    
    $overview = $market->getMarketOverview();
    $distribution = $market->getMarketCapDistribution();
    $leaders24h = $market->getPerformanceLeaders('24h', 5);
    $leaders7d = $market->getPerformanceLeaders('7d', 5);
    $leaders30d = $market->getPerformanceLeaders('30d', 5);
    
    if (!$overview) {
        throw new Exception('Failed to fetch market overview');
    }
    
    $topCoins = $market->getTopCoins(10, 0, []);
    $btcDominance = 0;
    $ethDominance = 0;
    
    if (!empty($topCoins) && $overview['total_market_cap'] > 0) {
        foreach ($topCoins as $coin) {
            if (strtolower($coin['symbol']) === 'btc') {
                $btcDominance = ($coin['market_cap_usd'] / $overview['total_market_cap']) * 100;
            }
            if (strtolower($coin['symbol']) === 'eth') {
                $ethDominance = ($coin['market_cap_usd'] / $overview['total_market_cap']) * 100;
            }
        }
    }
    
    $fearGreedIndex = calculateFearGreedIndex($overview);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'overview' => [
                'total_market_cap' => (float)$overview['total_market_cap'],
                'total_volume_24h' => (float)$overview['total_volume_24h'],
                'total_coins' => (int)$overview['total_coins'],
                'gainers_count' => (int)$overview['gainers_count'],
                'losers_count' => (int)$overview['losers_count'],
                'avg_change_24h' => (float)$overview['avg_change_24h'],
                'biggest_gainer' => (float)$overview['biggest_gainer'],
                'biggest_loser' => (float)$overview['biggest_loser'],
                'btc_dominance' => round($btcDominance, 2),
                'eth_dominance' => round($ethDominance, 2),
                'fear_greed_index' => $fearGreedIndex
            ],
            'distribution' => $distribution,
            'performance_leaders' => [
                '24h' => $leaders24h,
                '7d' => $leaders7d,
                '30d' => $leaders30d
            ],
            'top_coins' => array_slice($topCoins, 0, 5),
            'last_updated' => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

function calculateFearGreedIndex($overview) {
    $avgChange = (float)$overview['avg_change_24h'];
    $gainersCount = (int)$overview['gainers_count'];
    $losersCount = (int)$overview['losers_count'];
    $totalCoins = $gainersCount + $losersCount;
    
    if ($totalCoins == 0) return 50;
    
    $gainersRatio = ($gainersCount / $totalCoins) * 100;
    
    $changeScore = max(0, min(100, 50 + ($avgChange * 2)));
    $ratioScore = $gainersRatio;
    
    $fearGreedScore = ($changeScore * 0.6) + ($ratioScore * 0.4);
    
    return round($fearGreedScore);
}
?>