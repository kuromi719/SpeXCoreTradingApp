
<?php
header('Content-Type: text/html; charset=utf-8');

require __DIR__ . '/../../system/spex_v4_func.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request mode.']);
    exit;
}

$mail  = Sanitize($_POST['email']);
$pass  = Sanitize($_POST['password']);
$phone = Sanitize($_POST['phone']);
$invc  = isset($_POST['invc']) ? Sanitize($_POST['invc']) : '';

if (empty($mail) || empty($pass) || empty($phone)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill all fields.']);
    exit;
}

if (mailExist($mail)) {
    echo json_encode(['status' => 'error', 'message' => 'This E-Mail address already exist.']);
    exit;
}

if (phoneExist($phone)) {
    echo json_encode(['status' => 'error', 'message' => 'This Phone number already exist.']);
    exit;
}

$GenUID = generateUniqueUID();

$hashedPass = md5($pass);

$Register = registerUser($GenUID,$mail,$phone,$hashedPass);

$WebSets = FetchWebSettings();

if ($Register) {
    
    $RefBonus = $WebSets['RefBonus'];
    $RegBonus = $WebSets['RegBonus'];

    if ($RegBonus>0) {
        addUserBalance($GenUID,$RegBonus);
        newUserLog($GenUID,'Register Bonus','Welcome bonus for new registration',$RegBonus,'rew');
    }

    if (!empty($invc)) {
        $RefV = refToUID($invc);
        newRef($RefV,$GenUID);
        if ($RefBonus>0) {
            addUserBalance($GenUID,$RefBonus);
            newUserLog($GenUID,'Reference Bonus','A user registered with your referral link.',$RefBonus,'rew');
        }
    }

    setSession('verifyMail','1');
    setSession('UID', $GenUID);
    echo json_encode(['status' => 'success', 'message' => 'Successfuly Registered!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Unkown Error occurred.']);
}


?>