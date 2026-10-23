<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to continue']);
    exit;
}

$UID = getSession('UID');
$userData = FetchUserData($UID);

if ($userData['balance_blocked'] == 1) {
    echo json_encode(['success' => false, 'message' => 'Your Balance is Locked by System']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    switch($action) {
        case 'redeemPromoCode':
            handleRedeemPromoCode($UID);
            break;
        case 'getUserPromoHistory':
            handleGetUserPromoHistory($UID);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function handleRedeemPromoCode($userId) {
    global $spexcon;
    
    $promoCode = strtoupper(trim($_POST['promoCode'] ?? ''));
    
    if (empty($promoCode)) {
        throw new Exception('Promo code is required');
    }
    
    if (strlen($promoCode) < 3 || strlen($promoCode) > 50) {
        throw new Exception('Invalid promo code format');
    }
    
    $spexcon->begin_transaction();
    
    try {
        $promoQuery = $spexcon->prepare("
            SELECT * FROM promo_codes 
            WHERE code = ? AND is_active = 1
        ");
        $promoQuery->bind_param('s', $promoCode);
        $promoQuery->execute();
        $promoResult = $promoQuery->get_result();
        $promo = $promoResult->fetch_assoc();
        
        if (!$promo) {
            throw new Exception('Invalid or inactive promo code');
        }
        
        if ($promo['expires_at'] && strtotime($promo['expires_at']) < time()) {
            throw new Exception('This promo code has expired');
        }
        
        $usageCheckQuery = $spexcon->prepare("
            SELECT id FROM user_promo_usage 
            WHERE user_id = ? AND promo_code_id = ?
        ");
        $usageCheckQuery->bind_param('ii', $userId, $promo['id']);
        $usageCheckQuery->execute();
        $usageCheckResult = $usageCheckQuery->get_result();
        
        if ($usageCheckResult->fetch_assoc()) {
            throw new Exception('You have already used this promo code');
        }
        
        if ($promo['max_usage'] > 0 && $promo['current_usage'] >= $promo['max_usage']) {
            throw new Exception('This promo code has reached its usage limit');
        }
        
        if ($promo['code_type'] === 'usd') {
            addUserUSDReward($userId, $promo['reward_usd_value'], $promoCode);
        } else {
            addUserCoinReward($userId, $promo['coin_id'], $promo['reward_amount'], $promoCode);
        }
        
        $usageQuery = $spexcon->prepare("
            INSERT INTO user_promo_usage (
                user_id, promo_code_id, promo_code, reward_type, 
                coin_id, reward_amount, reward_usd_value, 
                ip_address, user_agent
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $usageQuery->bind_param(
            'iissddsss',  // i=int, s=string, d=double - 9 param
            $userId,                    // i
            $promo['id'],              // i 
            $promoCode,                // s
            $promo['code_type'],       // s
            $promo['coin_id'],         // d -> s (string)
            $promo['reward_amount'],   // d
            $promo['reward_usd_value'], // d
            $ip,                       // s
            $userAgent                 // s
        );
        $usageQuery->execute();
        
        $updateUsageQuery = $spexcon->prepare("
            UPDATE promo_codes 
            SET current_usage = current_usage + 1 
            WHERE id = ?
        ");
        $updateUsageQuery->bind_param('i', $promo['id']);
        $updateUsageQuery->execute();
        
        $spexcon->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Promo code redeemed successfully!',
            'data' => [
                'reward_type' => $promo['code_type'],
                'coin_symbol' => $promo['coin_id'],
                'reward_amount' => $promo['reward_amount'],
                'reward_usd_value' => $promo['reward_usd_value'],
                'description' => $promo['description']
            ]
        ]);
        
    } catch (Exception $e) {
        $spexcon->rollback();
        throw $e;
    }
}

function handleGetUserPromoHistory($userId) {
    global $spexcon;
    
    $query = $spexcon->prepare("
        SELECT upu.*, pc.description 
        FROM user_promo_usage upu
        JOIN promo_codes pc ON upu.promo_code_id = pc.id
        WHERE upu.user_id = ?
        ORDER BY upu.used_at DESC
        LIMIT 20
    ");
    
    $query->bind_param('i', $userId);
    $query->execute();
    $result = $query->get_result();
    
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $history]);
}

function addUserUSDReward($userId, $amount, $promoCode) {
    global $spexcon;
    
    $balanceQuery = $spexcon->prepare("UPDATE users SET balance = balance + ? WHERE uid = ?");
    $balanceQuery->bind_param('di', $amount, $userId);
    $balanceQuery->execute();
    
    $logQuery = $spexcon->prepare("
        INSERT INTO user_logs (uid, type, msg, cat, amount, date) 
        VALUES (?, 'Promo Code Bonus', ?, 'rew', ?, NOW())
    ");
    
    $logMessage = "Promo Code Redeemed: {$promoCode} - \${$amount} USD Bonus";
    $logQuery->bind_param('isd', $userId, $logMessage, $amount);
    $logQuery->execute();
}

function addUserCoinReward($userId, $coinId, $amount, $promoCode) {
    global $spexcon;
    
    $fullCoinId = getCoinIdBySymbol($coinId);
    if (!$fullCoinId) {
        $fullCoinId = strtolower($coinId); // Fallback
    }
    
    $balanceQuery = $spexcon->prepare("
        INSERT INTO user_coin_balances (user_id, coin_id, total_balance, available_balance, updated_at)
        VALUES (?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE
        total_balance = total_balance + VALUES(total_balance),
        available_balance = available_balance + VALUES(available_balance),
        updated_at = NOW()
    ");
    
    $balanceQuery->bind_param('isdd', $userId, $fullCoinId, $amount, $amount);
    $balanceQuery->execute();
    
    $currentPrice = getCurrentCoinPrice($coinId);
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
    
    $notes = "Promo Code Reward: {$promoCode} - {$amount} {$coinId}";
    $transactionQuery->bind_param('isdddds', $userId, $fullCoinId, $amount, $amount, $currentPrice, $totalUsdValue, $notes);
    $transactionQuery->execute();
}

function getCoinIdBySymbol($symbol) {
    global $spexcon;
    
    $symbolMap = [
        'BTC' => 'bitcoin',
        'ETH' => 'ethereum', 
        'BNB' => 'binancecoin',
        'ADA' => 'cardano',
        'SOL' => 'solana',
        'DOT' => 'polkadot',
        'MATIC' => 'matic-network',
        'AVAX' => 'avalanche-2',
        'LINK' => 'chainlink',
        'UNI' => 'uniswap'
    ];
    
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

function isLoggedIn() {
    return !empty(getSession('UID')) && !empty(getSession('Login'));
}

?>