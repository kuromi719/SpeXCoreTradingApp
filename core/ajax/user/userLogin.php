<?php 
header('Content-Type: text/html; charset=utf-8');

require __DIR__ . '/../../system/spex_v4_func.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request mode.']);
    exit;
}

$mail = Sanitize($_POST['email']);
$pass = Sanitize($_POST['password']);

if (empty($mail) || empty($pass)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill all fields.']);
    exit;
}

$param = UserLogin($mail, $pass);
$ComUID = getUIDToMail($mail);

if ($param === true) {
    $uid = getSession('UID') ?? null;
    
    if ($uid) {
        updateUserLastLogin($uid);
        newUserLoginActivity($uid,'SUCCESS');
        PushSend($uid, 'Account Login',"Hello! You have logged in your account from ".getUserIP(),'login');
        echo json_encode(['status' => 'success', 'message' => 'Successfully logged in.']);
    } else {
        newUserLoginActivity($ComUID,'FAILED');
        echo json_encode(['status' => 'error', 'message' => 'Session error occurred']);
    }
} else {
    newUserLoginActivity($ComUID,'FAILED');
    echo json_encode(['status' => 'error', 'message' => $param]);
}

?>