<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

if (!getSession('Login')) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$UserUID = getSession('UID');

try {
    $balanceQuery = "SELECT balance FROM users WHERE uid = ?";
    $stmt = $spexcon->prepare($balanceQuery);
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    echo json_encode([
        'success' => true,
        'balance' => $result['balance'] ?? 0
    ]);

} catch (Exception $e) {
    error_log("Balance Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error loading balance'
    ]);
}
?>