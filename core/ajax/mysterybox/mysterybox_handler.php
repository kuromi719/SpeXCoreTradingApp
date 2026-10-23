<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';


$UID = getSession('UID');
if (empty($UID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userData = FetchUserData($UID);
    
if ($userData['balance_blocked'] == 1) {
    echo json_encode(['success' => false, 'message' => 'Your Balance is Locked by System']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    switch($action) {
        case 'checkSystem':
            handleCheckSystem();
            break;
        case 'checkDrops':
            handleCheckDrops($UID);
            break;
        case 'getUnclaimedDrop':
            handleGetUnclaimedDrop($UID);
            break;
        case 'claimDrop':
            handleClaimDrop($UID);
            break;
        case 'simulateDrop':
            handleSimulateDrop($UID); // for test
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function handleCheckSystem() {
    global $spexcon;
    
    $query = $spexcon->prepare("SELECT system_active FROM mystery_box_settings ORDER BY id DESC LIMIT 1");
    $query->execute();
    $result = $query->get_result();
    $settings = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'is_active' => $settings ? (bool)$settings['system_active'] : false
        ]
    ]);
}

function handleCheckDrops($userId) {
    global $spexcon;
    
    $query = $spexcon->prepare("
        SELECT COUNT(*) as count 
        FROM user_mystery_drops 
        WHERE user_id = ? AND is_claimed = 0
    ");
    $query->bind_param('i', $userId);
    $query->execute();
    $result = $query->get_result();
    $data = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'hasUnclaimedDrops' => $data['count'] > 0,
            'count' => (int)$data['count']
        ]
    ]);
}

