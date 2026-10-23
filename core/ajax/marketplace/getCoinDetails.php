<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';
require_once __DIR__ . '/../../api/crypto/MarketManager.php';
require_once __DIR__ . '/../../utils/CRUDManager.php';

header('Content-Type: application/json');

try {
    $coinId = $_GET['coin_id'] ?? '';
    
    if (empty($coinId)) {
        throw new Exception('Coin ID is required');
    }
    
    $market = new MarketManager();
    $crud = new CRUDManager();
    $UID = getSession('UID');
    
    $coinData = $crud->queryOne("
        SELECT 
            c.*,
            cmd.*,
            ci.thumb, ci.small, ci.large,
            cs.*
        FROM coins c
        LEFT JOIN coin_market_data cmd ON c.id = cmd.coin_id
        LEFT JOIN coin_images ci ON c.id = ci.coin_id
        LEFT JOIN coin_scores cs ON c.id = cs.coin_id
        WHERE c.id = ?
    ", [$coinId]);
    
    if (!$coinData) {
        throw new Exception('Coin not found');
    }
    
    $userBalance = null;
    if ($UID) {
        $userBalance = $crud->queryOne("
            SELECT * FROM user_coin_balances 
            WHERE user_id = ? AND coin_id = ?
        ", [$UID, $coinId]);
    }
    
    $similarCoins = $market->getSimilarCoins($coinId, 5);
    
    $priceHistory = $crud->query("
        SELECT timestamp, price_usd, market_cap, total_volume, date_recorded
        FROM coin_price_history 
        WHERE coin_id = ? 
        ORDER BY timestamp DESC 
        LIMIT 30
    ", [$coinId]);
    
    $categories = [];
    if (!empty($coinData['categories'])) {
        $categoriesJson = json_decode($coinData['categories'], true);
        if (is_array($categoriesJson)) {
            $categories = $categoriesJson;
        }
    }
    
    $platforms = [];
    if (!empty($coinData['platforms'])) {
        $platformsJson = json_decode($coinData['platforms'], true);
        if (is_array($platformsJson)) {
            $platforms = $platformsJson;
        }
    }
    
    $isInWatchlist = false;
    if ($UID) {
        $watchlistCheck = $crud->queryOne("
            SELECT id FROM user_watchlist 
            WHERE user_id = ? AND coin_id = ?
        ", [$UID, $coinId]);
        $isInWatchlist = !empty($watchlistCheck);
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'coin' => [
                'id' => $coinData['id'],
                'name' => $coinData['name'],
                'symbol' => strtoupper($coinData['symbol']),
                'description' => $coinData['description'] ?? '',
                'homepage' => $coinData['homepage'] ?? '',
                'blockchain_site' => $coinData['blockchain_site'] ?? '',
                'twitter_screen_name' => $coinData['twitter_screen_name'] ?? '',
                'telegram_channel_identifier' => $coinData['telegram_channel_identifier'] ?? '',
                'subreddit_url' => $coinData['subreddit_url'] ?? '',
                'repos_url' => $coinData['repos_url'] ?? '',
                'genesis_date' => $coinData['genesis_date'],
                'categories' => $categories,
                'platforms' => $platforms
            ],
            'market_data' => [
                'current_price_usd' => (float)$coinData['current_price_usd'],
                'current_price_btc' => (float)$coinData['current_price_btc'],
                'market_cap_usd' => (float)$coinData['market_cap_usd'],
                'market_cap_rank' => (int)$coinData['market_cap_rank'],
                'total_volume_usd' => (float)$coinData['total_volume_usd'],
                'high_24h_usd' => (float)$coinData['high_24h_usd'],
                'low_24h_usd' => (float)$coinData['low_24h_usd'],
                'price_change_24h' => (float)$coinData['price_change_24h'],
                'price_change_percentage_24h' => (float)$coinData['price_change_percentage_24h'],
                'price_change_percentage_7d' => (float)$coinData['price_change_percentage_7d'],
                'price_change_percentage_30d' => (float)$coinData['price_change_percentage_30d'],
                'circulating_supply' => (float)$coinData['circulating_supply'],
                'total_supply' => (float)$coinData['total_supply'],
                'max_supply' => (float)$coinData['max_supply'],
                'ath_usd' => (float)$coinData['ath_usd'],
                'ath_change_percentage_usd' => (float)$coinData['ath_change_percentage_usd'],
                'ath_date_usd' => $coinData['ath_date_usd'],
                'atl_usd' => (float)$coinData['atl_usd'],
                'atl_change_percentage_usd' => (float)$coinData['atl_change_percentage_usd'],
                'atl_date_usd' => $coinData['atl_date_usd']
            ],
            'images' => [
                'thumb' => $coinData['thumb'] ?? '',
                'small' => $coinData['small'] ?? '',
                'large' => $coinData['large'] ?? ''
            ],
            'scores' => [
                'coingecko_rank' => (int)$coinData['coingecko_rank'],
                'coingecko_score' => (float)$coinData['coingecko_score'],
                'developer_score' => (float)$coinData['developer_score'],
                'community_score' => (float)$coinData['community_score'],
                'liquidity_score' => (float)$coinData['liquidity_score'],
                'public_interest_score' => (float)$coinData['public_interest_score']
            ],
            'user_data' => [
                'balance' => $userBalance,
                'is_in_watchlist' => $isInWatchlist
            ],
            'similar_coins' => $similarCoins,
            'price_history' => $priceHistory,
            'last_updated' => $coinData['last_updated'] ?? date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>