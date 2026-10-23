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
        case 'getStats':
            getPromoStats();
            break;
        case 'getPromoCodes':
            getPromoCodes();
            break;
        case 'getPromoCode':
            getPromoCode();
            break;
        case 'createPromoCode':
            createPromoCode();
            break;
        case 'updatePromoCode':
            updatePromoCode();
            break;
        case 'deletePromoCode':
            deletePromoCode();
            break;
        case 'getUsageHistory':
            getUsageHistory();
            break;
        case 'deleteUsage':
            deleteUsage();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getPromoStats() {
    global $spexcon;
    
    $stats = [];
    
    // Total codes
    $query = $spexcon->query("SELECT COUNT(*) as count FROM promo_codes");
    $stats['total_codes'] = $query ? $query->fetch_assoc()['count'] : 0;
    
    // Active codes
    $query = $spexcon->query("SELECT COUNT(*) as count FROM promo_codes WHERE is_active = 1");
    $stats['active_codes'] = $query ? $query->fetch_assoc()['count'] : 0;
    
    // Total redemptions
    $query = $spexcon->query("SELECT COUNT(*) as count FROM user_promo_usage");
    $stats['total_redemptions'] = $query ? $query->fetch_assoc()['count'] : 0;
    
    // Total reward value (USD equivalent)
    $query = $spexcon->query("SELECT SUM(reward_usd_value) as total FROM user_promo_usage");
    $result = $query ? $query->fetch_assoc() : null;
    $stats['total_reward_value'] = $result ? ($result['total'] ?? 0) : 0;
    
    echo json_encode(['success' => true, 'data' => $stats]);
}

function getPromoCodes() {
    global $spexcon;
    
    $whereConditions = [];
    $params = [];
    $types = '';
    
    // Status filter
    if (isset($_POST['status']) && $_POST['status'] !== '') {
        $whereConditions[] = "is_active = ?";
        $params[] = (int)$_POST['status'];
        $types .= 'i';
    }
    
    // Type filter
    if (!empty($_POST['type'])) {
        $whereConditions[] = "code_type = ?";
        $params[] = $_POST['type'];
        $types .= 's';
    }
    
    $whereClause = '';
    if (!empty($whereConditions)) {
        $whereClause = ' WHERE ' . implode(' AND ', $whereConditions);
    }
    
    $query = $spexcon->prepare("
        SELECT * FROM promo_codes 
        $whereClause 
        ORDER BY created_at DESC
    ");
    
    if (!empty($params)) {
        $query->bind_param($types, ...$params);
    }
    
    $query->execute();
    $result = $query->get_result();
    
    $codes = [];
    while ($row = $result->fetch_assoc()) {
        $codes[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $codes]);
}

function getPromoCode() {
    global $spexcon;
    
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        throw new Exception('Promo code ID is required');
    }
    
    $query = $spexcon->prepare("SELECT * FROM promo_codes WHERE id = ?");
    $query->bind_param('i', $id);
    $query->execute();
    $result = $query->get_result();
    $promo = $result->fetch_assoc();
    
    if (!$promo) {
        throw new Exception('Promo code not found');
    }
    
    echo json_encode(['success' => true, 'data' => $promo]);
}