function handleGetUnclaimedDrop($userId) {
    global $spexcon;
    
    $query = $spexcon->prepare("
        SELECT d.*, b.box_name, b.box_description
        FROM user_mystery_drops d
        JOIN mystery_boxes b ON d.box_id = b.id
        WHERE d.user_id = ? AND d.is_claimed = 0
        ORDER BY d.dropped_at ASC
        LIMIT 1
    ");
    $query->bind_param('i', $userId);
    $query->execute();
    $result = $query->get_result();
    $drop = $result->fetch_assoc();
    
    if (!$drop) {
        echo json_encode(['success' => false, 'message' => 'No unclaimed drops found']);
        return;
    }
    
    echo json_encode(['success' => true, 'data' => $drop]);
}

function handleClaimDrop($userId) {
    global $spexcon;
    
    $spexcon->begin_transaction();
    
    try {
        $query = $spexcon->prepare("
            SELECT d.*, b.box_name
            FROM user_mystery_drops d
            JOIN mystery_boxes b ON d.box_id = b.id
            WHERE d.user_id = ? AND d.is_claimed = 0
            ORDER BY d.dropped_at ASC
            LIMIT 1
            FOR UPDATE
        ");
        $query->bind_param('i', $userId);
        $query->execute();
        $result = $query->get_result();
        $drop = $result->fetch_assoc();
        
        if (!$drop) {
            throw new Exception('No unclaimed drops found');
        }
        
        $updateQuery = $spexcon->prepare("
            UPDATE user_mystery_drops 
            SET is_claimed = 1, claimed_at = NOW() 
            WHERE id = ?
        ");
        $updateQuery->bind_param('i', $drop['id']);
        $updateQuery->execute();
        
        if ($drop['reward_type'] === 'usd') {
            addUserUSDBalance($userId, $drop['reward_usd_value']);
        } else if ($drop['reward_type'] === 'coin') {
            addUserCoinBalance($userId, $drop['coin_symbol'], $drop['reward_amount']);
        }
        
        $rewardText = $drop['reward_type'] === 'usd' ? 
            '$' . $drop['reward_usd_value'] : 
            $drop['reward_amount'] . ' ' . $drop['coin_symbol'];
            
        $rewDatascBalance = $drop['reward_type'] === 'usd' ? $drop['reward_usd_value'] : $drop['reward_usd_value'];
        logUserActivity($userId, 'rew', "Mystery Box Claimed: {$drop['box_name']} - {$drop['rarity']} ({$rewardText})",'null');
        
        $notificationText = "You received a {$drop['rarity']} reward: {$rewardText}";
        sendPushNotification($userId, 'Mystery Box Reward!', $notificationText);
        
        $spexcon->commit();
        
        echo json_encode([
            'success' => true,
            'data' => [
                'reward_type' => $drop['reward_type'],
                'coin_symbol' => $drop['coin_symbol'],
                'reward_amount' => $drop['reward_amount'],
                'reward_usd_value' => $drop['reward_usd_value'],
                'rarity' => $drop['rarity'],
                'box_name' => $drop['box_name']
            ]
        ]);
        
    } catch (Exception $e) {
        $spexcon->rollback();
        throw $e;
    }
}

function handleSimulateDrop($userId) {
    global $spexcon;
    
    $boxQuery = $spexcon->prepare("SELECT * FROM mystery_boxes WHERE is_active = 1 ORDER BY RAND() LIMIT 1");
    $boxQuery->execute();
    $result = $boxQuery->get_result();
    $box = $result->fetch_assoc();
    
    if (!$box) {
        throw new Exception('No active mystery boxes found');
    }
    
    $reward = generateRandomReward($box);
    
    $insertQuery = $spexcon->prepare("
        INSERT INTO user_mystery_drops 
        (user_id, box_id, reward_type, coin_symbol, reward_amount, reward_usd_value, rarity, ip_address, user_agent) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $insertQuery->bind_param(
        'iissddsss',
        $userId,
        $box['id'],
        $reward['reward_type'],
        $reward['coin_symbol'],
        $reward['reward_amount'],
        $reward['reward_usd_value'],
        $reward['rarity'],
        $ip,
        $userAgent
    );
    
    $insertQuery->execute();
    
    echo json_encode([
        'success' => true,
        'message' => 'Mystery box dropped!',
        'data' => [
            'box_name' => $box['box_name'],
            'rarity' => $reward['rarity']
        ]
    ]);
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

function addUserUSDBalance($userId, $amount) {
    global $spexcon;
    $query = $spexcon->prepare("UPDATE users SET balance = balance + ? WHERE uid = ?");
    $query->bind_param('di', $amount, $userId);
    $query->execute();
}

function getCoinIdBySymbol($symbol) {
    global $spexcon;
    
    $upperSymbol = strtoupper($symbol);
    
    if (isset($symbolMap[$upperSymbol])) {
        return $symbolMap[$upperSymbol];
    }
    
    $query = $spexcon->prepare("
        SELECT id 
        FROM coins 
        WHERE UPPER(symbol) = UPPER(?) 
        LIMIT 1
    ");
    
    $query->bind_param('s', $symbol);
    $query->execute();
    $result = $query->get_result();
    $coin = $result->fetch_assoc();
    
    return $coin ? $coin['id'] : null;
}

function addUserCoinBalance($userId, $coinSymbol, $amount) {
    global $spexcon;
    
    $coinId = getCoinIdBySymbol($coinSymbol);
    
    if (!$coinId) {
        throw new Exception("Coin not found: " . $coinSymbol);
    }
    
    $spexcon->begin_transaction();
    
    try {
        $balanceQuery = $spexcon->prepare("
            INSERT INTO user_coin_balances (user_id, coin_id, total_balance, available_balance, updated_at)
            VALUES (?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE
            total_balance = total_balance + VALUES(total_balance),
            available_balance = available_balance + VALUES(available_balance),
            updated_at = NOW()
        ");
        
        $balanceQuery->bind_param('isdd', $userId, $coinId, $amount, $amount);
        $balanceQuery->execute();
        
        $currentPrice = getCurrentCoinPrice($coinSymbol);
        $totalUsdValue = $amount * $currentPrice;
        
        $transactionQuery = $spexcon->prepare("
            INSERT INTO user_transactions (
                user_id, 
                coin_id,
                transaction_type, 
                order_side,
                amount, 
                filled_quantity,
                remaining_quantity,
                price_per_unit_usd,
                total_value_usd, 
                status, 
                notes,
                created_at,
                executed_at
            ) VALUES (?, ?, 'REWARD', 'BUY', ?, ?, 0.00000000, ?, ?, 'COMPLETED', ?, NOW(), NOW())
        ");
        
        $notes = "Mystery Box Reward: {$amount} {$coinSymbol}";
        $transactionQuery->bind_param('isdddds', $userId, $coinId, $amount, $amount, $currentPrice, $totalUsdValue, $notes);
        $transactionQuery->execute();
        
        $spexcon->commit();
        
    } catch (Exception $e) {
        $spexcon->rollback();
        throw $e;
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

function sendPushNotification($userId, $title, $message) {
    PushSend($userId, $title, $message, 'reward');
}

function checkUserBalanceBlocked($userId) {
    global $spexcon;
    $query = $spexcon->prepare("SELECT blocked FROM users WHERE uid = ?");
    $query->bind_param('i', $userId);
    $query->execute();
    $result = $query->get_result();
    $user = $result->fetch_assoc();
    
    return $user && $user['blocked'] == 1;
}

function logUserActivity($userId, $type, $description, $amo) {
    global $spexcon;
    $query = $spexcon->prepare("
        INSERT INTO user_logs (uid, type, msg, cat, amount, date) 
        VALUES (?, 'Mystery Box', ?, 'rew', ?, NOW())
    ");
    $query->bind_param('iss', $userId, $description, $amo);
    $query->execute();
}

function isUserEligibleForDrops($userId) {
    global $spexcon;
    
    $settingsQuery = $spexcon->prepare("SELECT use_eligible_users_only FROM mystery_box_settings ORDER BY id DESC LIMIT 1");
    $settingsQuery->execute();
    $settingsResult = $settingsQuery->get_result();
    $settings = $settingsResult->fetch_assoc();
    
    if (!$settings || !$settings['use_eligible_users_only']) {
        return true;
    }
    
    $eligibleQuery = $spexcon->prepare("
        SELECT COUNT(*) as count 
        FROM mystery_box_eligible_users 
        WHERE user_id = ? AND is_active = 1
    ");
    $eligibleQuery->bind_param('i', $userId);
    $eligibleQuery->execute();
    $eligibleResult = $eligibleQuery->get_result();
    $eligibleData = $eligibleResult->fetch_assoc();
    
    return $eligibleData['count'] > 0;
}

function shouldDropBoxForUser($userId) {
    global $spexcon;
    
    $settingsQuery = $spexcon->prepare("SELECT * FROM mystery_box_settings ORDER BY id DESC LIMIT 1");
    $settingsQuery->execute();
    $settingsResult = $settingsQuery->get_result();
    $settings = $settingsResult->fetch_assoc();
    
    if (!$settings || !$settings['system_active']) {
        return false;
    }
    
    if (!isUserEligibleForDrops($userId)) {
        return false;
    }
    
    $todayDropsQuery = $spexcon->prepare("
        SELECT COUNT(*) as count 
        FROM user_mystery_drops 
        WHERE user_id = ? AND DATE(dropped_at) = CURDATE()
    ");
    $todayDropsQuery->bind_param('i', $userId);
    $todayDropsQuery->execute();
    $todayDropsResult = $todayDropsQuery->get_result();
    $todayDropsData = $todayDropsResult->fetch_assoc();
    
    if ($todayDropsData['count'] >= $settings['max_daily_drops']) {
        return false;
    }
    
    $lastDropQuery = $spexcon->prepare("
        SELECT dropped_at 
        FROM user_mystery_drops 
        WHERE user_id = ? 
        ORDER BY dropped_at DESC 
        LIMIT 1
    ");
    $lastDropQuery->bind_param('i', $userId);
    $lastDropQuery->execute();
    $lastDropResult = $lastDropQuery->get_result();
    $lastDrop = $lastDropResult->fetch_assoc();
    
    if ($lastDrop) {
        $minInterval = $settings['drop_interval_min'];
        $lastDropTime = strtotime($lastDrop['dropped_at']);
        $currentTime = time();
        $timeDifference = ($currentTime - $lastDropTime) / 60;
        
        if ($timeDifference < $minInterval) {
            return false;
        }
    }
    
    $random = mt_rand(1, 10000) / 100;
    return $random <= $settings['drop_chance'];
}

function processAutomaticDrops() {
    global $spexcon;
    
    $settingsQuery = $spexcon->prepare("SELECT * FROM mystery_box_settings WHERE system_active = 1 ORDER BY id DESC LIMIT 1");
    $settingsQuery->execute();
    $settingsResult = $settingsQuery->get_result();
    $settings = $settingsResult->fetch_assoc();
    
    if (!$settings) {
        return;
    }
    
    if ($settings['use_eligible_users_only']) {
        $usersQuery = $spexcon->prepare("
            SELECT DISTINCT u.uid 
            FROM users u
            JOIN mystery_box_eligible_users meu ON u.uid = meu.user_id
            WHERE u.disablead = 0 AND meu.is_active = 1
        ");
    } else {
        $usersQuery = $spexcon->prepare("
            SELECT uid 
            FROM users 
            WHERE disablead = 0 AND balance_blocked = 0
        ");
    }
    
    $usersQuery->execute();
    $usersResult = $usersQuery->get_result();
    
    while ($user = $usersResult->fetch_assoc()) {
        $userId = $user['uid'];
        
        if (shouldDropBoxForUser($userId)) {
            $boxQuery = $spexcon->prepare("SELECT * FROM mystery_boxes WHERE is_active = 1 ORDER BY RAND() LIMIT 1");
            $boxQuery->execute();
            $boxResult = $boxQuery->get_result();
            $box = $boxResult->fetch_assoc();
            
            if ($box) {
                $reward = generateRandomReward($box);
                
                $insertQuery = $spexcon->prepare("
                    INSERT INTO user_mystery_drops 
                    (user_id, box_id, reward_type, coin_symbol, reward_amount, reward_usd_value, rarity, ip_address, user_agent) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'AUTO_DROP', 'SYSTEM_CRON')
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
            }
        }
    }
}
?>