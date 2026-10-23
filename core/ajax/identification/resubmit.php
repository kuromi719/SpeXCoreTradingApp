<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../system/spex_v4_func.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    // Check if user has a rejected verification
    $checkQuery = $spexcon->prepare("SELECT status FROM user_identifications WHERE user_id = ? AND status = 'REJECTED'");
    $checkQuery->bind_param("i", $UserUID);
    $checkQuery->execute();
    $result = $checkQuery->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'No rejected verification found']);
        exit;
    }
    
    // Delete the rejected record to allow resubmission
    $deleteQuery = $spexcon->prepare("DELETE FROM user_identifications WHERE user_id = ? AND status = 'REJECTED'");
    $deleteQuery->bind_param("i", $UserUID);
    $deleteQuery->execute();
    
    echo json_encode([
        'success' => true,
        'message' => 'You can now resubmit your identity verification'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>