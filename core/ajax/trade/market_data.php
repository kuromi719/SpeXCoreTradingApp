<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../system/spex_v4_func.php';

try {
    $symbol = isset($_GET['symbol']) ? strtolower(str_replace('USDT', '', $_GET['symbol'])) : 'bitcoin';
    
    $priceQuery = $spexcon->prepare("SELECT current_price_usd FROM coin_market_data WHERE coin_id = ?");
    $priceQuery->bind_param("s", $symbol);
    $priceQuery->execute();
    $priceResult = $priceQuery->get_result()->fetch_assoc();
    $basePrice = $priceResult ? floatval($priceResult['current_price_usd']) : 76200;
    
    $orderbook = [
        'bids' => [],
        'asks' => []
    ];
    
    for ($i = 0; $i < 15; $i++) {
        $buyPrice = $basePrice - rand(1, 500);
        $sellPrice = $basePrice + rand(1, 500);
        $quantity = rand(1, 500) / 100;
        
        $orderbook['bids'][] = [
            number_format($buyPrice, 2),
            number_format($quantity, 6)
        ];
        
        $orderbook['asks'][] = [
            number_format($sellPrice, 2),
            number_format($quantity, 6)
        ];
    }
    
    usort($orderbook['bids'], function($a, $b) {
        return $b[0] <=> $a[0];
    });
    
    usort($orderbook['asks'], function($a, $b) {
        return $a[0] <=> $b[0];
    });
    
    $trades = [];
    for ($i = 0; $i < 20; $i++) {
        $trades[] = [
            'price' => number_format($basePrice + rand(-300, 300), 2),
            'qty' => number_format(rand(1, 200) / 100, 6),
            'time' => time() - rand(1, 3600),
            'isBuyerMaker' => rand(0, 1) == 1
        ];
    }
    
    $coinsQuery = $spexcon->prepare("
        SELECT c.id, c.symbol, c.name, cmd.current_price_usd, 
               cmd.price_change_percentage_24h, cmd.total_volume_usd 
        FROM coins c
        JOIN coin_market_data cmd ON c.id = cmd.coin_id 
        WHERE cmd.market_cap_rank > 0
        ORDER BY cmd.market_cap_rank ASC 
        LIMIT 100
    ");
    $coinsQuery->execute();
    $coins = $coinsQuery->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $pairs = [];
    foreach ($coins as $coin) {
        $symbol = strtoupper($coin['symbol']) . 'USDT';
        $pairs[] = [
            'symbol' => $symbol,
            'lastPrice' => $coin['current_price_usd'],
            'priceChangePercent' => $coin['price_change_percentage_24h'],
            'volume' => $coin['total_volume_usd']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'orderbook' => $orderbook,
        'trades' => $trades,
        'pairs' => $pairs
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Market data unavailable',
        'orderbook' => ['bids' => [], 'asks' => []],
        'trades' => [],
        'pairs' => []
    ]);
}
?>