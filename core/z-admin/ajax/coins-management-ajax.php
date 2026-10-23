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
        case 'getCronStatus':
            $cronStatus = getCronStatus();
            $response['success'] = true;
            $response['last_update'] = $cronStatus;
            break;
            
        case 'getStats':
            $stats = getCoinStats();
            $response['success'] = true;
            $response['stats'] = $stats;
            break;
            
        case 'getCoins':
            $limit = intval($_POST['limit'] ?? 50);
            $offset = intval($_POST['offset'] ?? 0);
            $search = Sanitize($_POST['search'] ?? '');
            $rank = Sanitize($_POST['rank'] ?? '');
            $change = Sanitize($_POST['change'] ?? '');
            $image = $_POST['image'] ?? '';
            
            $result = getCoins($limit, $offset, $search, $rank, $change, $image);
            $response['success'] = true;
            $response['coins'] = $result['coins'];
            $response['total'] = $result['total'];
            break;
            
        case 'getCoinDetails':
            $coinId = Sanitize($_POST['coin_id']);
            $coin = getCoinDetails($coinId);
            if ($coin) {
                $response['success'] = true;
                $response['coin'] = $coin;
            } else {
                $response['message'] = 'Coin not found';
            }
            break;
            
        case 'createCoin':
            $coinData = getCoinDataFromPost();
            if (createCoin($coinData)) {
                $response['success'] = true;
                $response['message'] = 'Coin created successfully';
            } else {
                $response['message'] = 'Failed to create coin';
            }
            break;
            
        case 'updateCoin':
            $originalId = Sanitize($_POST['original_id']);
            $coinData = getCoinDataFromPost();
            if (updateCoin($originalId, $coinData)) {
                $response['success'] = true;
                $response['message'] = 'Coin updated successfully';
            } else {
                $response['message'] = 'Failed to update coin';
            }
            break;
            
        case 'deleteCoin':
            $coinId = Sanitize($_POST['coin_id']);
            if (deleteCoin($coinId)) {
                $response['success'] = true;
                $response['message'] = 'Coin deleted successfully';
            } else {
                $response['message'] = 'Failed to delete coin';
            }
            break;
            
        case 'syncCoin':
            $coinId = Sanitize($_POST['coin_id']);
            if (syncSingleCoin($coinId)) {
                $response['success'] = true;
                $response['message'] = 'Coin synced successfully';
            } else {
                $response['message'] = 'Failed to sync coin';
            }
            break;
            
        case 'syncAllCoins':
            $updatedCount = syncAllCoins();
            $response['success'] = true;
            $response['updated_count'] = $updatedCount;
            break;
            
        case 'exportCSV':
            exportCoinsCSV();
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