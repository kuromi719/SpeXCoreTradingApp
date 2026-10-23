<?php
require __DIR__ . '/../../system/spex_v4_func.php';

header('Content-Type: application/json');

if (!isset($_SESSION['adminlog']) || $_SESSION['adminlog'] !== 'appr') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch($action) {
        case 'getTables':
            getTables();
            break;
        case 'getTableData':
            getTableData();
            break;
        case 'getRecord':
            getRecord();
            break;
        case 'insertRecord':
            insertRecord();
            break;
        case 'updateRecord':
            updateRecord();
            break;
        case 'deleteRecord':
            deleteRecord();
            break;
        case 'exportTable':
            exportTable();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>