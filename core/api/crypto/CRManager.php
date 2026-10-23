<?php
namespace CRManager;

require_once __DIR__ . '/../http/HTTPManager.php';
use HTTPManager\HTTPManager;

class CRManager {
    
    private $apiUrl = 'https://api.coingecko.com/api/v3/';
    private $cache = [];
    private $cacheTime = 300;
    private $httpManager;
    
    public function __construct() {
        $this->httpManager = new HTTPManager();
    }
    
    /**
     * Test API connection
     * @return array|null
     */
    public function testConnection() {
        try {
            $url = $this->apiUrl . 'ping';
            $result = $this->httpManager->get($url);
            return $result;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Get single coin information
     */
    public function getCoin($coinId, $includeMarketData = true, $includeCommunityData = false, $includeDeveloperData = false) {
        $cacheKey = 'coin_' . $coinId . '_' . ($includeMarketData ? '1' : '0') . '_' . ($includeCommunityData ? '1' : '0') . '_' . ($includeDeveloperData ? '1' : '0');
        
        if ($this->isCacheValid($cacheKey)) {
            return $this->cache[$cacheKey]['data'];
        }
        
        $params = [];
        if (!$includeMarketData) $params[] = 'market_data=false';
        if (!$includeCommunityData) $params[] = 'community_data=false';
        if (!$includeDeveloperData) $params[] = 'developer_data=false';
        
        $url = $this->apiUrl . 'coins/' . $coinId . (!empty($params) ? '?' . implode('&', $params) : '');
        $data = $this->httpManager->get($url);
        
        if ($data) {
            $this->cache[$cacheKey] = [
                'data' => $data,
                'time' => time()
            ];
        }
        
        return $data;
    }
    
    /**
     * Get detailed coin information (formatted)
     */
    public function getCoinDetails($coinId) {
        $coin = $this->getCoin($coinId);
        
        if (!$coin) return null;
        
        return [
            'id' => $coin['id'] ?? '',
            'name' => $coin['name'] ?? '',
            'symbol' => strtoupper($coin['symbol'] ?? ''),
            'description' => $coin['description']['en'] ?? '',
            'homepage' => $coin['links']['homepage'][0] ?? '',
            'blockchain_site' => $coin['links']['blockchain_site'][0] ?? '',
            'official_forum_url' => $coin['links']['official_forum_url'][0] ?? '',
            'chat_url' => $coin['links']['chat_url'][0] ?? '',
            'twitter_screen_name' => $coin['links']['twitter_screen_name'] ?? '',
            'facebook_username' => $coin['links']['facebook_username'] ?? '',
            'telegram_channel_identifier' => $coin['links']['telegram_channel_identifier'] ?? '',
            'subreddit_url' => $coin['links']['subreddit_url'] ?? '',
            'repos_url' => $coin['links']['repos_url']['github'][0] ?? '',
            'image' => $coin['image']['large'] ?? '',
            'genesis_date' => $coin['genesis_date'] ?? null,
            'market_cap_rank' => $coin['market_cap_rank'] ?? 0,
            'coingecko_rank' => $coin['coingecko_rank'] ?? 0,
            'coingecko_score' => $coin['coingecko_score'] ?? 0,
            'developer_score' => $coin['developer_score'] ?? 0,
            'community_score' => $coin['community_score'] ?? 0,
            'liquidity_score' => $coin['liquidity_score'] ?? 0,
            'public_interest_score' => $coin['public_interest_score'] ?? 0,
            'current_price' => $coin['market_data']['current_price']['usd'] ?? 0,
            'price_change_24h' => $coin['market_data']['price_change_24h'] ?? 0,
            'price_change_percentage_24h' => $coin['market_data']['price_change_percentage_24h'] ?? 0,
            'price_change_percentage_7d' => $coin['market_data']['price_change_percentage_7d'] ?? 0,
            'price_change_percentage_30d' => $coin['market_data']['price_change_percentage_30d'] ?? 0,
            'market_cap' => $coin['market_data']['market_cap']['usd'] ?? 0,
            'total_volume' => $coin['market_data']['total_volume']['usd'] ?? 0,
            'high_24h' => $coin['market_data']['high_24h']['usd'] ?? 0,
            'low_24h' => $coin['market_data']['low_24h']['usd'] ?? 0,
            'circulating_supply' => $coin['market_data']['circulating_supply'] ?? 0,
            'total_supply' => $coin['market_data']['total_supply'] ?? 0,
            'max_supply' => $coin['market_data']['max_supply'] ?? 0,
            'ath' => $coin['market_data']['ath']['usd'] ?? 0,
            'ath_change_percentage' => $coin['market_data']['ath_change_percentage']['usd'] ?? 0,
            'ath_date' => $coin['market_data']['ath_date']['usd'] ?? '',
            'atl' => $coin['market_data']['atl']['usd'] ?? 0,
            'atl_change_percentage' => $coin['market_data']['atl_change_percentage']['usd'] ?? 0,
            'atl_date' => $coin['market_data']['atl_date']['usd'] ?? '',
        ];
    }
    
    /**
     * Get list of coins with market data
     */
    public function listCoins($limit = 100, $page = 1, $currency = 'usd', $order = 'market_cap_desc', $sparkline = false) {
        $cacheKey = 'coins_list_' . $limit . '_' . $page . '_' . $currency . '_' . $order . '_' . ($sparkline ? '1' : '0');
        
        if ($this->isCacheValid($cacheKey)) {
            return $this->cache[$cacheKey]['data'];
        }
        
        $url = $this->apiUrl . 'coins/markets?vs_currency=' . $currency . '&order=' . $order . '&per_page=' . $limit . '&page=' . $page . '&sparkline=' . ($sparkline ? 'true' : 'false');
        $data = $this->httpManager->get($url);
        
        if ($data) {
            $this->cache[$cacheKey] = [
                'data' => $data,
                'time' => time()
            ];
        }
        
        return $data;
    }
    
    /**
     * Get coin price
     */
    public function getCoinPrice($coinId, $currency = 'usd', $includeMarketCap = false, $include24hrVol = false, $include24hrChange = false) {
        $params = ['ids=' . $coinId, 'vs_currencies=' . $currency];
        
        if ($includeMarketCap) $params[] = 'include_market_cap=true';
        if ($include24hrVol) $params[] = 'include_24hr_vol=true';
        if ($include24hrChange) $params[] = 'include_24hr_change=true';
        
        $url = $this->apiUrl . 'simple/price?' . implode('&', $params);
        $data = $this->httpManager->get($url);
        
        return $data ? $data[$coinId] : null;
    }
    
    /**
     * Get historical price data
     */
    public function getCoinHistory($coinId, $currency = 'usd', $days = 7, $interval = 'daily') {
        $url = $this->apiUrl . 'coins/' . $coinId . '/market_chart?vs_currency=' . $currency . '&days=' . $days;
        if ($interval && $days <= 90) {
            $url .= '&interval=' . $interval;
        }
        return $this->httpManager->get($url);
    }
    
    /**
     * Get trending coins
     */
    public function getTrendingCoins() {
        $cacheKey = 'trending_coins';
        
        if ($this->isCacheValid($cacheKey)) {
            return $this->cache[$cacheKey]['data'];
        }
        
        $url = $this->apiUrl . 'search/trending';
        $data = $this->httpManager->get($url);
        
        if ($data) {
            $this->cache[$cacheKey] = [
                'data' => $data,
                'time' => time()
            ];
        }
        
        return $data;
    }
    
    /**
     * Search for coins
     */
    public function searchCoin($query) {
        $url = $this->apiUrl . 'search?query=' . urlencode($query);
        return $this->httpManager->get($url);
    }
    
    /**
     * Get top gainers
     */
    public function getTopGainers($currency = 'usd', $limit = 10) {
        $coins = $this->listCoins($limit, 1, $currency, 'price_change_percentage_24h_desc');
        return $coins ? array_slice($coins, 0, $limit) : null;
    }
    
    /**
     * Get top losers
     */
    public function getTopLosers($currency = 'usd', $limit = 10) {
        $coins = $this->listCoins($limit, 1, $currency, 'price_change_percentage_24h_asc');
        return $coins ? array_slice($coins, 0, $limit) : null;
    }
    
    /**
     * Get global market data
     */
    public function getMarketData() {
        $cacheKey = 'market_data';
        
        if ($this->isCacheValid($cacheKey)) {
            return $this->cache[$cacheKey]['data'];
        }
        
        $url = $this->apiUrl . 'global';
        $data = $this->httpManager->get($url);
        
        if ($data) {
            $this->cache[$cacheKey] = [
                'data' => $data,
                'time' => time()
            ];
        }
        
        return $data;
    }
    
    /**
     * Get multiple coin prices
     */
    public function getMultiplePrices($coinIds, $currency = 'usd', $includeMarketCap = false, $include24hrVol = false, $include24hrChange = false) {
        $ids = is_array($coinIds) ? implode(',', $coinIds) : $coinIds;
        $params = ['ids=' . $ids, 'vs_currencies=' . $currency];
        
        if ($includeMarketCap) $params[] = 'include_market_cap=true';
        if ($include24hrVol) $params[] = 'include_24hr_vol=true';
        if ($include24hrChange) $params[] = 'include_24hr_change=true';
        
        $url = $this->apiUrl . 'simple/price?' . implode('&', $params);
        return $this->httpManager->get($url);
    }
    
    /**
     * Get coin icon/image
     */
    public function getCoinIcon($coinId, $size = 'large') {
        $coin = $this->getCoin($coinId);
        
        if (!$coin || !isset($coin['image'])) {
            return null;
        }
        
        switch ($size) {
            case 'small':
                return $coin['image']['small'];
            case 'thumb':
                return $coin['image']['thumb'];
            case 'large':
            default:
                return $coin['image']['large'];
        }
    }
    
    /**
     * Format price with currency symbol
     */
    public function formatPrice($price, $currency = 'usd') {
        $symbols = [
            'usd' => '$',
            'eur' => '€',
            'gbp' => '£',
            'jpy' => '¥',
            'try' => '₺',
            'btc' => '₿',
            'eth' => 'Ξ'
        ];
        
        $symbol = $symbols[strtolower($currency)] ?? strtoupper($currency) . ' ';
        return $symbol . number_format($price, 2);
    }
    
    /**
     * Check if cache is valid
     */
    private function isCacheValid($key) {
        return isset($this->cache[$key]) && 
               (time() - $this->cache[$key]['time']) < $this->cacheTime;
    }
    
    /**
     * Clear cache
     */
    public function clearCache() {
        $this->cache = [];
    }
    
    /**
     * Set cache time
     */
    public function setCacheTime($seconds) {
        $this->cacheTime = $seconds;
    }
    
    /**
     * Get cache statistics
     */
    public function getCacheStats() {
        return [
            'entries' => count($this->cache),
            'cache_time' => $this->cacheTime,
            'memory_usage' => memory_get_usage(true)
        ];
    }
}
?>