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

if (!$input || !isset($input['nickname'])) {
    echo json_encode(['success' => false, 'message' => 'Nickname is required']);
    exit;
}

try {
    $nickname = trim($input['nickname']);
    $avatar = isset($input['avatar']) ? $input['avatar'] : null;
    
    if (strlen($nickname) < 3 || strlen($nickname) > 20) {
        throw new Exception('Nickname must be between 3-20 characters');
    }
    
    $spexcon->begin_transaction();

    if ($nickname) {
        $userasQuery = $spexcon->prepare("UPDATE users SET nickname = ? WHERE uid = ?");
        $userasQuery->bind_param("si", $nickname, $UserUID);
        $userasQuery->execute();
    }
    
    if ($avatar) {
        $avatarPath = "core/pages/assets/images/social/" . $avatar;
        $userQuery = $spexcon->prepare("UPDATE users SET profile_img = ? WHERE uid = ?");
        $userQuery->bind_param("si", $avatarPath, $UserUID);
        $userQuery->execute();
    }
    
    $spexcon->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully'
    ]);
    
} catch (Exception $e) {
    $spexcon->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>