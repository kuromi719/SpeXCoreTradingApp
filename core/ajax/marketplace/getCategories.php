<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';
require_once __DIR__ . '/../../api/crypto/MarketManager.php';

header('Content-Type: application/json');

try {
    $market = new MarketManager();
    
    $categories = $market->getAvailableCategories();
    
    $popularCategories = [
        'layer-1',
        'defi',
        'smart-contracts',
        'ethereum-ecosystem',
        'binance-smart-chain',
        'decentralized-exchange',
        'yield-farming',
        'nft',
        'gaming',
        'metaverse',
        'meme-token',
        'stablecoins',
        'privacy-coins',
        'oracle',
        'storage'
    ];
    
    $filteredCategories = array_filter($categories, function($category) use ($popularCategories) {
        return in_array($category, $popularCategories) || 
               strlen($category) <= 20 && 
               !empty($category) && 
               $category !== 'null';
    });
    
    sort($filteredCategories);
    
    echo json_encode([
        'success' => true,
        'data' => array_values($filteredCategories),
        'total_count' => count($filteredCategories)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'data' => []
    ]);
}
?>