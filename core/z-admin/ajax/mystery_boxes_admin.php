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
        case 'getBoxes':
            getBoxes();
            break;
        case 'getBox':
            getBox();
            break;
        case 'createBox':
            createBox();
            break;
        case 'updateBox':
            updateBox();
            break;
        case 'deleteBox':
            deleteBox();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getBoxes() {
    global $spexcon;
    
    $query = $spexcon->query("SELECT * FROM mystery_boxes ORDER BY id ASC");
    if (!$query) {
        throw new Exception("Database error: " . $spexcon->error);
    }
    
    $boxes = [];
    while ($row = $query->fetch_assoc()) {
        $boxes[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $boxes]);
}

function getBox() {
    global $spexcon;
    
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        throw new Exception('Box ID is required');
    }
    
    $query = $spexcon->prepare("SELECT * FROM mystery_boxes WHERE id = ?");
    if (!$query) {
        throw new Exception("Database prepare error: " . $spexcon->error);
    }
    
    $query->bind_param('i', $id);
    $query->execute();
    $result = $query->get_result();
    $box = $result->fetch_assoc();
    
    if (!$box) {
        throw new Exception('Box not found');
    }
    
    echo json_encode(['success' => true, 'data' => $box]);
}

function createBox() {
    global $spexcon;
    
    $box_name = $_POST['box_name'] ?? '';
    $box_description = $_POST['box_description'] ?? '';
    $rarity_common = (float)($_POST['rarity_common_chance'] ?? 50);
    $rarity_rare = (float)($_POST['rarity_rare_chance'] ?? 30);
    $rarity_epic = (float)($_POST['rarity_epic_chance'] ?? 15);
    $rarity_legendary = (float)($_POST['rarity_legendary_chance'] ?? 5);
    $min_usd = (float)($_POST['min_usd_reward'] ?? 1);
    $max_usd = (float)($_POST['max_usd_reward'] ?? 100);
    $coin_rewards = $_POST['coin_rewards'] ?? '{}';
    $is_active = (int)($_POST['is_active'] ?? 1);
    
    if (empty($box_name)) {
        throw new Exception('Box name is required');
    }
    
    // JSON validation
    json_decode($coin_rewards);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON format for coin rewards');
    }
    
    $query = $spexcon->prepare("
        INSERT INTO mystery_boxes (
            box_name, box_description, rarity_common_chance, rarity_rare_chance, 
            rarity_epic_chance, rarity_legendary_chance, min_usd_reward, 
            max_usd_reward, coin_rewards, is_active
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    if (!$query) {
        throw new Exception("Database prepare error: " . $spexcon->error);
    }
    
    $query->bind_param(
        'ssddddddsi',
        $box_name,
        $box_description,
        $rarity_common,
        $rarity_rare,
        $rarity_epic,
        $rarity_legendary,
        $min_usd,
        $max_usd,
        $coin_rewards,
        $is_active
    );
    
    if (!$query->execute()) {
        throw new Exception("Execute error: " . $query->error);
    }
    
    echo json_encode(['success' => true, 'message' => 'Mystery box created successfully']);
}

function updateBox() {
    global $spexcon;
    
    $id = (int)($_POST['id'] ?? 0);
    $box_name = $_POST['box_name'] ?? '';
    $box_description = $_POST['box_description'] ?? '';
    $rarity_common = (float)($_POST['rarity_common_chance'] ?? 50);
    $rarity_rare = (float)($_POST['rarity_rare_chance'] ?? 30);
    $rarity_epic = (float)($_POST['rarity_epic_chance'] ?? 15);
    $rarity_legendary = (float)($_POST['rarity_legendary_chance'] ?? 5);
    $min_usd = (float)($_POST['min_usd_reward'] ?? 1);
    $max_usd = (float)($_POST['max_usd_reward'] ?? 100);
    $coin_rewards = $_POST['coin_rewards'] ?? '{}';
    $is_active = (int)($_POST['is_active'] ?? 1);
    
    if (!$id || empty($box_name)) {
        throw new Exception('Box ID and name are required');
    }
    
    // JSON validation
    json_decode($coin_rewards);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON format for coin rewards');
    }
    
    $query = $spexcon->prepare("
        UPDATE mystery_boxes SET 
            box_name = ?, box_description = ?, rarity_common_chance = ?, 
            rarity_rare_chance = ?, rarity_epic_chance = ?, rarity_legendary_chance = ?, 
            min_usd_reward = ?, max_usd_reward = ?, coin_rewards = ?, is_active = ?
        WHERE id = ?
    ");
    
    if (!$query) {
        throw new Exception("Database prepare error: " . $spexcon->error);
    }
    
    $query->bind_param(
        'ssddddddsi i',
        $box_name,
        $box_description,
        $rarity_common,
        $rarity_rare,
        $rarity_epic,
        $rarity_legendary,
        $min_usd,
        $max_usd,
        $coin_rewards,
        $is_active,
        $id
    );
    
    if (!$query->execute()) {
        throw new Exception("Execute error: " . $query->error);
    }
    
    echo json_encode(['success' => true, 'message' => 'Mystery box updated successfully']);
}

function deleteBox() {
    global $spexcon;
    
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        throw new Exception('Box ID is required');
    }
    
    $query = $spexcon->prepare("DELETE FROM mystery_boxes WHERE id = ?");
    if (!$query) {
        throw new Exception("Database prepare error: " . $spexcon->error);
    }
    
    $query->bind_param('i', $id);
    if (!$query->execute()) {
        throw new Exception("Execute error: " . $query->error);
    }
    
    echo json_encode(['success' => true, 'message' => 'Mystery box deleted successfully']);
}
?>