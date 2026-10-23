<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../../system/spex_v4_func.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$email = Sanitize($_POST['email'] ?? '');

if (empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill all fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address.']);
    exit;
}

$uid = MailToUID($email);

if (!$uid) {
    echo json_encode(['status' => 'error', 'message' => 'No account found with this email address.']);
    exit;
}

$GenPassword = generatePassword();
$MailTemplate = generateEmailTemplate($uid, $GenPassword);
$WebSets = FetchWebSettings();
$baseName = $WebSets['BaseName'];

$SMTPHost = $WebSets['smtp_host'];
$SMTPUser = $WebSets['smtp_user'];
$SMTPPass = $WebSets['smtp_pass'];
$SMTPPort = $WebSets['smtp_port'];

$SendMail = new PHPMailer(true);

try {
    $SendMail->isSMTP();
    $SendMail->Host       = $SMTPHost;
    $SendMail->SMTPAuth   = true;
    $SendMail->Username   = $SMTPUser;
    $SendMail->Password   = $SMTPPass;
    $SendMail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $SendMail->Port       = $SMTPPort;

    $SendMail->setFrom('support@' . $_SERVER['HTTP_HOST'], $baseName . ' Support');
    $SendMail->addAddress($email);

    $SendMail->isHTML(true);
    $SendMail->Subject = 'Password Reset - ' . $baseName;
    $SendMail->Body    = $MailTemplate;

    $SendMail->send();
    
    updateUserPassword($uid, $GenPassword);
    
    echo json_encode(['status' => 'success', 'message' => 'Your new password has been generated and sent to your email address.']);
    
} catch (Exception $e) {
    $errorMessage = date('Y-m-d H:i:s') . " SMTP error: " . $SendMail->ErrorInfo . "\n";
    file_put_contents('log.txt', $errorMessage, FILE_APPEND);
    
    echo json_encode(['status' => 'error', 'message' => 'Failed to send email. Please try again later.']);
}
?>