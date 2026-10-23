<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $spexcon->query("UPDATE users SET `disablead`=1 WHERE uid='$UserUID'");
    
    echo json_encode([
        'success' => true
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false
    ]);
}
?>