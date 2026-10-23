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
        case 'getDrops':
            getDrops();
            break;
        case 'getDropStats':
            getDropStats();
            break;
        case 'simulateDrop':
            simulateDrop();
            break;
        case 'cancelDrop':
            cancelDrop();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
} catch (Exception $e) {
   echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getDrops() {
   global $spexcon;
   
   $whereConditions = [];
   $params = [];
   $types = '';
   
   // Status filter
   if (isset($_POST['status']) && $_POST['status'] !== '') {
       $whereConditions[] = "d.is_claimed = ?";
       $params[] = (int)$_POST['status'];
       $types .= 'i';
   }
   
   // Rarity filter
   if (!empty($_POST['rarity'])) {
       $whereConditions[] = "d.rarity = ?";
       $params[] = $_POST['rarity'];
       $types .= 's';
   }
   
   // User filter
   if (!empty($_POST['userId'])) {
       $whereConditions[] = "(d.user_id = ? OR u.mail LIKE ?)";
       $params[] = (int)$_POST['userId'];
       $params[] = '%' . $_POST['userId'] . '%';
       $types .= 'is';
   }
   
   $whereClause = '';
   if (!empty($whereConditions)) {
       $whereClause = ' WHERE ' . implode(' AND ', $whereConditions);
   }
   
   $query = $spexcon->prepare("
       SELECT d.*, b.box_name, u.mail as user_email
       FROM user_mystery_drops d
       JOIN mystery_boxes b ON d.box_id = b.id
       JOIN users u ON d.user_id = u.uid
       $whereClause
       ORDER BY d.dropped_at DESC
       LIMIT 100
   ");
   
   if (!empty($params)) {
       $query->bind_param($types, ...$params);
   }
   
   $query->execute();
   $result = $query->get_result();
   
   $drops = [];
   while ($row = $result->fetch_assoc()) {
       $drops[] = $row;
   }
   
   echo json_encode(['success' => true, 'data' => $drops]);
}

function getDropStats() {
   global $spexcon;
   
   $stats = [];
   
   // Total drops
   $query = $spexcon->query("SELECT COUNT(*) as count FROM user_mystery_drops");
   $stats['total_drops'] = $query ? $query->fetch_assoc()['count'] : 0;
   
   // Claimed drops
   $query = $spexcon->query("SELECT COUNT(*) as count FROM user_mystery_drops WHERE is_claimed = 1");
   $stats['claimed_drops'] = $query ? $query->fetch_assoc()['count'] : 0;
   
   // Unclaimed drops
   $query = $spexcon->query("SELECT COUNT(*) as count FROM user_mystery_drops WHERE is_claimed = 0");
   $stats['unclaimed_drops'] = $query ? $query->fetch_assoc()['count'] : 0;
   
   // Today drops
   $query = $spexcon->query("SELECT COUNT(*) as count FROM user_mystery_drops WHERE DATE(dropped_at) = CURDATE()");
   $stats['today_drops'] = $query ? $query->fetch_assoc()['count'] : 0;
   
   echo json_encode(['success' => true, 'data' => $stats]);
}

function simulateDrop() {
   global $spexcon;
   
   $userInput = $_POST['userId'] ?? '';
   $boxId = (int)($_POST['boxId'] ?? 0);
   
   if (empty($userInput)) {
       throw new Exception('User ID or email is required');
   }
   
   // Find user
   if (is_numeric($userInput)) {
       $userQuery = $spexcon->prepare("SELECT uid FROM users WHERE uid = ? AND disablead = 0");
       $userQuery->bind_param('i', $userInput);
   } else {
       $userQuery = $spexcon->prepare("SELECT uid FROM users WHERE mail = ? AND disablead = 0");
       $userQuery->bind_param('s', $userInput);
   }
   
   $userQuery->execute();
   $userResult = $userQuery->get_result();
   $user = $userResult->fetch_assoc();
   
   if (!$user) {
       throw new Exception('User not found');
   }
   
   $userId = $user['uid'];
   
   // Get box (random if not specified)
   if ($boxId > 0) {
       $boxQuery = $spexcon->prepare("SELECT * FROM mystery_boxes WHERE id = ? AND is_active = 1");
       $boxQuery->bind_param('i', $boxId);
   } else {
       $boxQuery = $spexcon->prepare("SELECT * FROM mystery_boxes WHERE is_active = 1 ORDER BY RAND() LIMIT 1");
   }
   
   $boxQuery->execute();
   $boxResult = $boxQuery->get_result();
   $box = $boxResult->fetch_assoc();
   
   if (!$box) {
       throw new Exception('No active mystery boxes found');
   }
   
   $reward = generateRandomReward($box);
   
   // Insert drop
   $insertQuery = $spexcon->prepare("
       INSERT INTO user_mystery_drops 
       (user_id, box_id, reward_type, coin_symbol, reward_amount, reward_usd_value, rarity, ip_address, user_agent) 
       VALUES (?, ?, ?, ?, ?, ?, ?, 'ADMIN_SIMULATE', 'ADMIN_PANEL')
   ");
   
   $insertQuery->bind_param(
       'iissdds',
       $userId,
       $box['id'],
       $reward['reward_type'],
       $reward['coin_symbol'],
       $reward['reward_amount'],
       $reward['reward_usd_value'],
       $reward['rarity']
   );
   
   $insertQuery->execute();
   
   $rewardText = $reward['reward_type'] === 'usd' ? 
       '$' . $reward['reward_usd_value'] : 
       $reward['reward_amount'] . ' ' . $reward['coin_symbol'];
   
   echo json_encode([
       'success' => true, 
       'message' => "Drop simulated successfully! User {$userId} received {$rewardText} ({$reward['rarity']})"
   ]);
}

function cancelDrop() {
   global $spexcon;
   
   $dropId = (int)($_POST['dropId'] ?? 0);
   if (!$dropId) {
       throw new Exception('Drop ID is required');
   }
   
   // Check if drop exists and is unclaimed
   $checkQuery = $spexcon->prepare("SELECT id FROM user_mystery_drops WHERE id = ? AND is_claimed = 0");
   $checkQuery->bind_param('i', $dropId);
   $checkQuery->execute();
   $checkResult = $checkQuery->get_result();
   
   if (!$checkResult->fetch_assoc()) {
       throw new Exception('Drop not found or already claimed');
   }
   
   // Delete the drop
   $deleteQuery = $spexcon->prepare("DELETE FROM user_mystery_drops WHERE id = ?");
   $deleteQuery->bind_param('i', $dropId);
   $deleteQuery->execute();
   
   echo json_encode(['success' => true, 'message' => 'Drop cancelled successfully']);
}

function generateRandomReward($box) {
    $random = mt_rand(1, 10000) / 100;
    
    $rarity = 'common';
    if ($random <= $box['rarity_legendary_chance']) {
        $rarity = 'legendary';
    } else if ($random <= $box['rarity_legendary_chance'] + $box['rarity_epic_chance']) {
        $rarity = 'epic';
    } else if ($random <= $box['rarity_legendary_chance'] + $box['rarity_epic_chance'] + $box['rarity_rare_chance']) {
        $rarity = 'rare';
    }
    
    $rewardType = mt_rand(1, 100) <= 70 ? 'usd' : 'coin';
    
    $multiplier = 1;
    switch ($rarity) {
        case 'legendary': $multiplier = 5; break;
        case 'epic': $multiplier = 3; break;
        case 'rare': $multiplier = 2; break;
        case 'common': $multiplier = 1; break;
    }
    
    if ($rewardType === 'usd') {
        $baseAmount = mt_rand((int)($box['min_usd_reward'] * 100), (int)($box['max_usd_reward'] * 100)) / 100;
        $usdAmount = $baseAmount * $multiplier;
        
        return [
            'reward_type' => 'usd',
            'coin_symbol' => null,
            'reward_amount' => 0,
            'reward_usd_value' => round($usdAmount, 2),
            'rarity' => $rarity
        ];
    } else {
        $coinRewards = json_decode($box['coin_rewards'], true);
        if (empty($coinRewards)) {
            $baseAmount = mt_rand((int)($box['min_usd_reward'] * 100), (int)($box['max_usd_reward'] * 100)) / 100;
            $usdAmount = $baseAmount * $multiplier;
            
            return [
                'reward_type' => 'usd',
                'coin_symbol' => null,
                'reward_amount' => 0,
                'reward_usd_value' => round($usdAmount, 2),
                'rarity' => $rarity
            ];
        }
        
        $coinSymbol = array_rand($coinRewards);
        $baseCoinAmount = $coinRewards[$coinSymbol];
        $coinAmount = $baseCoinAmount * $multiplier;
        
        $coinPrice = getCurrentCoinPrice($coinSymbol);
        $usdValue = $coinAmount * $coinPrice;
        
        return [
            'reward_type' => 'coin',
            'coin_symbol' => $coinSymbol,
            'reward_amount' => $coinAmount,
            'reward_usd_value' => round($usdValue, 2),
            'rarity' => $rarity
        ];
    }
}

function getCurrentCoinPrice($coinSymbol) {
    global $spexcon;
    
    $query = $spexcon->prepare("
        SELECT current_price_usd 
        FROM coin_market_data 
        WHERE coin_id = ? OR UPPER(coin_id) = UPPER(?)
        ORDER BY last_updated DESC 
        LIMIT 1
    ");
    
    $coinId = strtolower($coinSymbol);
    $query->bind_param('ss', $coinId, $coinSymbol);
    $query->execute();
    $result = $query->get_result();
    $data = $result->fetch_assoc();
    
    return $data ? (float)$data['current_price_usd'] : 1.0;
}
?>