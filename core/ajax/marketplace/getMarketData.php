<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';
require_once __DIR__ . '/../../api/crypto/MarketManager.php';

header('Content-Type: application/json');

try {
    $market = new MarketManager();
    
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = max(10, min(100, (int)($_GET['limit'] ?? 50)));
    $offset = ($page - 1) * $limit;
    
    $filters = [
        'category' => $_GET['category'] ?? '',
        'search' => $_GET['search'] ?? '',
        'sort' => $_GET['sort'] ?? 'market_cap',
        'min_price' => $_GET['minPrice'] ?? '',
        'max_price' => $_GET['maxPrice'] ?? ''
    ];
    
    $searchFilters = [];
    
    if (!empty($filters['min_price']) && is_numeric($filters['min_price'])) {
        $searchFilters['min_price'] = (float)$filters['min_price'];
    }
    
    if (!empty($filters['max_price']) && is_numeric($filters['max_price'])) {
        $searchFilters['max_price'] = (float)$filters['max_price'];
    }
    
    if (!empty($filters['category'])) {
        $searchFilters['categories'] = [$filters['category']];
    }
    
    if (!empty($filters['search'])) {
        $coins = $market->searchCoins($filters['search'], $searchFilters, $limit);
        $totalCount = count($coins);
    } else {
        $coins = $market->getTopCoins($limit, $offset, $searchFilters);
        $totalCount = $market->getCoinCount($searchFilters);
    }
    
    if (!empty($filters['sort']) && $filters['sort'] !== 'market_cap') {
        switch ($filters['sort']) {
            case 'volume':
                usort($coins, function($a, $b) {
                    return $b['total_volume_usd'] <=> $a['total_volume_usd'];
                });
                break;
            case 'price_high':
                usort($coins, function($a, $b) {
                    return $b['current_price_usd'] <=> $a['current_price_usd'];
                });
                break;
            case 'price_low':
                usort($coins, function($a, $b) {
                    return $a['current_price_usd'] <=> $b['current_price_usd'];
                });
                break;
            case 'change_24h':
                usort($coins, function($a, $b) {
                    return $b['price_change_percentage_24h'] <=> $a['price_change_percentage_24h'];
                });
                break;
        }
    }
    
    $totalPages = ceil($totalCount / $limit);
    
    echo json_encode([
        'success' => true,
        'data' => $coins,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalCount,
            'items_per_page' => $limit,
            'has_next' => $page < $totalPages,
            'has_prev' => $page > 1
        ],
        'filters' => $filters
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>