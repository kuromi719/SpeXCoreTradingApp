<?php
require __DIR__ . '/../../system/spex_v4_func.php';

header('Content-Type: text/html');

if (!isset($_SESSION['adminlog']) || $_SESSION['adminlog'] !== 'appr') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    switch($action) {
        case 'getStats':
            getUserStats2();
            break;
        case 'getUsers':
            getUsers();
            break;
        case 'createUser':
            createUser();
            break;
        case 'updateUser':
            updateUser();
            break;
        case 'deleteUser':
            deleteUser();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getUserStats2() {
    global $spexcon;
    
    $totalQuery = $spexcon->query("SELECT COUNT(*) as count FROM users");
    $totalUsers = $totalQuery ? $totalQuery->fetch_assoc()['count'] : 0;
    
    $activeQuery = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE disablead = 0");
    $activeUsers = $activeQuery ? $activeQuery->fetch_assoc()['count'] : 0;
    
    $verifiedQuery = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE mail_verified = '1'");
    $verifiedEmails = $verifiedQuery ? $verifiedQuery->fetch_assoc()['count'] : 0;
    
    $kycQuery = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE identification_verify = 1");
    $kycVerified = $kycQuery ? $kycQuery->fetch_assoc()['count'] : 0;
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'verified_emails' => $verifiedEmails,
            'kyc_verified' => $kycVerified
        ]
    ]);
}

function getUsers() {
    global $spexcon;
    
    $query = $spexcon->query("SELECT * FROM users ORDER BY register_date DESC");
    if (!$query) {
        throw new Exception("Database error: " . $spexcon->error);
    }
    
    $users = [];
    $security = [];

    while ($row = $query->fetch_assoc()) {
        $users[] = $row;

        $uid = $row['uid'];
        $sque = $spexcon->query("SELECT * FROM user_security_settings WHERE user_id='$uid'");
        if ($sque && $sque->num_rows > 0) {
            $security[$uid] = $sque->fetch_assoc();
        } else {
            $security[$uid] = 0;
        }
    }

    echo json_encode(['success' => true, 'users' => $users, 'security' => $security]);
}


function createUser() {
    global $spexcon;
    
    $mail = Sanitize($_POST['mail'] ?? '');
    $phone = Sanitize($_POST['phone'] ?? '');
    $pass = Sanitize($_POST['pass'] ?? '');
    $nickname = Sanitize($_POST['nickname'] ?? '');
    $balance = Sanitize($_POST['balance'] ?? '0');
    $virtual_address = Sanitize($_POST['virtual_address'] ?? '');
    
    $mail_verified = ($_POST['mail_verified'] ?? '0') == '1' ? '1' : '0';
    $identification_verify = ($_POST['identification_verify'] ?? '0') == '1' ? 1 : 0;
    $disablead = ($_POST['disablead'] ?? '0') == '1' ? 1 : 0;
    
    if (empty($mail) || empty($phone) || empty($pass)) {
        throw new Exception("Email, phone and password are required");
    }
    
    $checkQuery = $spexcon->prepare("SELECT uid FROM users WHERE mail = ?");
    $checkQuery->bind_param("s", $mail);
    $checkQuery->execute();
    if ($checkQuery->get_result()->num_rows > 0) {
        throw new Exception("Email already exists");
    }
    
    $checkQuery = $spexcon->prepare("SELECT uid FROM users WHERE phone = ?");
    $checkQuery->bind_param("s", $phone);
    $checkQuery->execute();
    if ($checkQuery->get_result()->num_rows > 0) {
        throw new Exception("Phone already exists");
    }
    
    $hashedPass = md5($pass);
    
    if (empty($virtual_address)) {
        $virtual_address = generateUniqueWalletAddress();
    }
    
    $ugenid = generateUniqueUID();
    $invitation_code = generateUniqueInvatationCode();
    $profile_img = randomUserImgPath();
    $ip_address = getUserIP();
    
    $stmt = $spexcon->prepare("
        INSERT INTO users (
            uid,mail, phone, pass, nickname, balance, virtual_address, 
            invitation_code, profile_img, ip_address, mail_verified, 
            identification_verify, disablead, register_date, last_login
        ) VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");
    
    $stmt->bind_param("sssssssssssii", 
        $ugenid,$mail, $phone, $hashedPass, $nickname, $balance, $virtual_address,
        $invitation_code, $profile_img, $ip_address, $mail_verified,
        $identification_verify, $disablead
    );
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User created successfully']);
    } else {
        throw new Exception("Failed to create user: " . $stmt->error);
    }
}

