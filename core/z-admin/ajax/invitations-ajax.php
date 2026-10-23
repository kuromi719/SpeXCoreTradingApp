<?php
require __DIR__ . '/../../system/spex_v4_func.php';

header('Content-Type: application/json');

if (!isset($_SESSION['adminlog']) || $_SESSION['adminlog'] !== 'appr') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch($action) {
        case 'getStats':
            getInvitationStats();
            break;
        case 'getUsers':
            getUsers();
            break;
        case 'getInvitations':
            getInvitations();
            break;
        case 'getInvitationDetails':
            getInvitationDetails();
            break;
        case 'sendInvitation':
            sendInvitation();
            break;
        case 'deleteInvitation':
            deleteInvitation();
            break;
        case 'exportCSV':
            exportInvitationsCSV();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getInvitationStats() {
    global $spexcon;
    
    // Total invitations
    $totalQuery = $spexcon->query("SELECT COUNT(*) as count FROM invitations");
    $total = $totalQuery ? $totalQuery->fetch_assoc()['count'] : 0;
    
    // Today invitations
    $todayQuery = $spexcon->query("SELECT COUNT(*) as count FROM invitations WHERE DATE(date) = CURDATE()");
    $today = $todayQuery ? $todayQuery->fetch_assoc()['count'] : 0;
    
    // This week invitations
    $weekQuery = $spexcon->query("SELECT COUNT(*) as count FROM invitations WHERE WEEK(date) = WEEK(NOW())");
    $week = $weekQuery ? $weekQuery->fetch_assoc()['count'] : 0;
    
    // Active inviters (users who made invitations this month)
    $activeQuery = $spexcon->query("SELECT COUNT(DISTINCT user_uid) as count FROM invitations WHERE MONTH(date) = MONTH(NOW())");
    $activeInviters = $activeQuery ? $activeQuery->fetch_assoc()['count'] : 0;
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total' => (int)$total,
            'today' => (int)$today,
            'week' => (int)$week,
            'active_inviters' => (int)$activeInviters
        ]
    ]);
}

function getUsers() {
    global $spexcon;
    
    $query = $spexcon->query("SELECT uid, mail FROM users ORDER BY mail ASC LIMIT 1000");
    $users = [];
    
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            $users[] = $row;
        }
    }
    
    echo json_encode(['success' => true, 'users' => $users]);
}

function getInvitations() {
    global $spexcon;
    
    $search = Sanitize($_POST['search'] ?? '');
    $inviter = Sanitize($_POST['inviter'] ?? '');
    $date = Sanitize($_POST['date'] ?? '');
    $status = Sanitize($_POST['status'] ?? '');
    
    $sql = "
        SELECT 
            i.*,
            u1.mail as inviter_email,
            u2.mail as invited_email
        FROM invitations i
        LEFT JOIN users u1 ON i.user_uid = u1.uid
        LEFT JOIN users u2 ON i.ref_uid = u2.uid
        WHERE 1=1
    ";
    
    $params = [];
    $types = "";
    
    if (!empty($search)) {
        $sql .= " AND (u1.mail LIKE ? OR u2.mail LIKE ? OR i.user_uid LIKE ? OR i.ref_uid LIKE ?)";
        $searchParam = "%$search%";
        $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
        $types .= "ssss";
    }
    
    if (!empty($inviter)) {
        $sql .= " AND i.user_uid = ?";
        $params[] = $inviter;
        $types .= "s";
    }
    
    if (!empty($date)) {
        $sql .= " AND DATE(i.date) = ?";
        $params[] = $date;
        $types .= "s";
    }
    
    if (!empty($status)) {
        if ($status === 'registered') {
            $sql .= " AND u2.uid IS NOT NULL";
        } elseif ($status === 'pending') {
            $sql .= " AND u2.uid IS NULL";
        }
    }
    
    $sql .= " ORDER BY i.date DESC LIMIT 100";
    
    if (!empty($params)) {
        $stmt = $spexcon->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $spexcon->query($sql);
    }
    
    $invitations = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Determine status
            if ($row['invited_email']) {
                $row['status'] = 'registered';
                $row['bonus_amount'] = 10; // Example bonus amount
            } else {
                $row['status'] = 'pending';
                $row['bonus_amount'] = null;
            }
            
            $invitations[] = $row;
        }
    }
    
    echo json_encode(['success' => true, 'invitations' => $invitations]);
}

