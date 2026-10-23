<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';
require_once __DIR__ . '/../../utils/TwoFactorManager.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['code'])) {
    echo json_encode(['success' => false, 'message' => '2FA code required.']);
    exit;
}

$code = trim($_POST['code']);

try {
    $WebData = FetchWebSettings();
    $twoFA = new TwoFactorManager($WebData['BaseName']);
    $result = $twoFA->verifyCode($UserUID, $code);
    if ($result['message']=="Code verified") {
        setSession('LoginVia2Fa', '0');
    }
    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
