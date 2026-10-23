<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';
require_once __DIR__ . '/../../utils/TwoFactorManager.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $WebData = FetchWebSettings();
    $twoFA = new TwoFactorManager($WebData['BaseName']);
    $result = $twoFA->disable2FA($UserUID);
    
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>