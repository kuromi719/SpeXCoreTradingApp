<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$UserData = FetchUserData($UserUID);
if ($UserData['balance_blocked'] == 1) {
    echo json_encode(['success' => false, 'message' => 'Your Balance is Locked by System']);
    exit;
}

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

$lastCodeTime = getSession('last_withdraw_code_time');
if ($lastCodeTime && (time() - $lastCodeTime) < 60) {
    echo json_encode(['success' => false, 'message' => 'Please wait before requesting another code']);
    exit;
}

try {
    $verificationCode = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
    
    setSession('withdraw_verification_code', $verificationCode);
    setSession('withdraw_verification_time', time());
    setSession('withdraw_verification_data', json_encode($input));
    setSession('last_withdraw_code_time', time());

    $WebsData = FetchWebSettings();
    
    $subject = "Withdrawal Verification Code";
    $body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f6f8fa; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .header { background: linear-gradient(135deg, #238636 0%, #2ea043 100%); color: white; padding: 30px; text-align: center; }
        .content { padding: 40px 30px; }
        .code-box { text-align: center; font-size: 36px; font-weight: bold; color: #2ea043; letter-spacing: 8px; margin: 30px 0; background: #f6f8fa; padding: 25px; border-radius: 10px; border: 2px dashed #2ea043; }
        .details-box { background: #f6f8fa; padding: 20px; border-radius: 8px; margin-top: 30px; border-left: 4px solid #0969da; }
        .details-table { width: 100%; border-collapse: collapse; }
        .details-table td { padding: 10px 0; }
        .label { color: #57606a; font-weight: bold; width: 30%; }
        .value { color: #24292f; }
        .address { font-family: monospace; font-size: 13px; word-break: break-all; }
        .warning { background: #fff8c5; border: 1px solid #f9c513; padding: 15px; border-radius: 6px; margin-top: 20px; }
        .footer { background: #f6f8fa; padding: 20px; text-align: center; color: #57606a; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0; font-size: 28px;">Withdrawal Verification</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Secure your transaction</p>
        </div>
        <div class="content">
            <p style="color: #24292f; font-size: 16px; margin-bottom: 5px;">Hello ' . htmlspecialchars($UserData['mail']) . ',</p>
            <p style="color: #57606a; margin-bottom: 30px;">You have requested to withdraw funds from your account. Please use the verification code below to complete your withdrawal:</p>
            
            <div class="code-box">' . $verificationCode . '</div>
            
            <div class="details-box">
                <h3 style="margin: 0 0 15px 0; color: #0969da; font-size: 18px;">Withdrawal Details</h3>
                <table class="details-table">
                    <tr>
                        <td class="label">Amount:</td>
                        <td class="value" style="font-size: 18px; color: #2ea043;"><strong>' . number_format($input['amount'], 2) . ' USDT</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Network:</td>
                        <td class="value">' . strtoupper($input['network']) . '</td>
                    </tr>
                    <tr>
                        <td class="label">Destination:</td>
                        <td class="value address">' . substr($input['address'], 0, 8) . '...' . substr($input['address'], -8) . '</td>
                    </tr>
                </table>
            </div>
            
            <div class="warning">
                <strong style="color: #9a6700;">Important:</strong>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    <li>This code will expire in <strong>10 minutes</strong></li>
                    <li>Never share this code with anyone</li>
                    <li>If you didn\'t request this withdrawal, contact support immediately</li>
                </ul>
            </div>
        </div>
        <div class="footer">
            <p style="margin: 0;">This is an automated message from ' . $WebsData['BaseName'] . '</p>
            <p style="margin: 5px 0 0 0;">© ' . date('Y') . ' ' . $WebsData['BaseName'] . '. All rights reserved.</p>
        </div>
    </div>
</body>
</html>';

    $MailSend = sendVerificationEmail($subject,$body,$UserData['mail']);
    
    if ($MailSend) {
        echo json_encode([
            'success' => true,
            'message' => 'Verification code sent to your email'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to send verification email'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error sending verification code'
    ]);
}

function sendVerificationEmail($subjects,$bodys,$email) {
    $webSettings = FetchWebSettings();
    $siteName = $webSettings['BaseName'];
    $domain = $_SERVER['HTTP_HOST'];

    $SMTPHost = $webSettings['smtp_host'];
    $SMTPUser = $webSettings['smtp_user'];
    $SMTPPass = $webSettings['smtp_pass'];
    $SMTPPort = $webSettings['smtp_port'];
    
    $subject = $subjects." ".$siteName;
    
    $message = $bodys;
    
    $SendMail = new PHPMailer(true);

    $SendMail->isSMTP();
    $SendMail->Host       = $SMTPHost;
    $SendMail->SMTPAuth   = true;
    $SendMail->Username   = $SMTPUser;
    $SendMail->Password   = $SMTPPass;
    $SendMail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $SendMail->Port       = $SMTPPort;

    $SendMail->setFrom('support@' . $_SERVER['HTTP_HOST'] , $siteName . ' Support');
    $SendMail->addAddress($email);

    $SendMail->isHTML(true);
    $SendMail->Subject = $subject;
    $SendMail->Body    = $message;
    
    return $SendMail->send();
}
?>