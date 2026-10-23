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

if (!$input || !isset($input['newEmail'], $input['currentPassword'])) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

try {
    $newEmail = trim($input['newEmail']);
    $currentPassword = $input['currentPassword'];
    
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }
    
    // Verify current password
    $userQuery = $spexcon->prepare("SELECT pass FROM users WHERE uid = ?");
    $userQuery->bind_param("i", $UserUID);
    $userQuery->execute();
    $user = $userQuery->get_result()->fetch_assoc();
    
    if (!$user || $user['pass'] !== md5($currentPassword)) {
        throw new Exception('Current password is incorrect');
    }
    
    // Check if email already exists
    if (mailExist($newEmail)) {
        throw new Exception('Email already exists');
    }
    
    // Update email
    $updateQuery = $spexcon->prepare("UPDATE users SET mail = ?, mail_verified = '0' WHERE uid = ?");
    $updateQuery->bind_param("si", $newEmail, $UserUID);
    $updateQuery->execute();
    
    // Update session
    setSession('EMail', $newEmail);
    
    // Log the change
    newUserLog($UserUID, 'Email Changed', 'Email address updated to ' . Censor($newEmail, 'email'), '', 'security');
    
    echo json_encode([
        'success' => true,
        'message' => 'Email updated successfully'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>