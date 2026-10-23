<?php
require __DIR__ . '/../../system/spex_v4_func.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

header('Content-Type: application/json');

if (!isset($_SESSION['adminlog']) || $_SESSION['adminlog'] !== 'appr') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    switch($action) {
        case 'getStats':
            getStats();
            break;
        case 'getEmailHistory':
            getEmailHistory();
            break;
        case 'getUsers':
            getUsers();
            break;
        case 'getTemplates':
            getTemplates();
            break;
        case 'getRecipientCount':
            getRecipientCount();
            break;
        case 'saveDraft':
            saveDraft();
            break;
        case 'sendEmail':
            sendEmail();
            break;
        case 'getEmailDetails':
            getEmailDetails();
            break;
        case 'getEmailRecipients':
            getEmailRecipientsDetails();
            break;
        case 'deleteTemplate':
            deleteTemplate();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getStats() {
    global $spexcon;
    
    $totalEmailsQuery = $spexcon->query("SELECT SUM(sent_count) as total FROM email_logs");
    $totalEmails = $totalEmailsQuery ? ($totalEmailsQuery->fetch_assoc()['total'] ?? 0) : 0;
    
    $todayEmailsQuery = $spexcon->query("SELECT SUM(sent_count) as total FROM email_logs WHERE DATE(created_at) = CURDATE()");
    $todayEmails = $todayEmailsQuery ? ($todayEmailsQuery->fetch_assoc()['total'] ?? 0) : 0;
    
    $successQuery = $spexcon->query("SELECT SUM(sent_count) as sent, SUM(failed_count) as failed FROM email_logs");
    $successData = $successQuery ? $successQuery->fetch_assoc() : ['sent' => 0, 'failed' => 0];
    $totalSent = $successData['sent'] + $successData['failed'];
    $successRate = $totalSent > 0 ? round(($successData['sent'] / $totalSent) * 100, 1) : 0;
    
    $templatesQuery = $spexcon->query("SELECT COUNT(*) as count FROM email_templates WHERE is_active = 1");
    $totalTemplates = $templatesQuery ? $templatesQuery->fetch_assoc()['count'] : 0;
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_emails' => $totalEmails,
            'today_emails' => $todayEmails,
            'success_rate' => $successRate,
            'total_templates' => $totalTemplates
        ]
    ]);
}

function getEmailHistory() {
    global $spexcon;
    
    $status = $_POST['status'] ?? '';
    $date = $_POST['date'] ?? '';
    
    $whereConditions = [];
    if ($status) {
        $whereConditions[] = "el.status = '" . $spexcon->real_escape_string($status) . "'";
    }
    if ($date) {
        $whereConditions[] = "DATE(el.created_at) = '" . $spexcon->real_escape_string($date) . "'";
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    $query = $spexcon->query("
        SELECT el.*, a.user as sent_by_name
        FROM email_logs el
        LEFT JOIN admin a ON el.sent_by = a.id
        $whereClause
        ORDER BY el.created_at DESC
        LIMIT 50
    ");
    
    $emails = [];
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            $emails[] = $row;
        }
    }
    
    echo json_encode([
        'success' => true,
        'emails' => $emails
    ]);
}

function getUsers() {
    global $spexcon;
    
    $query = $spexcon->query("
        SELECT uid, mail, disablead, identification_verify
        FROM users 
        WHERE disablead = 0
        ORDER BY mail ASC
        LIMIT 1000
    ");
    
    $users = [];
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            $users[] = $row;
        }
    }
    
    echo json_encode([
        'success' => true,
        'users' => $users
    ]);
}

function getTemplates() {
    global $spexcon;
    
    $query = $spexcon->query("
        SELECT * FROM email_templates 
        WHERE is_active = 1
        ORDER BY created_at DESC
    ");
    
    $templates = [];
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            $templates[] = $row;
        }
    }
    
    echo json_encode([
        'success' => true,
        'templates' => $templates
    ]);
}

function getRecipientCount() {
    global $spexcon;
    
    $type = $_POST['type'] ?? '';
    
    $count = 0;
    switch($type) {
        case 'ALL':
            $query = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE disablead = 0");
            break;
        case 'KYC_APPROVED':
            $query = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE disablead = 0 AND identification_verify = 1");
            break;
        case 'ACTIVE_USERS':
            $query = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE disablead = 0");
            break;
        case 'DISABLED_USERS':
            $query = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE disablead = 1");
            break;
        default:
            $query = null;
    }
    
    if ($query) {
        $count = $query->fetch_assoc()['count'];
    }
    
    echo json_encode([
        'success' => true,
        'count' => $count
    ]);
}

