<?php
require __DIR__ . '/../../system/spex_v4_func.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$adminReque = getSession('AdminReque') ?? null;
$requestedUser = getSession('RequestedUser') ?? null;

if (!$adminReque || !$requestedUser) {
    echo json_encode(['success' => false, 'message' => 'Session expired or invalid.']);
    exit;
}

$UserData = FetchUserData($requestedUser);

if ($UserData) {
    setSession('UID',$UserData['uid']);
    setSession('Login',1);
    setSession('EMail',$UserData['mail']);
    setSession('isAdminLogin',1);
    unset($_SESSION['AdminReque'], $_SESSION['RequestedUser']);
    echo json_encode(['success' => true, 'message' => '[SecureAdmin] Login successful.']);
}

exit;