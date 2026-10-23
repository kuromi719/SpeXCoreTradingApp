<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';
require_once __DIR__ . '/../../utils/TwoFactorManager.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

if (empty($input['code'])) {
    echo json_encode(['success' => false, 'message' => 'Verification code is required']);
    exit;
}

try {
    $WebData = FetchWebSettings();
    $twoFA = new TwoFactorManager($WebData['BaseName']);
    $result = $twoFA->verifyAndActivate($UserUID, $input['code']);
    
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>