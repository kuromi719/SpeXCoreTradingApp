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
        case 'getStats':
            $stats = getLoginStats();
            $response['success'] = true;
            $response['stats'] = $stats;
            break;
            
        case 'getLoginHistory':
            $limit = intval($_POST['limit'] ?? 50);
            $offset = intval($_POST['offset'] ?? 0);
            $search = Sanitize($_POST['search'] ?? '');
            $status = Sanitize($_POST['status'] ?? '');
            $device = Sanitize($_POST['device'] ?? '');
            $dateFrom = Sanitize($_POST['date_from'] ?? '');
            $dateTo = Sanitize($_POST['date_to'] ?? '');
            
            $result = getLoginHistory($limit, $offset, $search, $status, $device, $dateFrom, $dateTo);
            $response['success'] = true;
            $response['history'] = $result['history'];
            $response['total'] = $result['total'];
            break;
            
        case 'getLoginDetails':
            $id = intval($_POST['id']);
            $details = getLoginDetails($id);
            if ($details) {
                $response['success'] = true;
                $response['record'] = $details['record'];
                $response['user_info'] = $details['user_info'];
            } else {
                $response['message'] = 'Login record not found';
            }
            break;
            
        case 'deleteRecord':
            $id = intval($_POST['id']);
            if (deleteLoginRecord($id)) {
                $response['success'] = true;
                $response['message'] = 'Login record deleted successfully';
            } else {
                $response['message'] = 'Failed to delete login record';
            }
            break;
            
        case 'clearOldHistory':
            $deletedCount = clearOldLoginHistory();
            $response['success'] = true;
            $response['deleted_count'] = $deletedCount;
            $response['message'] = 'Old login history cleared successfully';
            break;
            
        case 'exportCSV':
            exportLoginHistoryCSV();
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