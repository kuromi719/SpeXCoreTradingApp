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
        case 'getSettings333':
            getSettings333();
            break;
        case 'updateSettings333':
            updateSettings333();
            break;
        case 'getStats333':
            getStats333();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getSettings333() {
    global $spexcon;
    
    $query = $spexcon->query("SELECT * FROM mystery_box_settings ORDER BY id DESC LIMIT 1");
    if (!$query) {
        throw new Exception("Database error: " . $spexcon->error);
    }
    
    $settings = $query->fetch_assoc();
    if (!$settings) {
        throw new Exception('Settings not found');
    }
    
    echo json_encode(['success' => true, 'data' => $settings]);
}

function updateSettings333() {
    global $spexcon;
    
    $system_active = (int)($_POST['system_active'] ?? 0);
    $use_eligible_only = (int)($_POST['use_eligible_users_only'] ?? 0);
    $drop_chance = (float)($_POST['drop_chance'] ?? 2.5);
    $drop_interval_min = (int)($_POST['drop_interval_min'] ?? 15);
    $max_daily_drops = (int)($_POST['max_daily_drops'] ?? 5);
    $announcement_message = $_POST['announcement_message'] ?? '';
    
    $query = $spexcon->prepare("
        UPDATE mystery_box_settings SET 
            system_active = ?, 
            use_eligible_users_only = ?, 
            drop_chance = ?, 
            drop_interval_min = ?, 
            max_daily_drops = ?, 
            announcement_message = ?
        ORDER BY id DESC LIMIT 1
    ");
    
    if (!$query) {
        throw new Exception("Database prepare error: " . $spexcon->error);
    }
    
    $query->bind_param('iidiis', $system_active, $use_eligible_only, $drop_chance, $drop_interval_min, $max_daily_drops, $announcement_message);
    
    if (!$query->execute()) {
        throw new Exception("Execute error: " . $query->error);
    }
    
    echo json_encode(['success' => true, 'message' => 'Settings updated successfully']);
}

function getStats333() {
    global $spexcon;
    
    $stats = [];
    
    // Total drops
    $query = $spexcon->query("SELECT COUNT(*) as count FROM user_mystery_drops");
    $stats['total_drops'] = $query ? $query->fetch_assoc()['count'] : 0;
    
    // Claimed drops
    $query = $spexcon->query("SELECT COUNT(*) as count FROM user_mystery_drops WHERE is_claimed = 1");
    $stats['claimed_drops'] = $query ? $query->fetch_assoc()['count'] : 0;
    
    // Eligible users
    $query = $spexcon->query("SELECT COUNT(*) as count FROM mystery_box_eligible_users WHERE is_active = 1");
    $stats['eligible_users'] = $query ? $query->fetch_assoc()['count'] : 0;
    
    // Active boxes
    $query = $spexcon->query("SELECT COUNT(*) as count FROM mystery_boxes WHERE is_active = 1");
    $stats['active_boxes'] = $query ? $query->fetch_assoc()['count'] : 0;
    
    echo json_encode(['success' => true, 'data' => $stats]);
}
?>