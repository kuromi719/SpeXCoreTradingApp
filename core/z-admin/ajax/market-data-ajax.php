<?php
require __DIR__ . '/../../system/spex_v4_func.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['action']) && !isset($_GET['action'])) {
        throw new Exception('Action not specified');
    }
    
    $action = $_POST['action'] ?? $_GET['action'];
    $response = array('success' => false);
    
    switch ($action) {
        case 'getLastSync':
            $lastSync = getLastSync();
            $response['success'] = true;
            $response['last_sync'] = $lastSync;
            break;
            
        case 'getMarketStats':
            $stats = getMarketStats();
            $response['success'] = true;
            $response['stats'] = $stats;
            break;
            
        case 'getTopGainersLosers':
            $gainersLosers = getTopGainersLosers();
            $response['success'] = true;
            $response['gainers'] = $gainersLosers['gainers'];
            $response['losers'] = $gainersLosers['losers'];
            break;
            
        case 'getMarketData':
            $limit = intval($_POST['limit'] ?? 50);
            $offset = intval($_POST['offset'] ?? 0);
            $search = Sanitize($_POST['search'] ?? '');
            $rank = Sanitize($_POST['rank'] ?? '');
            $price = Sanitize($_POST['price'] ?? '');
            $change = Sanitize($_POST['change'] ?? '');
            $sortBy = Sanitize($_POST['sort_by'] ?? 'market_cap_rank');
            $sortOrder = Sanitize($_POST['sort_order'] ?? 'asc');
            
            $result = getMarketData($limit, $offset, $search, $rank, $price, $change, $sortBy, $sortOrder);
            $response['success'] = true;
            $response['data'] = $result['data'];
            $response['total'] = $result['total'];
            break;
            
        case 'getCoinDetails':
            $coinId = Sanitize($_POST['coin_id']);
            $coin = getCoinFullDetails($coinId);
            if ($coin) {
                $response['success'] = true;
                $response['coin'] = $coin;
            } else {
                $response['message'] = 'Coin not found';
            }
            break;
            
        case 'refreshData':
            $refreshed = refreshMarketData();
            $response['success'] = true;
            $response['refreshed_count'] = $refreshed;
            break;
            
        case 'exportCSV':
            exportMarketDataCSV();
            exit;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>