<?php
/*
 * SpeX MarketManager - Advanced Crypto Market Data Operations
 * Specialized database operations for cryptocurrency data with filtering, sorting, and analytics
*/

require_once __DIR__ . '/../../system/spex_v4_func.php';
require_once __DIR__ . '/../../utils/CRUDManager.php';

class MarketManager {
    
    private $crud;
    
    public function __construct() {
        $this->crud = new CRUDManager();
    }
    
    // ==================== MARKET DATA RETRIEVAL ====================
    
    /**
     * Get top coins by market capitalization with filtering
     * @param int $limit Number of coins to return
     * @param int $offset Pagination offset
     * @param array $filters Additional filters
     * @return array
     */
    public function getTopCoins($limit = 100, $offset = 0, $filters = []) {
        $whereClause = "cmd.market_cap_rank > 0";
        $params = [];
        
        if (isset($filters['min_price'])) {
            $whereClause .= " AND cmd.current_price_usd >= ?";
            $params[] = $filters['min_price'];
        }
        
        if (isset($filters['max_price'])) {
            $whereClause .= " AND cmd.current_price_usd <= ?";
            $params[] = $filters['max_price'];
        }
        
        if (isset($filters['min_market_cap'])) {
            $whereClause .= " AND cmd.market_cap_usd >= ?";
            $params[] = $filters['min_market_cap'];
        }
        
        if (isset($filters['categories'])) {
            $categories = is_array($filters['categories']) ? $filters['categories'] : [$filters['categories']];
            $categoryPlaceholders = str_repeat('?,', count($categories) - 1) . '?';
            $whereClause .= " AND JSON_OVERLAPS(c.categories, JSON_ARRAY($categoryPlaceholders))";
            $params = array_merge($params, $categories);
        }
        
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->crud->query("
            SELECT 
                c.id,
                c.name,
                c.symbol,
                c.description,
                c.categories,
                cmd.current_price_usd,
                cmd.market_cap_usd,
                cmd.market_cap_rank,
                cmd.price_change_percentage_24h,
                cmd.price_change_percentage_7d,
                cmd.price_change_percentage_30d,
                cmd.total_volume_usd,
                cmd.high_24h_usd,
                cmd.low_24h_usd,
                cmd.circulating_supply,
                cmd.max_supply,
                ci.small as logo,
                ci.large as large_logo,
                cs.coingecko_rank,
                cs.coingecko_score
            FROM coins c
            JOIN coin_market_data cmd ON c.id = cmd.coin_id
            LEFT JOIN coin_images ci ON c.id = ci.coin_id
            LEFT JOIN coin_scores cs ON c.id = cs.coin_id
            WHERE $whereClause
            ORDER BY cmd.market_cap_rank ASC
            LIMIT ? OFFSET ?
        ", $params);
    }
    
    /**
     * Get trending coins (highest gainers in 24h)
     * @param int $limit Number of coins
     * @param float $minVolume Minimum 24h volume filter
     * @return array
     */
    public function getTrendingCoins($limit = 50, $minVolume = 1000000) {
        return $this->crud->query("
            SELECT 
                c.id,
                c.name,
                c.symbol,
                cmd.current_price_usd,
                cmd.price_change_percentage_24h,
                cmd.price_change_percentage_7d,
                cmd.total_volume_usd,
                cmd.market_cap_rank,
                ci.small as logo
            FROM coins c
            JOIN coin_market_data cmd ON c.id = cmd.coin_id
            LEFT JOIN coin_images ci ON c.id = ci.coin_id
            WHERE cmd.price_change_percentage_24h > 0 
            AND cmd.total_volume_usd >= ?
            AND cmd.market_cap_rank <= 1000
            ORDER BY cmd.price_change_percentage_24h DESC
            LIMIT ?
        ", [$minVolume, $limit]);
    }
    
    /**
     * Get biggest losers (highest decline in 24h)
     * @param int $limit Number of coins
     * @param float $minVolume Minimum 24h volume filter
     * @return array
     */
    public function getBiggestLosers($limit = 50, $minVolume = 1000000) {
        return $this->crud->query("
            SELECT 
                c.id,
                c.name,
                c.symbol,
                cmd.current_price_usd,
                cmd.price_change_percentage_24h,
                cmd.price_change_percentage_7d,
                cmd.total_volume_usd,
                cmd.market_cap_rank,
                ci.small as logo
            FROM coins c
            JOIN coin_market_data cmd ON c.id = cmd.coin_id
            LEFT JOIN coin_images ci ON c.id = ci.coin_id
            WHERE cmd.price_change_percentage_24h < 0 
            AND cmd.total_volume_usd >= ?
            AND cmd.market_cap_rank <= 1000
            ORDER BY cmd.price_change_percentage_24h ASC
            LIMIT ?
        ", [$minVolume, $limit]);
    }
    
    /**
     * Get highest volume coins for the day
     * @param int $limit Number of coins
     * @param int $maxRank Maximum market cap rank filter
     * @return array
     */
    public function getHighestVolumeCoins($limit = 50, $maxRank = 500) {
        return $this->crud->query("
            SELECT 
                c.id,
                c.name,
                c.symbol,
                cmd.current_price_usd,
                cmd.total_volume_usd,
                cmd.market_cap_usd,
                cmd.price_change_percentage_24h,
                cmd.market_cap_rank,
                ci.small as logo
            FROM coins c
            JOIN coin_market_data cmd ON c.id = cmd.coin_id
            LEFT JOIN coin_images ci ON c.id = ci.coin_id
            WHERE cmd.total_volume_usd > 0 
            AND cmd.market_cap_rank <= ?
            ORDER BY cmd.total_volume_usd DESC
            LIMIT ?
        ", [$maxRank, $limit]);
    }
    
    /**
     * Get coins by price range with advanced filtering
     * @param float $minPrice Minimum price
     * @param float $maxPrice Maximum price
     * @param int $limit Number of coins
     * @param string $sortBy Sort criteria
     * @return array
     */
    public function getCoinsByPriceRange($minPrice, $maxPrice, $limit = 100, $sortBy = 'market_cap_rank') {
        $allowedSorts = [
            'market_cap_rank' => 'cmd.market_cap_rank ASC',
            'price_high' => 'cmd.current_price_usd DESC',
            'price_low' => 'cmd.current_price_usd ASC',
            'volume' => 'cmd.total_volume_usd DESC',
            'change_24h' => 'cmd.price_change_percentage_24h DESC'
        ];
        
        $orderBy = $allowedSorts[$sortBy] ?? $allowedSorts['market_cap_rank'];
        
        return $this->crud->query("
            SELECT 
                c.id,
                c.name,
                c.symbol,
                cmd.current_price_usd,
                cmd.market_cap_usd,
                cmd.market_cap_rank,
                cmd.price_change_percentage_24h,
                cmd.total_volume_usd,
                ci.small as logo
            FROM coins c
            JOIN coin_market_data cmd ON c.id = cmd.coin_id
            LEFT JOIN coin_images ci ON c.id = ci.coin_id
            WHERE cmd.current_price_usd BETWEEN ? AND ?
            AND cmd.market_cap_rank > 0
            ORDER BY $orderBy
            LIMIT ?
        ", [$minPrice, $maxPrice, $limit]);
    }
    
    // ==================== SEARCH & DISCOVERY ====================
    
    /**
     * Advanced coin search with multiple criteria
     * @param string $query Search term (name or symbol)
     * @param array $filters Additional search filters
     * @param int $limit Number of results
     * @return array
     */
    public function searchCoins($query, $filters = [], $limit = 50) {
        $whereClause = "(c.name LIKE ? OR c.symbol LIKE ?)";
        $params = ["%$query%", "%$query%"];
        
        if (isset($filters['min_market_cap_rank'])) {
            $whereClause .= " AND cmd.market_cap_rank >= ?";
            $params[] = $filters['min_market_cap_rank'];
        }
        
        if (isset($filters['max_market_cap_rank'])) {
            $whereClause .= " AND cmd.market_cap_rank <= ?";
            $params[] = $filters['max_market_cap_rank'];
        }
        
        if (isset($filters['has_description']) && $filters['has_description']) {
            $whereClause .= " AND c.description IS NOT NULL AND c.description != ''";
        }
        
        $params[] = $limit;
        
        return $this->crud->query("
            SELECT 
                c.id,
                c.name,
                c.symbol,
                c.description,
                cmd.current_price_usd,
                cmd.market_cap_rank,
                cmd.price_change_percentage_24h,
                cmd.total_volume_usd,
                ci.small as logo,
                cs.coingecko_score
            FROM coins c
            LEFT JOIN coin_market_data cmd ON c.id = cmd.coin_id
            LEFT JOIN coin_images ci ON c.id = ci.coin_id
            LEFT JOIN coin_scores cs ON c.id = cs.coin_id
            WHERE $whereClause
            ORDER BY cmd.market_cap_rank ASC
            LIMIT ?
        ", $params);
    }
    
    /**
     * Get coins by category
     * @param string $category Category name
     * @param int $limit Number of coins
     * @param string $sortBy Sort criteria
     * @return array
     */
    public function getCoinsByCategory($category, $limit = 100, $sortBy = 'market_cap_rank') {
        $allowedSorts = [
            'market_cap_rank' => 'cmd.market_cap_rank ASC',
            'volume' => 'cmd.total_volume_usd DESC',
            'change_24h' => 'cmd.price_change_percentage_24h DESC',
            'price' => 'cmd.current_price_usd DESC'
        ];
        
        $orderBy = $allowedSorts[$sortBy] ?? $allowedSorts['market_cap_rank'];
        
        return $this->crud->query("
            SELECT 
                c.id,
                c.name,
                c.symbol,
                c.categories,
                cmd.current_price_usd,
                cmd.market_cap_usd,
                cmd.market_cap_rank,
                cmd.price_change_percentage_24h,
                cmd.total_volume_usd,
                ci.small as logo
            FROM coins c
            JOIN coin_market_data cmd ON c.id = cmd.coin_id
            LEFT JOIN coin_images ci ON c.id = ci.coin_id
            WHERE JSON_CONTAINS(c.categories, JSON_QUOTE(?))
            AND cmd.market_cap_rank > 0
            ORDER BY $orderBy
            LIMIT ?
        ", [$category, $limit]);
    }
    
    /**
     * Get similar coins based on market cap and category
     * @param string $coinId Reference coin ID
     * @param int $limit Number of similar coins
     * @return array
     */
    public function getSimilarCoins($coinId, $limit = 10) {
        $refCoin = $this->crud->queryOne("
            SELECT cmd.market_cap_usd, c.categories 
            FROM coins c 
            JOIN coin_market_data cmd ON c.id = cmd.coin_id 
            WHERE c.id = ?
        ", [$coinId]);
        
        if (!$refCoin) return [];
        
        $marketCapRange = $refCoin['market_cap_usd'] * 0.5;
        $minCap = $refCoin['market_cap_usd'] - $marketCapRange;
        $maxCap = $refCoin['market_cap_usd'] + $marketCapRange;
        
        return $this->crud->query("
            SELECT 
                c.id,
                c.name,
                c.symbol,
                cmd.current_price_usd,
                cmd.market_cap_usd,
                cmd.market_cap_rank,
                cmd.price_change_percentage_24h,
                ci.small as logo
            FROM coins c
            JOIN coin_market_data cmd ON c.id = cmd.coin_id
            LEFT JOIN coin_images ci ON c.id = ci.coin_id
            WHERE c.id != ?
            AND cmd.market_cap_usd BETWEEN ? AND ?
            AND cmd.market_cap_rank > 0
            ORDER BY ABS(cmd.market_cap_usd - ?) ASC
            LIMIT ?
        ", [$coinId, $minCap, $maxCap, $refCoin['market_cap_usd'], $limit]);
    }
    
    // ==================== MARKET ANALYTICS ====================
    
    /**
     * Get market overview statistics
     * @return array
     */
    public function getMarketOverview() {
        return $this->crud->queryOne("
            SELECT 
                COUNT(*) as total_coins,
                SUM(cmd.market_cap_usd) as total_market_cap,
                SUM(cmd.total_volume_usd) as total_volume_24h,
                AVG(cmd.price_change_percentage_24h) as avg_change_24h,
                COUNT(CASE WHEN cmd.price_change_percentage_24h > 0 THEN 1 END) as gainers_count,
                COUNT(CASE WHEN cmd.price_change_percentage_24h < 0 THEN 1 END) as losers_count,
                MAX(cmd.price_change_percentage_24h) as biggest_gainer,
                MIN(cmd.price_change_percentage_24h) as biggest_loser
            FROM coin_market_data cmd
            WHERE cmd.market_cap_rank > 0 AND cmd.market_cap_rank <= 1000
        ");
    }
    
    /**
     * Get performance leaders across different timeframes
     * @param string $timeframe Time period (24h, 7d, 30d)
     * @param int $limit Number of coins per category
     * @return array
     */
    public function getPerformanceLeaders($timeframe = '24h', $limit = 10) {
        $columnMap = [
            '24h' => 'price_change_percentage_24h',
            '7d' => 'price_change_percentage_7d',
            '30d' => 'price_change_percentage_30d'
        ];
        
        $column = $columnMap[$timeframe] ?? $columnMap['24h'];
        
        $gainers = $this->crud->query("
            SELECT 
                c.id, c.name, c.symbol,
                cmd.current_price_usd,
                cmd.$column as change_percent,
                cmd.market_cap_rank,
                ci.small as logo
            FROM coins c
            JOIN coin_market_data cmd ON c.id = cmd.coin_id
            LEFT JOIN coin_images ci ON c.id = ci.coin_id
            WHERE cmd.$column > 0 AND cmd.market_cap_rank <= 500
            ORDER BY cmd.$column DESC
            LIMIT ?
        ", [$limit]);
        
        $losers = $this->crud->query("
            SELECT 
                c.id, c.name, c.symbol,
                cmd.current_price_usd,
                cmd.$column as change_percent,
                cmd.market_cap_rank,
                ci.small as logo
            FROM coins c
            JOIN coin_market_data cmd ON c.id = cmd.coin_id
            LEFT JOIN coin_images ci ON c.id = ci.coin_id
            WHERE cmd.$column < 0 AND cmd.market_cap_rank <= 500
            ORDER BY cmd.$column ASC
            LIMIT ?
        ", [$limit]);
        
        return [
            'timeframe' => $timeframe,
            'gainers' => $gainers,
            'losers' => $losers
        ];
    }
    
    /**
     * Get market cap distribution analysis
     * @return array
     */
    public function getMarketCapDistribution() {
        return $this->crud->query("
            SELECT 
                CASE 
                    WHEN cmd.market_cap_usd >= 10000000000 THEN 'Large Cap (10B+)'
                    WHEN cmd.market_cap_usd >= 1000000000 THEN 'Mid Cap (1B-10B)'
                    WHEN cmd.market_cap_usd >= 100000000 THEN 'Small Cap (100M-1B)'
                    ELSE 'Micro Cap (<100M)'
                END as cap_category,
                COUNT(*) as coin_count,
                SUM(cmd.market_cap_usd) as total_cap,
                AVG(cmd.price_change_percentage_24h) as avg_change_24h
            FROM coin_market_data cmd
            WHERE cmd.market_cap_rank > 0 AND cmd.market_cap_usd > 0
            GROUP BY cap_category
            ORDER BY total_cap DESC
        ");
    }
    
    // ==================== SPECIALIZED QUERIES ====================
    
    /**
     * Get new listings (coins with recent market cap rank)
     * @param int $maxRank Maximum rank to consider as "new"
     * @param int $limit Number of coins
     * @return array
     */
    public function getNewListings($maxRank = 1000, $limit = 20) {
        return $this->crud->query("
            SELECT 
                c.id,
                c.name,
                c.symbol,
                c.genesis_date,
                cmd.current_price_usd,
                cmd.market_cap_usd,
                cmd.market_cap_rank,
                cmd.price_change_percentage_24h,
                cmd.total_volume_usd,
                ci.small as logo
            FROM coins c
            JOIN coin_market_data cmd ON c.id = cmd.coin_id
            LEFT JOIN coin_images ci ON c.id = ci.coin_id
            WHERE cmd.market_cap_rank > 0 
            AND cmd.market_cap_rank <= ?
            AND c.genesis_date >= DATE_SUB(NOW(), INTERVAL 90 DAY)
            ORDER BY c.genesis_date DESC
            LIMIT ?
        ", [$maxRank, $limit]);
    }
    
    /**
     * Get undervalued coins (high scores, low market cap)
     * @param float $maxMarketCap Maximum market cap filter
     * @param float $minScore Minimum CoinGecko score
     * @param int $limit Number of coins
     * @return array
     */
    public function getUndervaluedCoins($maxMarketCap = 1000000000, $minScore = 30, $limit = 50) {
        return $this->crud->query("
            SELECT 
                c.id,
                c.name,
                c.symbol,
                cmd.current_price_usd,
                cmd.market_cap_usd,
                cmd.market_cap_rank,
                cmd.price_change_percentage_24h,
                cs.coingecko_score,
                cs.developer_score,
                cs.community_score,
                ci.small as logo
            FROM coins c
            JOIN coin_market_data cmd ON c.id = cmd.coin_id
            JOIN coin_scores cs ON c.id = cs.coin_id
            LEFT JOIN coin_images ci ON c.id = ci.coin_id
            WHERE cmd.market_cap_usd <= ?
            AND cs.coingecko_score >= ?
            AND cmd.market_cap_rank > 0
            ORDER BY (cs.coingecko_score / (cmd.market_cap_usd / 1000000)) DESC
            LIMIT ?
        ", [$maxMarketCap, $minScore, $limit]);
    }
    
    /**
     * Get coins with highest development activity
     * @param float $minDevScore Minimum developer score
     * @param int $limit Number of coins
     * @return array
     */
    public function getHighDevActivityCoins($minDevScore = 50, $limit = 30) {
        return $this->crud->query("
            SELECT 
                c.id,
                c.name,
                c.symbol,
                c.repos_url,
                cmd.current_price_usd,
                cmd.market_cap_rank,
                cmd.price_change_percentage_24h,
                cs.developer_score,
                cs.coingecko_score,
                ci.small as logo
            FROM coins c
            JOIN coin_scores cs ON c.id = cs.coin_id
            JOIN coin_market_data cmd ON c.id = cmd.coin_id
            LEFT JOIN coin_images ci ON c.id = ci.coin_id
            WHERE cs.developer_score >= ?
            AND cmd.market_cap_rank > 0
            ORDER BY cs.developer_score DESC
            LIMIT ?
        ", [$minDevScore, $limit]);
    }
    
    // ==================== UTILITY FUNCTIONS ====================
    
    /**
     * Get all available categories
     * @return array
     */
    public function getAvailableCategories() {
        $categories = $this->crud->query("
            SELECT DISTINCT JSON_UNQUOTE(JSON_EXTRACT(c.categories, CONCAT('$[', numbers.n, ']'))) as category
            FROM coins c
            JOIN (
                SELECT 0 as n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 
                UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9
            ) numbers
            WHERE JSON_EXTRACT(c.categories, CONCAT('$[', numbers.n, ']')) IS NOT NULL
            AND JSON_EXTRACT(c.categories, CONCAT('$[', numbers.n, ']')) != 'null'
            ORDER BY category
        ");
        
        return array_column($categories, 'category');
    }
    
    /**
     * Format large numbers for display
     * @param float $number
     * @param int $decimals
     * @return string
     */
    public function formatLargeNumber($number, $decimals = 1) {
        if ($number >= 1000000000000) {
            return number_format($number / 1000000000000, $decimals) . 'T';
        } elseif ($number >= 1000000000) {
            return number_format($number / 1000000000, $decimals) . 'B';
        } elseif ($number >= 1000000) {
            return number_format($number / 1000000, $decimals) . 'M';
        } elseif ($number >= 1000) {
            return number_format($number / 1000, $decimals) . 'K';
        } else {
            return number_format($number, $decimals);
        }
    }
    
    /**
     * Get formatted price change with color indicator
     * @param float $change
     * @return array
     */
    public function getPriceChangeFormatted($change) {
        return [
            'value' => $change,
            'formatted' => ($change >= 0 ? '+' : '') . number_format($change, 2) . '%',
            'color' => $change >= 0 ? 'green' : 'red',
            'icon' => $change >= 0 ? '↗' : '↘'
        ];
    }
    
    /**
     * Get coin count by filters
     * @param array $filters
     * @return int
     */
    public function getCoinCount($filters = []) {
        $whereClause = "cmd.market_cap_rank > 0";
        $params = [];
        
        if (isset($filters['min_price'])) {
            $whereClause .= " AND cmd.current_price_usd >= ?";
            $params[] = $filters['min_price'];
        }
        
        if (isset($filters['max_price'])) {
            $whereClause .= " AND cmd.current_price_usd <= ?";
            $params[] = $filters['max_price'];
        }
        
        $result = $this->crud->queryOne("
            SELECT COUNT(*) as total
            FROM coins c
            JOIN coin_market_data cmd ON c.id = cmd.coin_id
            WHERE $whereClause
        ", $params);
        
        return $result['total'] ?? 0;
    }
}
?>