function saveDraft() {
    global $spexcon;
    
    $recipientType = $_POST['recipient_type'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $body = $_POST['body'] ?? '';
    $saveAsTemplate = $_POST['save_as_template'] === 'true';
    $templateName = $_POST['template_name'] ?? '';
    
    if (empty($subject) || empty($body)) {
        throw new Exception("Subject and body are required");
    }
    
    $adminQuery = $spexcon->query("SELECT id FROM admin WHERE user = '" . $_SESSION['adminlog_user'] . "'");
    $adminData = $adminQuery ? $adminQuery->fetch_assoc() : null;
    $adminId = $adminData ? $adminData['id'] : 1;
    
    if ($saveAsTemplate && !empty($templateName)) {
        $stmt = $spexcon->prepare("
            INSERT INTO email_templates (name, subject, body, created_by) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("sssi", $templateName, $subject, $body, $adminId);
        $stmt->execute();
    }
    
    echo json_encode(['success' => true, 'message' => 'Draft saved successfully']);
}

function sendEmail() {
    global $spexcon;
    
    $recipientType = $_POST['recipient_type'] ?? '';
    $specificUser = $_POST['specific_user'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $body = $_POST['body'] ?? '';
    $saveAsTemplate = $_POST['save_as_template'] === 'true';
    $templateName = $_POST['template_name'] ?? '';
    
    if (empty($recipientType) || empty($subject) || empty($body)) {
        throw new Exception("All fields are required");
    }
    
    $adminQuery = $spexcon->query("SELECT id FROM admin WHERE user = '" . $_SESSION['adminlog_user'] . "'");
    $adminData = $adminQuery ? $adminQuery->fetch_assoc() : null;
    $adminId = $adminData ? $adminData['id'] : 1;
    
    $recipients = getEmailRecipients($recipientType, $specificUser);
    
    if (empty($recipients)) {
        throw new Exception("No recipients found");
    }
    
    if ($saveAsTemplate && !empty($templateName)) {
        $stmt = $spexcon->prepare("
            INSERT INTO email_templates (name, subject, body, created_by) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("sssi", $templateName, $subject, $body, $adminId);
        $stmt->execute();
    }
    
    $stmt = $spexcon->prepare("
        INSERT INTO email_logs (recipient_type, recipient_count, subject, body, sent_by, status) 
        VALUES (?, ?, ?, ?, ?, 'PENDING')
    ");
    $recipientCount = count($recipients);
    $stmt->bind_param("sissi", $recipientType, $recipientCount, $subject, $body, $adminId);
    $stmt->execute();
    
    $emailLogId = $spexcon->insert_id;
    
    $stmt = $spexcon->prepare("
        INSERT INTO email_recipients (email_log_id, user_id, email) 
        VALUES (?, ?, ?)
    ");
    
    foreach ($recipients as $recipient) {
        $stmt->bind_param("iis", $emailLogId, $recipient['uid'], $recipient['mail']);
        $stmt->execute();
    }
    
    sendEmailsToRecipients($emailLogId, $subject, $body);
    
    echo json_encode([
        'success' => true,
        'message' => 'Email queued for sending',
        'recipient_count' => $recipientCount
    ]);
}

function getEmailRecipients($type, $specificUser = '') {
    global $spexcon;
    
    switch($type) {
        case 'ALL':
            $query = $spexcon->query("SELECT uid, mail FROM users WHERE disablead = 0");
            break;
        case 'KYC_APPROVED':
            $query = $spexcon->query("SELECT uid, mail FROM users WHERE disablead = 0 AND identification_verify = 1");
            break;
        case 'ACTIVE_USERS':
            $query = $spexcon->query("SELECT uid, mail FROM users WHERE disablead = 0");
            break;
        case 'DISABLED_USERS':
            $query = $spexcon->query("SELECT uid, mail FROM users WHERE disablead = 1");
            break;
        case 'SPECIFIC_USER':
            if (empty($specificUser)) return [];
            $query = $spexcon->query("SELECT uid, mail FROM users WHERE uid = " . intval($specificUser));
            break;
        default:
            return [];
    }
    
    $recipients = [];
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            $recipients[] = $row;
        }
    }
    
    return $recipients;
}

function sendEmailsToRecipients($emailLogId, $subject, $body) {
    global $spexcon;
    
    $spexcon->query("UPDATE email_logs SET status = 'SENDING' WHERE id = $emailLogId");
    
    $settingsQuery = $spexcon->query("SELECT * FROM settings WHERE id = 1");
    $settings = $settingsQuery ? $settingsQuery->fetch_assoc() : [];
    
    $SMTPHost = $settings['smtp_host'] ?? 'localhost';
    $SMTPUser = $settings['smtp_user'] ?? '';
    $SMTPPass = $settings['smtp_pass'] ?? '';
    $SMTPPort = $settings['smtp_port'] ?? 587;
    $siteName = $settings['BaseName'] ?? 'TradingApp';
    
    $recipientsQuery = $spexcon->query("
        SELECT er.*, u.mail, u.nickname, u.balance
        FROM email_recipients er
        LEFT JOIN users u ON er.user_id = u.uid
        WHERE er.email_log_id = $emailLogId AND er.status = 'PENDING'
    ");
    
    $sentCount = 0;
    $failedCount = 0;
    
    if ($recipientsQuery) {
        while ($recipient = $recipientsQuery->fetch_assoc()) {
            try {
                $SendMail = new PHPMailer(true);
                $SendMail->isSMTP();
                $SendMail->Host = $SMTPHost;
                $SendMail->SMTPAuth = true;
                $SendMail->Username = $SMTPUser;
                $SendMail->Password = $SMTPPass;
                $SendMail->Port = $SMTPPort;

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
                
                $SendMail->setFrom('support@' . $_SERVER['HTTP_HOST'], $siteName . ' Support'); // $_SERVER['HTTP_HOST'] -- test.com
                $SendMail->addAddress($recipient['email']);
                
                $personalizedSubject = str_replace([
                    ':USERNAME:',
                    ':SITENAME:',
                    ':EMAIL:',
                    ':DATE:',
                    ':BALANCE:'
                ], [
                    $recipient['nickname'] ?? 'User',
                    $siteName,
                    $recipient['mail'],
                    date('Y-m-d'),
                    '$' . number_format($recipient['balance'] ?? 0, 2)
                ], $subject);
                
                $personalizedBody = str_replace([
                    ':USERNAME:',
                    ':SITENAME:',
                    ':EMAIL:',
                    ':DATE:',
                    ':BALANCE:'
                ], [
                    $recipient['nickname'] ?? 'User',
                    $siteName,
                    $recipient['mail'],
                    date('Y-m-d'),
                    '$' . number_format($recipient['balance'] ?? 0, 2)
                ], $body);
                
                $SendMail->Subject = $personalizedSubject;
                $SendMail->isHTML(true);
                $SendMail->Body = $personalizedBody;
                
                $SendMail->send();
                
                $spexcon->query("UPDATE email_recipients SET status = 'SENT', sent_at = NOW() WHERE id = " . $recipient['id']);
                $sentCount++;
                
            } catch (Exception $e) {
                $errorMsg = $spexcon->real_escape_string($e->getMessage());
                $spexcon->query("UPDATE email_recipients SET status = 'FAILED', error_message = '$errorMsg' WHERE id = " . $recipient['id']);
                $failedCount++;
            }
            
            usleep(100000); // 0.1 second delay
        }
    }
    
    $spexcon->query("
        UPDATE email_logs 
        SET status = 'COMPLETED', 
            sent_count = $sentCount, 
            failed_count = $failedCount,
            completed_at = NOW()
        WHERE id = $emailLogId
    ");
}

function getEmailDetails() {
    global $spexcon;
    
    $emailId = (int)$_POST['email_id'];
    if (!$emailId) {
        throw new Exception("Email ID is required");
    }
    
    $query = $spexcon->query("
        SELECT el.*, a.user as sent_by_name
        FROM email_logs el
        LEFT JOIN admin a ON el.sent_by = a.id
        WHERE el.id = $emailId
    ");
    
    if (!$query || $query->num_rows === 0) {
        throw new Exception("Email not found");
    }
    
    $email = $query->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'email' => $email
    ]);
}

function getEmailRecipientsDetails() {
    global $spexcon;
    
    $emailId = (int)$_POST['email_id'];
    if (!$emailId) {
        throw new Exception("Email ID is required");
    }
    
    $query = $spexcon->query("
        SELECT * FROM email_recipients 
        WHERE email_log_id = $emailId
        ORDER BY sent_at DESC, id DESC
        LIMIT 100
    ");
    
    $recipients = [];
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            $recipients[] = $row;
        }
    }
    
    echo json_encode([
        'success' => true,
        'recipients' => $recipients
    ]);
}

function deleteTemplate() {
    global $spexcon;
    
    $templateId = (int)$_POST['template_id'];
    if (!$templateId) {
        throw new Exception("Template ID is required");
    }
    
    $stmt = $spexcon->prepare("UPDATE email_templates SET is_active = 0 WHERE id = ?");
    $stmt->bind_param("i", $templateId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Template deleted successfully']);
    } else {
        throw new Exception("Failed to delete template");
    }
}
?>