function getInvitationDetails() {
    global $spexcon;
    
    $invitationId = (int)$_POST['invitation_id'];
    if (!$invitationId) {
        throw new Exception("Invitation ID is required");
    }
    
    $stmt = $spexcon->prepare("
        SELECT 
            i.*,
            u1.mail as inviter_email,
            u2.mail as invited_email
        FROM invitations i
        LEFT JOIN users u1 ON i.user_uid = u1.uid
        LEFT JOIN users u2 ON i.ref_uid = u2.uid
        WHERE i.id = ?
    ");
    $stmt->bind_param("i", $invitationId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Invitation not found");
    }
    
    $invitation = $result->fetch_assoc();
    
    // Determine status and bonus
    if ($invitation['invited_email']) {
        $invitation['status'] = 'registered';
        $invitation['bonus_amount'] = 10; // Example bonus
    } else {
        $invitation['status'] = 'pending';
        $invitation['bonus_amount'] = null;
    }
    
    echo json_encode(['success' => true, 'invitation' => $invitation]);
}

function sendInvitation() {
    global $spexcon;
    
    $inviterUser = Sanitize($_POST['inviter_user']);
    $invitedEmail = Sanitize($_POST['invited_email']);
    $message = Sanitize($_POST['message'] ?? '');
    
    if (empty($inviterUser) || empty($invitedEmail)) {
        throw new Exception("Inviter user and invited email are required");
    }
    
    if (!filter_var($invitedEmail, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email address");
    }
    
    // Check if user exists
    $userCheck = $spexcon->prepare("SELECT uid FROM users WHERE uid = ?");
    $userCheck->bind_param("s", $inviterUser);
    $userCheck->execute();
    if ($userCheck->get_result()->num_rows === 0) {
        throw new Exception("Inviter user not found");
    }
    
    // Check if email is already registered
    $emailCheck = $spexcon->prepare("SELECT uid FROM users WHERE mail = ?");
    $emailCheck->bind_param("s", $invitedEmail);
    $emailCheck->execute();
    if ($emailCheck->get_result()->num_rows > 0) {
        throw new Exception("This email is already registered");
    }
    
    // Check if invitation already exists
    $inviteCheck = $spexcon->query("SELECT id FROM invitations WHERE user_uid = '$inviterUser' AND ref_uid = '$invitedEmail'");
    if ($inviteCheck && $inviteCheck->num_rows > 0) {
        throw new Exception("Invitation already sent to this email");
    }
    
    // Generate a unique invitation code for the invited user
    $invitationCode = uniqid('inv_', true);
    
    // Insert invitation record
    $stmt = $spexcon->prepare("INSERT INTO invitations (user_uid, ref_uid, date) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $inviterUser, $invitedEmail);
    
    if ($stmt->execute()) {
        // Here you would typically send an email to the invited user
        // For now, we'll just return success
        
        echo json_encode(['success' => true, 'message' => 'Invitation sent successfully to ' . $invitedEmail]);
    } else {
        throw new Exception("Failed to send invitation: " . $spexcon->error);
    }
}

function deleteInvitation() {
    global $spexcon;
    
    $invitationId = (int)$_POST['invitation_id'];
    if (!$invitationId) {
        throw new Exception("Invitation ID is required");
    }
    
    $stmt = $spexcon->prepare("DELETE FROM invitations WHERE id = ?");
    $stmt->bind_param("i", $invitationId);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Invitation deleted successfully']);
        } else {
            throw new Exception("Invitation not found");
        }
    } else {
        throw new Exception("Failed to delete invitation: " . $spexcon->error);
    }
}

function exportInvitationsCSV() {
    global $spexcon;
    
    $search = Sanitize($_GET['search'] ?? '');
    $inviter = Sanitize($_GET['inviter'] ?? '');
    $date = Sanitize($_GET['date'] ?? '');
    $status = Sanitize($_GET['status'] ?? '');
    
    $sql = "
        SELECT 
            i.*,
            u1.mail as inviter_email,
            u2.mail as invited_email
        FROM invitations i
        LEFT JOIN users u1 ON i.user_uid = u1.uid
        LEFT JOIN users u2 ON i.ref_uid = u2.uid
        WHERE 1=1
    ";
    
    $params = [];
    $types = "";
    
    if (!empty($search)) {
        $sql .= " AND (u1.mail LIKE ? OR u2.mail LIKE ? OR i.user_uid LIKE ? OR i.ref_uid LIKE ?)";
        $searchParam = "%$search%";
        $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
        $types .= "ssss";
    }
    
    if (!empty($inviter)) {
        $sql .= " AND i.user_uid = ?";
        $params[] = $inviter;
        $types .= "s";
    }
    
    if (!empty($date)) {
        $sql .= " AND DATE(i.date) = ?";
        $params[] = $date;
        $types .= "s";
    }
    
    if (!empty($status)) {
        if ($status === 'registered') {
            $sql .= " AND u2.uid IS NOT NULL";
        } elseif ($status === 'pending') {
            $sql .= " AND u2.uid IS NULL";
        }
    }
    
    $sql .= " ORDER BY i.date DESC";
    
    if (!empty($params)) {
        $stmt = $spexcon->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $spexcon->query($sql);
    }
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="invitations_export_' . date('Y-m-d_H-i-s') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    fputcsv($output, [
        'Date', 'Inviter ID', 'Inviter Email', 'Invited User ID', 'Invited Email', 'Status', 'Bonus Amount'
    ]);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $status = $row['invited_email'] ? 'Registered' : 'Pending';
            $bonus = $row['invited_email'] ? '$10.00' : '$0.00';
            
            fputcsv($output, [
                $row['date'],
                $row['user_uid'],
                $row['inviter_email'] ?: 'Not Available',
                $row['ref_uid'],
                $row['invited_email'] ?: $row['ref_uid'],
                $status,
                $bonus
            ]);
        }
    }
    
    fclose($output);
    exit;
}
?>