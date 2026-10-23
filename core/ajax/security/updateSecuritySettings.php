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
    $updateFields = [];
    $params = [];
    $types = "";
    
    $allowedFields = [
        'no_data_sharing' => 'i',
        'marketing_emails' => 'i',
        'analytics_cookies' => 'i',
        'login_notifications' => 'i',
        'data_retention' => 'i'
    ];
    
    foreach ($input as $field => $value) {
        if (isset($allowedFields[$field])) {
            $updateFields[] = "$field = ?";
            $params[] = $value;
            $types .= $allowedFields[$field];
        }
    }
    
    if (empty($updateFields)) {
        echo json_encode(['success' => false, 'message' => 'No valid fields']);
        exit;
    }
    
    $params[] = $UserUID;
    $types .= "i";
    
    $updateQuery = $spexcon->prepare("
        UPDATE user_security_settings 
        SET " . implode(', ', $updateFields) . ", updated_at = NOW() 
        WHERE user_id = ?
    ");
    $updateQuery->bind_param($types, ...$params);
    $updateQuery->execute();
    
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>