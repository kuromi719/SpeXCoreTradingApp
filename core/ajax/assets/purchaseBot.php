<?php
header('Content-Type: text/html');

require_once __DIR__ . '/../../system/spex_v4_func.php';

if (!getSession('Login')) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$UserUID = getSession('UID');

$UserData = FetchUserData($UserUID);
if ($UserData['balance_blocked'] == 1) {
    echo json_encode(['success' => false, 'message' => 'Your Balance is Locked by System']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['bot_id'], $input['rental_duration_hours'], $input['payment_amount'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        exit;
    }

    $botId = intval($input['bot_id']);
    $rentalDurationHours = intval($input['rental_duration_hours']);
    $payment_amount = floatval($input['payment_amount']);

    if ($rentalDurationHours <= 0 || $payment_amount <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid amounts']);
        exit;
    }

    $spexcon->autocommit(false);

    $botQuery = "
        SELECT name, hourly_profit_percentage, is_active, rental_prices
        FROM trading_bots 
        WHERE id = ? AND is_active = 1
    ";
    $stmt = $spexcon->prepare($botQuery);
    $stmt->bind_param("i", $botId);
    $stmt->execute();
    $botResult = $stmt->get_result()->fetch_assoc();

    if (!$botResult) {
        $spexcon->rollback();
        $spexcon->autocommit(true);
        echo json_encode(['success' => false, 'message' => 'Bot not found or inactive']);
        exit;
    }

    $rentalPrices = json_decode($botResult['rental_prices'], true);
    if (!isset($rentalPrices[$rentalDurationHours]) || $rentalPrices[$rentalDurationHours] != $payment_amount) {
        $spexcon->rollback();
        $spexcon->autocommit(true);
        echo json_encode(['success' => false, 'message' => 'Invalid package selection']);
        exit;
    }

    $balanceQuery = "SELECT balance FROM users WHERE uid = ?";
    $stmt = $spexcon->prepare($balanceQuery);
    $stmt->bind_param("i", $UserUID);
    $stmt->execute();
    $balanceResult = $stmt->get_result()->fetch_assoc();
    $userBalance = $balanceResult['balance'] ?? 0;

    if ($userBalance < $payment_amount) {
        $spexcon->rollback();
        $spexcon->autocommit(true);
        echo json_encode(['success' => false, 'message' => 'Insufficient balance']);
        exit;
    }

    $deductBalanceQuery = "UPDATE users SET balance = balance - ? WHERE uid = ?";
    $stmt = $spexcon->prepare($deductBalanceQuery);
    $stmt->bind_param("di", $payment_amount, $UserUID);
    
    if (!$stmt->execute()) {
        $spexcon->rollback();
        $spexcon->autocommit(true);
        echo json_encode(['success' => false, 'message' => 'Failed to update balance']);
        exit;
    }

    $subscriptionEndTime = date('Y-m-d H:i:s', strtotime("+{$rentalDurationHours} hours"));

    $insertSubscriptionQuery = "
        INSERT INTO user_bot_subscriptions 
        (user_id, bot_id, rental_duration_hours, hourly_profit_rate, subscription_end, payment_amount, status) 
        VALUES (?, ?, ?, ?, ?, ?, 'ACTIVE')
    ";
    $stmt = $spexcon->prepare($insertSubscriptionQuery);
    $stmt->bind_param("iiidsd", 
        $UserUID,
        $botId,
        $rentalDurationHours,
        $botResult['hourly_profit_percentage'],
        $subscriptionEndTime,
        $payment_amount
    );
    
    if (!$stmt->execute()) {
        $spexcon->rollback();
        $spexcon->autocommit(true);
        echo json_encode(['success' => false, 'message' => 'Failed to create subscription']);
        exit;
    }

    $updateBotStatsQuery = "UPDATE trading_bots SET total_users = total_users + 1 WHERE id = ?";
    $stmt = $spexcon->prepare($updateBotStatsQuery);
    $stmt->bind_param("i", $botId);
    $stmt->execute();

    try {
        $transactionQuery = "
            INSERT INTO user_transactions 
            (user_id, coin_id, transaction_type, amount, price_per_unit_usd, total_value_usd, status) 
            VALUES (?, 'usdt', 'BOT_PURCHASE', ?, 1.0, ?, 'COMPLETED')
        ";
        $stmt = $spexcon->prepare($transactionQuery);
        if ($stmt) {
            $stmt->bind_param("idd", $UserUID, $payment_amount, $payment_amount);
            $stmt->execute();
        }
    } catch (Exception $e) {
        error_log("Transaction log failed: " . $e->getMessage());
    }

    try {
        $logMsg = "Purchased {$botResult['name']} trading bot package for {$rentalDurationHours} hours";
        $logAmount = number_format($payment_amount, 2);
        
        $logQuery = "INSERT INTO user_logs (uid, type, msg, amount, cat) VALUES (?, 'Bot Purchase', ?, ?, 'bot')";
        $stmt = $spexcon->prepare($logQuery);
        if ($stmt) {
            $stmt->bind_param("sss", $UserUID, $logMsg, $logAmount);
            $stmt->execute();
        }
    } catch (Exception $e) {
        error_log("User log failed: " . $e->getMessage());
    }

    try {
        if (function_exists('PushSend')) {
            PushSend($UserUID, "Bot Package Purchased", "Your {$botResult['name']} trading bot package is now active!", 'bot');
        }
    } catch (Exception $e) {
        error_log("Push notification failed: " . $e->getMessage());
    }

    $spexcon->commit();
    $spexcon->autocommit(true);

    echo json_encode([
        'success' => true,
        'message' => 'Trading bot package purchased successfully',
        'data' => [
            'bot_name' => $botResult['name'],
            'payment_amount' => $payment_amount,
            'rental_duration' => $rentalDurationHours,
            'hourly_profit_rate' => $botResult['hourly_profit_percentage'],
            'subscription_end' => $subscriptionEndTime
        ]
    ]);

} catch (Exception $e) {
    if (isset($spexcon)) {
        $spexcon->rollback();
        $spexcon->autocommit(true);
    }
    error_log("Purchase Bot Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error processing purchase']);
}
?>