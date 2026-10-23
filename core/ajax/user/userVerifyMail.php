<?php 
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../../system/spex_v4_func.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_POST['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Action not specified']);
    exit;
}

$action = Sanitize($_POST['action']);
$UID = getSession('UID');
$verifyMail = getSession('verifyMail');

if (empty($UID) || $verifyMail < 1) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid session']);
    exit;
}

$userData = FetchUserData($UID);
if (!$userData) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit;
}

if ($userData['mail_verified'] == '1') {
    echo json_encode(['status' => 'error', 'message' => 'Email already verified']);
    exit;
}

if ($action === 'resend') {
    
    $lastSent = getSession('last_verify_sent');
    if ($lastSent && (time() - $lastSent) < 60) {
        echo json_encode(['status' => 'error', 'message' => 'Please wait 60 seconds before requesting another code']);
        exit;
    }
    
    $verificationCode = generateVerificationCode();
    
    setSession('verification_code', $verificationCode);
    setSession('verification_time', time());
    setSession('last_verify_sent', time());
    
    $emailSent = sendVerificationEmail($userData['mail'], $verificationCode, $UID);
    
    if ($emailSent) {
        echo json_encode(['status' => 'success', 'message' => 'Verification code sent to your email']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send email. Please try again']);
    }
    
} elseif ($action === 'verify') {
    
    if (!isset($_POST['code'])) {
        echo json_encode(['status' => 'error', 'message' => 'Verification code required']);
        exit;
    }
    
    $inputCode = Sanitize($_POST['code']);
    $storedCode = getSession('verification_code');
    $codeTime = getSession('verification_time');
    
    if (empty($storedCode)) {
        echo json_encode(['status' => 'error', 'message' => 'No verification code found. Please request a new one']);
        exit;
    }
    
    if (empty($codeTime) || (time() - $codeTime) > 600) {
        echo json_encode(['status' => 'error', 'message' => 'Verification code expired. Please request a new one']);
        exit;
    }
    
    if ($inputCode !== $storedCode) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid verification code']);
        exit;
    }
    
    $updateResult = updateUserEmailVerification($UID);
    
    if ($updateResult) {
        unset($_SESSION['verification_code']);
        unset($_SESSION['verification_time']);
        unset($_SESSION['verifyMail']);
        unset($_SESSION['last_verify_sent']);
        
        echo json_encode(['status' => 'success', 'message' => 'Email verified successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update verification status']);
    }
    
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

function sendVerificationEmail($email, $code, $uid) {
    $webSettings = FetchWebSettings();
    $siteName = $webSettings['BaseName'];
    $domain = $_SERVER['HTTP_HOST'];

    $SMTPHost = $webSettings['smtp_host'];
    $SMTPUser = $webSettings['smtp_user'];
    $SMTPPass = $webSettings['smtp_pass'];
    $SMTPPort = $webSettings['smtp_port'];
    
    $subject = "Email Verification - $siteName";
    
    $message = generateVerificationEmailTemplate($code, $uid, $siteName, $domain);
    
    $SendMail = new PHPMailer(true);

    $SendMail->isSMTP();
    $SendMail->Host       = $SMTPHost;
    $SendMail->SMTPAuth   = true;
    $SendMail->Username   = $SMTPUser;
    $SendMail->Password   = $SMTPPass;
    $SendMail->Port       = $SMTPPort;

    $SendMail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
    );

    if ($SMTPPort == 465) {
        $SendMail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } elseif ($SMTPPort == 587) {
        $SendMail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    }

    $SendMail->setFrom('support@' . $_SERVER['HTTP_HOST'] , $siteName . ' Support');
    $SendMail->addAddress($email);

    $SendMail->isHTML(true);
    $SendMail->Subject = $subject;
    $SendMail->Body    = $message;
    
    return $SendMail->send();
}

function generateVerificationEmailTemplate($code, $uid, $siteName, $domain) {
    $loginLink = "https://$domain/?r=verifyMail";
    $time = date('l jS \of F Y h:i:s A');
    
    return '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Email Verification</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px 20px; text-align: center; }
            .header h1 { margin: 0; font-size: 24px; }
            .content { padding: 30px 20px; }
            .code-box { background: #f8f9fa; border: 2px dashed #007bff; border-radius: 10px; padding: 20px; text-align: center; margin: 20px 0; }
            .code { font-size: 32px; font-weight: bold; color: #007bff; letter-spacing: 8px; font-family: monospace; }
            .button { display: inline-block; background: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
            .warning { background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 15px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Email Verification</h1>
                <p>Welcome to ' . $siteName . '</p>
            </div>
            
            <div class="content">
                <h2>Hello!</h2>
                <p>Thank you for signing up with <strong>' . $siteName . '</strong>. To complete your registration, please verify your email address using the code below:</p>
                
                <div class="code-box">
                    <p style="margin: 0; font-size: 14px; color: #666;">Your verification code is:</p>
                    <div class="code">' . $code . '</div>
                    <p style="margin: 10px 0 0 0; font-size: 12px; color: #666;">Enter this code on the verification page</p>
                </div>
                
                <div style="text-align: center;">
                    <a href="' . $loginLink . '" class="button">Verify Email Address</a>
                </div>
                
                <div class="warning">
                    <strong>Important:</strong> This code will expire in 10 minutes for security reasons.
                </div>
                
                <h3>Security Information:</h3>
                <ul>
                    <li><strong>User ID:</strong> ' . $uid . '</li>
                    <li><strong>Time:</strong> ' . $time . '</li>
                    <li><strong>IP Address:</strong> ' . $_SERVER['REMOTE_ADDR'] . '</li>
                </ul>
                
                <p><small>If you did not create an account with ' . $siteName . ', please ignore this email.</small></p>
            </div>
            
            <div class="footer">
                <p>This is an automated email. Please do not reply.</p>
                <p>&copy; 2025 ' . $siteName . '. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>';
}

function generateVerificationCode() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function updateUserEmailVerification($uid) {
    global $spexcon;
    
    $query = "UPDATE users SET mail_verified = '1' WHERE uid = '$uid'";
    $result = $spexcon->query($query);
    
    return $result ? true : false;
}

?>