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
            getNotificationStats();
            break;
        case 'getUsers':
            getUsers();
            break;
        case 'getNotifications':
            getNotifications();
            break;
        case 'getNotificationDetails':
            getNotificationDetails();
            break;
        case 'sendNotification':
            sendNotification();
            break;
        case 'exportCSV':
            exportNotificationsCSV();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getNotificationStats() {
    global $spexcon;
    
    // Total notifications
    $totalQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_pushs");
    $total = $totalQuery ? $totalQuery->fetch_assoc()['count'] : 0;
    
    // Today notifications
    $todayQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_pushs WHERE DATE(date) = CURDATE()");
    $today = $todayQuery ? $todayQuery->fetch_assoc()['count'] : 0;
    
    // This week notifications
    $weekQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_pushs WHERE WEEK(date) = WEEK(NOW())");
    $week = $weekQuery ? $weekQuery->fetch_assoc()['count'] : 0;
    
    // Total users
    $usersQuery = $spexcon->query("SELECT COUNT(*) as count FROM users");
    $users = $usersQuery ? $usersQuery->fetch_assoc()['count'] : 0;
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total' => (int)$total,
            'today' => (int)$today,
            'week' => (int)$week,
            'users' => (int)$users
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

function getNotifications() {
    global $spexcon;
    
    $search = Sanitize($_POST['search'] ?? '');
    $type = Sanitize($_POST['type'] ?? '');
    $date = Sanitize($_POST['date'] ?? '');
    $user = Sanitize($_POST['user'] ?? '');
    
    $sql = "
        SELECT 
            up.*,
            u.mail as user_email
        FROM user_pushs up
        LEFT JOIN users u ON up.uid = u.uid
        WHERE 1=1
    ";
    
    $params = [];
    $types = "";
    
    if (!empty($search)) {
        $sql .= " AND (up.title LIKE ? OR up.descr LIKE ? OR u.mail LIKE ?)";
        $searchParam = "%$search%";
        $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        $types .= "sss";
    }
    
    if (!empty($type)) {
        $sql .= " AND up.type = ?";
        $params[] = $type;
        $types .= "s";
    }
    
    if (!empty($date)) {
        $sql .= " AND DATE(up.date) = ?";
        $params[] = $date;
        $types .= "s";
    }
    
    if (!empty($user)) {
        $sql .= " AND up.uid = ?";
        $params[] = $user;
        $types .= "s";
    }
    
    $sql .= " ORDER BY up.date DESC LIMIT 100";
    
    if (!empty($params)) {
        $stmt = $spexcon->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $spexcon->query($sql);
    }
    
    $notifications = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $notifications[] = $row;
        }
    }
    
    echo json_encode(['success' => true, 'notifications' => $notifications]);
}

function getNotificationDetails() {
    global $spexcon;
    
    $notificationId = (int)$_POST['notification_id'];
    if (!$notificationId) {
        throw new Exception("Notification ID is required");
    }
    
    $stmt = $spexcon->prepare("
        SELECT 
            up.*,
            u.mail as user_email
        FROM user_pushs up
        LEFT JOIN users u ON up.uid = u.uid
        WHERE up.id = ?
    ");
    $stmt->bind_param("i", $notificationId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Notification not found");
    }
    
    $notification = $result->fetch_assoc();
    echo json_encode(['success' => true, 'notification' => $notification]);
}

function sendNotification() {
    global $spexcon;
    
    $sendTo = Sanitize($_POST['send_to']);
    $userId = Sanitize($_POST['user_id'] ?? '');
    $type = Sanitize($_POST['type']);
    $title = Sanitize($_POST['title']);
    $message = Sanitize($_POST['message']);
    
    if (empty($sendTo) || empty($type) || empty($title) || empty($message)) {
        throw new Exception("All fields are required");
    }
    
    if ($sendTo === 'specific' && empty($userId)) {
        throw new Exception("Please select a user");
    }
    
    $sentCount = 0;
    
    if ($sendTo === 'all') {
        // Send to all users
        $usersQuery = $spexcon->query("SELECT uid FROM users");
        if ($usersQuery) {
            while ($user = $usersQuery->fetch_assoc()) {
                if (PushSend($user['uid'], $title, $message, $type)) {
                    $sentCount++;
                }
            }
        }
        $message_text = "Notification sent to $sentCount users successfully";
    } else {
        // Send to specific user
        if (PushSend($userId, $title, $message, $type)) {
            $sentCount = 1;
            $message_text = "Notification sent to user successfully";
        } else {
            throw new Exception("Failed to send notification");
        }
    }
    
    echo json_encode(['success' => true, 'message' => $message_text, 'sent_count' => $sentCount]);
}

function exportNotificationsCSV() {
    global $spexcon;
    
    $search = Sanitize($_GET['search'] ?? '');
    $type = Sanitize($_GET['type'] ?? '');
    $date = Sanitize($_GET['date'] ?? '');
    $user = Sanitize($_GET['user'] ?? '');
    
    $sql = "
        SELECT 
            up.*,
            u.mail as user_email
        FROM user_pushs up
        LEFT JOIN users u ON up.uid = u.uid
        WHERE 1=1
    ";
    
    $params = [];
    $types = "";
    
    if (!empty($search)) {
        $sql .= " AND (up.title LIKE ? OR up.descr LIKE ? OR u.mail LIKE ?)";
        $searchParam = "%$search%";
        $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        $types .= "sss";
    }
    
    if (!empty($type)) {
        $sql .= " AND up.type = ?";
        $params[] = $type;
        $types .= "s";
    }
    
    if (!empty($date)) {
        $sql .= " AND DATE(up.date) = ?";
        $params[] = $date;
        $types .= "s";
    }
    
    if (!empty($user)) {
        $sql .= " AND up.uid = ?";
        $params[] = $user;
        $types .= "s";
    }
    
    $sql .= " ORDER BY up.date DESC";
    
    if (!empty($params)) {
        $stmt = $spexcon->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $spexcon->query($sql);
    }
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="notifications_export_' . date('Y-m-d_H-i-s') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    fputcsv($output, [
        'Date', 'User ID', 'Email', 'Type', 'Title', 'Message'
    ]);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['date'],
                $row['uid'],
                $row['user_email'] ?: 'Not Available',
                $row['type'],
                $row['title'],
                $row['descr']
            ]);
        }
    }
    
    fclose($output);
    exit;
}
?>