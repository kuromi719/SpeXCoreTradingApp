<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

try {
    $newPassword = $input['newPassword'];
    
    if (empty($newPassword)) {
        echo json_encode(['success' => false, 'message' => 'No valid fields']);
        exit;
    }

    $hashedPass = md5($newPassword);
    $spexcon->query("UPDATE users SET `pass`='$hashedPass' WHERE uid='$UserUID'");
    
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>