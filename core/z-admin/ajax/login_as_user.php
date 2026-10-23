<?php
require __DIR__ . '/../../system/spex_v4_func.php';

header('Content-Type: text/html');

if (!isset($_SESSION['adminlog']) || $_SESSION['adminlog'] !== 'appr') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$userId = $_POST['userId'];
$SecureAdmin = getSession('SecureAdmin');

if (empty($userId)) {
    echo json_encode(['success' => false, 'message' => 'Invalid UserUID']);
}

if (empty($SecureAdmin)) {
    echo json_encode(['success' => false, 'message' => 'SecureAdmin Data Corrupted!']);
}

setSession('AdminReque',$SecureAdmin);
setSession('RequestedUser',$userId);

echo json_encode(['success' => true, 'message' => 'Sessions Created.']);

?>