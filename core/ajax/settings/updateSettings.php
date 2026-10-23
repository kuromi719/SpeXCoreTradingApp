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

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

try {
    $updateFields = [];
    $params = [];
    $types = "";
    
    $allowedFields = [
        'notification_alert' => 'i',
        'notification_prefs' => 'i', 
        'price_alert' => 'i',
        'shortcuts' => 'i',
        'theme_color' => 's',
        'timezone' => 's',
        'chart_indicator' => 's',
        'nickname' => 's'
    ];
    
    foreach ($input as $field => $value) {
        if (isset($allowedFields[$field])) {
            $updateFields[] = "$field = ?";
            $params[] = $value;
            $types .= $allowedFields[$field];
        }
    }
    
    if (empty($updateFields)) {
        echo json_encode(['success' => false, 'message' => 'No valid fields to update']);
        exit;
    }
    
    // Check if settings exist
    $checkQuery = $spexcon->prepare("SELECT user_id FROM user_settings WHERE user_id = ?");
    $checkQuery->bind_param("i", $UserUID);
    $checkQuery->execute();
    
    if ($checkQuery->get_result()->num_rows > 0) {
        // Update existing settings
        $params[] = $UserUID;
        $types .= "i";
        
        $updateQuery = $spexcon->prepare("
            UPDATE user_settings 
            SET " . implode(', ', $updateFields) . ", updated_at = NOW() 
            WHERE user_id = ?
        ");
        $updateQuery->bind_param($types, ...$params);
        $updateQuery->execute();
    } else {
        // Insert new settings
        $insertQuery = $spexcon->prepare("
            INSERT INTO user_settings 
            (user_id, " . implode(', ', array_keys($input)) . ", created_at) 
            VALUES (?, " . str_repeat('?,', count($input) - 1) . "?, NOW())
        ");
        
        $insertParams = array_merge([$UserUID], array_values($input));
        $insertTypes = "i" . substr($types, 0, -1);
        $insertQuery->bind_param($insertTypes, ...$insertParams);
        $insertQuery->execute();
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Settings updated successfully'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>