function updateUser() {
    global $spexcon;
    
    $uid = (int)($_POST['uid'] ?? 0);
    if (!$uid) {
        throw new Exception("User ID is required");
    }
    
    $mail = Sanitize($_POST['mail'] ?? '');
    $phone = Sanitize($_POST['phone'] ?? '');
    $pass = Sanitize($_POST['pass'] ?? '');
    $nickname = Sanitize($_POST['nickname'] ?? '');
    $balance = Sanitize($_POST['balance'] ?? '0');
    $virtual_address = Sanitize($_POST['virtual_address'] ?? '');
    
    $mail_verified = ($_POST['mail_verified'] ?? '0') == '1' ? '1' : '0';
    $identification_verify = ($_POST['identification_verify'] ?? '0') == '1' ? 1 : 0;
    $balanceBlocked = ($_POST['balanceBlocked'] ?? '0') == '1' ? 1 : 0;
    $disablead = ($_POST['disablead'] ?? '0') == '1' ? 1 : 0;
    
    if (empty($mail) || empty($phone)) {
        throw new Exception("Email and phone are required");
    }
    
    $checkQuery = $spexcon->prepare("SELECT uid FROM users WHERE mail = ? AND uid != ?");
    $checkQuery->bind_param("si", $mail, $uid);
    $checkQuery->execute();
    if ($checkQuery->get_result()->num_rows > 0) {
        throw new Exception("Email already exists for another user");
    }
    
    $checkQuery = $spexcon->prepare("SELECT uid FROM users WHERE phone = ? AND uid != ?");
    $checkQuery->bind_param("si", $phone, $uid);
    $checkQuery->execute();
    if ($checkQuery->get_result()->num_rows > 0) {
        throw new Exception("Phone already exists for another user");
    }
    
    if (!empty($pass)) {
        $hashedPass = md5($pass);
        $stmt = $spexcon->prepare("
            UPDATE users SET 
                mail = ?, phone = ?, pass = ?, nickname = ?, balance = ?, 
                virtual_address = ?, mail_verified = ?, balance_blocked = ?, identification_verify = ?, 
                disablead = ?
            WHERE uid = ?
        ");
        $stmt->bind_param("sssssssssii", 
            $mail, $phone, $hashedPass, $nickname, $balance, $virtual_address,
            $mail_verified, $balanceBlocked, $identification_verify, $disablead, $uid
        );
    } else {
        $stmt = $spexcon->prepare("
            UPDATE users SET 
                mail = ?, phone = ?, nickname = ?, balance = ?, 
                virtual_address = ?, mail_verified = ?, balance_blocked = ?, identification_verify = ?, 
                disablead = ?
            WHERE uid = ?
        ");
        $stmt->bind_param("sssssssiii", 
            $mail, $phone, $nickname, $balance, $virtual_address,
            $mail_verified, $balanceBlocked, $identification_verify, $disablead, $uid
        );
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
    } else {
        throw new Exception("Failed to update user: " . $stmt->error);
    }
}

function deleteUser() {
    global $spexcon;
    
    $uid = (int)($_POST['uid'] ?? 0);
    if (!$uid) {
        throw new Exception("User ID is required");
    }
    
    $stmt = $spexcon->prepare("DELETE FROM users WHERE uid = ?");
    $stmt->bind_param("i", $uid);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
        } else {
            throw new Exception("User not found");
        }
    } else {
        throw new Exception("Failed to delete user: " . $stmt->error);
    }
}
?>