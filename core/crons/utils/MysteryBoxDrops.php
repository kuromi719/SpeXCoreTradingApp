<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';

global $spexcon;

processAutomaticDrops();

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

echo "Mystery box drops processed at " . date('Y-m-d H:i:s') . "\n";
?>