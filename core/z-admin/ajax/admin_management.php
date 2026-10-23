<?php
require __DIR__ . '/../../system/spex_v4_func.php';

header('Content-Type: application/json');

if (!isset($_SESSION['adminlog']) || $_SESSION['adminlog'] !== 'appr') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    switch($action) {
        case 'getAdmins':
            getAdmins();
            break;
        case 'getAdmin':
            getAdmin();
            break;
        case 'createAdmin':
            createAdmin();
            break;
        case 'updateAdmin':
            updateAdmin();
            break;
        case 'deleteAdmin':
            deleteAdmin();
            break;
        case 'getAvailablePages':
            getAvailablePages();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

?>