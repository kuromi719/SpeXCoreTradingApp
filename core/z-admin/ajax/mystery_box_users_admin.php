<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';

header('Content-Type: application/json');

if (!isset($_SESSION['adminlog']) || $_SESSION['adminlog'] !== 'appr') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    switch($action) {
        case 'getEligibleUsers':
            getEligibleUsers();
            break;
        case 'getUserStats':
            getUserStats();
            break;
        case 'searchUsers':
            searchUsers();
            break;
        case 'addEligibleUser':
            addEligibleUser();
            break;
        case 'toggleUserStatus':
            toggleUserStatus();
            break;
        case 'removeUser':
            removeUser();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getEligibleUsers() {
    global $spexcon;
    
    $search = $_POST['search'] ?? '';
    $whereClause = '';
    $params = [];
    $types = '';
    
    if (!empty($search)) {
        $whereClause = " AND (u.mail LIKE ? OR u.uid = ?)";
        $searchParam = '%' . $search . '%';
        $params = [$searchParam, $search];
        $types = 'si';
    }
    
    $query = $spexcon->prepare("
        SELECT meu.*, u.mail as email 
        FROM mystery_box_eligible_users meu
        JOIN users u ON meu.user_id = u.uid
        WHERE 1=1 $whereClause
        ORDER BY meu.added_at DESC
    ");
    
    if (!empty($params)) {
        $query->bind_param($types, ...$params);
    }
    
    $query->execute();
    $result = $query->get_result();
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $users]);
}

function getUserStats() {
    global $spexcon;
    
    $stats = [];
    
    // Total eligible users
    $query = $spexcon->query("SELECT COUNT(*) as count FROM mystery_box_eligible_users");
    $stats['total_eligible'] = $query ? $query->fetch_assoc()['count'] : 0;
    
    // Active eligible users
    $query = $spexcon->query("SELECT COUNT(*) as count FROM mystery_box_eligible_users WHERE is_active = 1");
    $stats['active_eligible'] = $query ? $query->fetch_assoc()['count'] : 0;
    
    // Total platform users
    $query = $spexcon->query("SELECT COUNT(*) as count FROM users WHERE disablead = 0");
    $stats['total_users'] = $query ? $query->fetch_assoc()['count'] : 0;
    
    echo json_encode(['success' => true, 'data' => $stats]);
}

function searchUsers() {
    global $spexcon;
    
    $search = $_POST['search'] ?? '';
    if (empty($search)) {
        echo json_encode(['success' => true, 'data' => []]);
        return;
    }
    
    $query = $spexcon->prepare("
        SELECT uid, mail 
        FROM users 
        WHERE (mail LIKE ? OR uid = ?) 
        AND disablead = 0
        AND uid NOT IN (SELECT user_id FROM mystery_box_eligible_users WHERE is_active = 1)
        LIMIT 10
    ");
    
    $searchParam = '%' . $search . '%';
    $query->bind_param('si', $searchParam, $search);
    $query->execute();
    $result = $query->get_result();
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $users]);
}

function addEligibleUser() {
    global $spexcon;
    
    $userId = (int)($_POST['userId'] ?? 0);
    if (!$userId) {
        throw new Exception('User ID is required');
    }
    
    $adminId = $_SESSION['adminID'] ?? 1;
    
    $query = $spexcon->prepare("
        INSERT INTO mystery_box_eligible_users (user_id, added_by_admin, is_active) 
        VALUES (?, ?, 1)
        ON DUPLICATE KEY UPDATE is_active = 1, added_by_admin = ?
    ");
    
    $query->bind_param('iii', $userId, $adminId, $adminId);
    $query->execute();
    
    echo json_encode(['success' => true, 'message' => 'User added to eligible list successfully']);
}

function toggleUserStatus() {
    global $spexcon;
    
    $userId = (int)($_POST['userId'] ?? 0);
    $status = (int)($_POST['status'] ?? 0);
    
    if (!$userId) {
        throw new Exception('User ID is required');
    }
    
    $query = $spexcon->prepare("UPDATE mystery_box_eligible_users SET is_active = ? WHERE user_id = ?");
    $query->bind_param('ii', $status, $userId);
    $query->execute();
    
    echo json_encode(['success' => true, 'message' => 'User status updated successfully']);
}

function removeUser() {
    global $spexcon;
    
    $userId = (int)($_POST['userId'] ?? 0);
    if (!$userId) {
        throw new Exception('User ID is required');
    }
    
    $query = $spexcon->prepare("DELETE FROM mystery_box_eligible_users WHERE user_id = ?");
    $query->bind_param('i', $userId);
    $query->execute();
    
    echo json_encode(['success' => true, 'message' => 'User removed from eligible list successfully']);
}
?>