function createPromoCode() {
    global $spexcon;
    
    $code = strtoupper(trim($_POST['code'] ?? ''));
    $code_type = $_POST['code_type'] ?? '';
    $reward_amount = (float)($_POST['reward_amount'] ?? 0);
    $reward_usd_value = (float)($_POST['reward_usd_value'] ?? 0);
    $coin_id = $_POST['coin_id'] ?? null;
    $max_usage = (int)($_POST['max_usage'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $is_active = (int)($_POST['is_active'] ?? 1);
    $expires_at = $_POST['expires_at'] ?? null;
    $admin_id = $_SESSION['adminID'] ?? 1;
    
    if (empty($code)) {
        throw new Exception('Promo code is required');
    }
    
    if (strlen($code) < 3 || strlen($code) > 50) {
        throw new Exception('Promo code must be between 3-50 characters');
    }
    
    if (!in_array($code_type, ['usd', 'coin'])) {
        throw new Exception('Invalid reward type');
    }
    
    if ($code_type === 'usd' && $reward_usd_value <= 0) {
        throw new Exception('USD reward amount must be greater than 0');
    }
    
    if ($code_type === 'coin') {
        if (empty($coin_id) || $reward_amount <= 0) {
            throw new Exception('Coin symbol and amount are required for coin rewards');
        }
    }
    
    $checkQuery = $spexcon->prepare("SELECT id FROM promo_codes WHERE code = ?");
    $checkQuery->bind_param('s', $code);
    $checkQuery->execute();
    if ($checkQuery->get_result()->fetch_assoc()) {
        throw new Exception('Promo code already exists');
    }
    
    if (!empty($expires_at)) {
        $expires_at = date('Y-m-d H:i:s', strtotime($expires_at));
    } else {
        $expires_at = null;
    }
    
    $query = $spexcon->prepare("
        INSERT INTO promo_codes (
            code, code_type, coin_id, reward_amount, reward_usd_value, 
            max_usage, description, is_active, expires_at, created_by_admin
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $query->bind_param(
        'sssddisiss',
        $code,
        $code_type,
        $coin_id,
        $reward_amount,
        $reward_usd_value,
        $max_usage,
        $description,
        $is_active,
        $expires_at,
        $admin_id
    );
    
    if (!$query->execute()) {
        throw new Exception("Failed to create promo code: " . $query->error);
    }
    
    echo json_encode(['success' => true, 'message' => 'Promo code created successfully']);
}

function updatePromoCode() {
    global $spexcon;
    
    $id = (int)($_POST['id'] ?? 0);
    $code = strtoupper(trim($_POST['code'] ?? ''));
    $code_type = $_POST['code_type'] ?? '';
    $reward_amount = (float)($_POST['reward_amount'] ?? 0);
    $reward_usd_value = (float)($_POST['reward_usd_value'] ?? 0);
    $coin_id = $_POST['coin_id'] ?? null;
    $max_usage = (int)($_POST['max_usage'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $is_active = (int)($_POST['is_active'] ?? 1);
    $expires_at = $_POST['expires_at'] ?? null;
    
    if (!$id) {
        throw new Exception('Promo code ID is required');
    }

    if (empty($code)) {
        throw new Exception('Promo code is required');
    }
    
    if (strlen($code) < 3 || strlen($code) > 50) {
        throw new Exception('Promo code must be between 3-50 characters');
    }
    
    if (!in_array($code_type, ['usd', 'coin'])) {
        throw new Exception('Invalid reward type');
    }
    
    if ($code_type === 'usd' && $reward_usd_value <= 0) {
        throw new Exception('USD reward amount must be greater than 0');
    }
    
    if ($code_type === 'coin') {
        if (empty($coin_id) || $reward_amount <= 0) {
            throw new Exception('Coin symbol and amount are required for coin rewards');
        }
    }
    
    $checkQuery = $spexcon->prepare("SELECT id FROM promo_codes WHERE code = ? AND id != ?");
    $checkQuery->bind_param('si', $code, $id);
    $checkQuery->execute();
    if ($checkQuery->get_result()->fetch_assoc()) {
        throw new Exception('Promo code already exists');
    }
    
    if (!empty($expires_at)) {
        $expires_at = date('Y-m-d H:i:s', strtotime($expires_at));
    } else {
        $expires_at = null;
    }
    
    $query = $spexcon->prepare("
        UPDATE promo_codes SET 
            code = ?, code_type = ?, coin_id = ?, reward_amount = ?, 
            reward_usd_value = ?, max_usage = ?, description = ?, 
            is_active = ?, expires_at = ?
        WHERE id = ?
    ");
    
    $query->bind_param(
        'sssddisiss',
        $code,
        $code_type,
        $coin_id,
        $reward_amount,
        $reward_usd_value,
        $max_usage,
        $description,
        $is_active,
        $expires_at,
        $id
    );
    
    if (!$query->execute()) {
        throw new Exception("Failed to update promo code: " . $query->error);
    }
    
    echo json_encode(['success' => true, 'message' => 'Promo code updated successfully']);
}

function deletePromoCode() {
    global $spexcon;
    
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        throw new Exception('Promo code ID is required');
    }
    
    $spexcon->begin_transaction();
    
    try {
        $deleteUsageQuery = $spexcon->prepare("DELETE FROM user_promo_usage WHERE promo_code_id = ?");
        $deleteUsageQuery->bind_param('i', $id);
        $deleteUsageQuery->execute();
        
        $deletePromoQuery = $spexcon->prepare("DELETE FROM promo_codes WHERE id = ?");
        $deletePromoQuery->bind_param('i', $id);
        $deletePromoQuery->execute();
        
        if ($deletePromoQuery->affected_rows === 0) {
            throw new Exception('Promo code not found');
        }
        
        $spexcon->commit();
        echo json_encode(['success' => true, 'message' => 'Promo code deleted successfully']);
        
    } catch (Exception $e) {
        $spexcon->rollback();
        throw $e;
    }
}

function getUsageHistory() {
    global $spexcon;
    
    $whereConditions = [];
    $params = [];
    $types = '';
    
    if (!empty($_POST['userId'])) {
        $whereConditions[] = "upu.user_id = ?";
        $params[] = (int)$_POST['userId'];
        $types .= 'i';
    }
    
    if (!empty($_POST['promoCode'])) {
        $whereConditions[] = "upu.promo_code LIKE ?";
        $params[] = '%' . $_POST['promoCode'] . '%';
        $types .= 's';
    }
    
    if (!empty($_POST['rewardType'])) {
        $whereConditions[] = "upu.reward_type = ?";
        $params[] = $_POST['rewardType'];
        $types .= 's';
    }
    
    $whereClause = '';
    if (!empty($whereConditions)) {
        $whereClause = ' WHERE ' . implode(' AND ', $whereConditions);
    }
    
    $query = $spexcon->prepare("
        SELECT upu.*, u.mail as user_email, pc.description
        FROM user_promo_usage upu
        LEFT JOIN users u ON upu.user_id = u.uid
        LEFT JOIN promo_codes pc ON upu.promo_code_id = pc.id
        $whereClause
        ORDER BY upu.used_at DESC
        LIMIT 100
    ");
    
    if (!empty($params)) {
        $query->bind_param($types, ...$params);
    }
    
    $query->execute();
    $result = $query->get_result();
    
    $usage = [];
    while ($row = $result->fetch_assoc()) {
        $usage[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $usage]);
}

function deleteUsage() {
    global $spexcon;
    
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        throw new Exception('Usage ID is required');
    }
    
    $getUsageQuery = $spexcon->prepare("
        SELECT upu.*, pc.current_usage 
        FROM user_promo_usage upu
        JOIN promo_codes pc ON upu.promo_code_id = pc.id
        WHERE upu.id = ?
    ");
    $getUsageQuery->bind_param('i', $id);
    $getUsageQuery->execute();
    $usageResult = $getUsageQuery->get_result();
    $usage = $usageResult->fetch_assoc();
    
    if (!$usage) {
        throw new Exception('Usage record not found');
    }
    
    $spexcon->begin_transaction();
    
    try {
        $deleteQuery = $spexcon->prepare("DELETE FROM user_promo_usage WHERE id = ?");
        $deleteQuery->bind_param('i', $id);
        $deleteQuery->execute();
        
        $updateCountQuery = $spexcon->prepare("
            UPDATE promo_codes 
            SET current_usage = GREATEST(0, current_usage - 1) 
            WHERE id = ?
        ");
        $updateCountQuery->bind_param('i', $usage['promo_code_id']);
        $updateCountQuery->execute();
        
        $spexcon->commit();
        echo json_encode(['success' => true, 'message' => 'Usage record deleted successfully']);
        
    } catch (Exception $e) {
        $spexcon->rollback();
        throw $e;
    }
}
?>