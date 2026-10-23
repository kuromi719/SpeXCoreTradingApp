<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';

if (!getSession('Login')) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

if (!isset($_POST['staking_id'])) {
    echo json_encode(['success' => false, 'message' => 'Staking ID required']);
    exit;
}

$UserUID = getSession('UID');

$UserData = FetchUserData($UserUID);
if ($UserData['balance_blocked'] == 1) {
    echo json_encode(['success' => false, 'message' => 'Your Balance is Locked by System']);
    exit;
}

$stakingId = intval($_POST['staking_id']);

try {
    global $spexcon;
    
    if (!$spexcon->autocommit(false)) {
        throw new Exception('Transaction start failed');
    }

    $stakingQuery = "
        SELECT 
            usp.id,
            usp.user_id,
            usp.coin_id,
            usp.staked_amount,
            usp.lock_period_days,
            usp.stake_end_date,
            usp.status,
            COALESCE(c.symbol, usp.coin_id) as symbol
        FROM user_staking_positions usp
        LEFT JOIN coins c ON usp.coin_id = c.id
        WHERE usp.id = ? AND usp.user_id = ? AND usp.status = 'ACTIVE'
    ";
    
    $stmt = $spexcon->prepare($stakingQuery);
    if (!$stmt) {
        throw new Exception('Prepare statement failed: ' . $spexcon->error);
    }
    
    $stmt->bind_param("ii", $stakingId, $UserUID);
    $stmt->execute();
    $stakingResult = $stmt->get_result()->fetch_assoc();

    if (!$stakingResult) {
        $spexcon->rollback();
        $spexcon->autocommit(true);
        echo json_encode(['success' => false, 'message' => 'Staking position not found']);
        exit;
    }

    $penalty = 0;
    $originalAmount = floatval($stakingResult['staked_amount']);
    $finalAmount = $originalAmount;

    if (intval($stakingResult['lock_period_days']) > 0 && $stakingResult['stake_end_date']) {
        $endDate = strtotime($stakingResult['stake_end_date']);
        if (time() < $endDate) {
            $penalty = $originalAmount * 0.02;
            $finalAmount = $originalAmount - $penalty;
        }
    }

    $updateStakingQuery = "
        UPDATE user_staking_positions 
        SET status = 'COMPLETED' 
        WHERE id = ? AND user_id = ?
    ";
    $stmt = $spexcon->prepare($updateStakingQuery);
    if (!$stmt) {
        throw new Exception('Update staking prepare failed: ' . $spexcon->error);
    }
    
    $stmt->bind_param("ii", $stakingId, $UserUID);
    
    if (!$stmt->execute()) {
        throw new Exception('Update staking failed: ' . $stmt->error);
    }

    $checkBalanceQuery = "
        SELECT id, available_balance, locked_balance 
        FROM user_coin_balances 
        WHERE user_id = ? AND coin_id = ?
    ";
    $stmt = $spexcon->prepare($checkBalanceQuery);
    $stmt->bind_param("is", $UserUID, $stakingResult['coin_id']);
    $stmt->execute();
    $balanceData = $stmt->get_result()->fetch_assoc();

    if ($balanceData) {
        $newAvailable = floatval($balanceData['available_balance']) + $finalAmount;
        $newLocked = floatval($balanceData['locked_balance']) - $originalAmount;
        $newTotal = $newAvailable + $newLocked;
        
        $updateBalanceQuery = "
            UPDATE user_coin_balances 
            SET available_balance = ?, locked_balance = ?, total_balance = ?
            WHERE user_id = ? AND coin_id = ?
        ";
        $stmt = $spexcon->prepare($updateBalanceQuery);
        $stmt->bind_param("dddis", $newAvailable, $newLocked, $newTotal, $UserUID, $stakingResult['coin_id']);
    } else {
        $insertBalanceQuery = "
            INSERT INTO user_coin_balances (user_id, coin_id, available_balance, locked_balance, total_balance) 
            VALUES (?, ?, ?, 0, ?)
        ";
        $stmt = $spexcon->prepare($insertBalanceQuery);
        $stmt->bind_param("isdd", $UserUID, $stakingResult['coin_id'], $finalAmount, $finalAmount);
    }

    if (!$stmt->execute()) {
        throw new Exception('Balance update failed: ' . $stmt->error);
    }

    $priceQuery = "SELECT current_price_usd FROM coin_market_data WHERE coin_id = ?";
    $stmt = $spexcon->prepare($priceQuery);
    $stmt->bind_param("s", $stakingResult['coin_id']);
    $stmt->execute();
    $priceResult = $stmt->get_result()->fetch_assoc();
    $currentPrice = $priceResult ? floatval($priceResult['current_price_usd']) : 1.0;

    $transactionQuery = "
        INSERT INTO user_transactions 
        (user_id, coin_id, transaction_type, amount, price_per_unit_usd, total_value_usd, status) 
        VALUES (?, ?, 'TRANSFER_IN', ?, ?, ?, 'COMPLETED')
    ";
    $stmt = $spexcon->prepare($transactionQuery);
    $totalValue = $finalAmount * $currentPrice;
    $stmt->bind_param("isddds", 
        $UserUID, 
        $stakingResult['coin_id'], 
        $finalAmount, 
        $currentPrice, 
        $totalValue,
        'COMPLETED'
    );
    $stmt->execute();

    $coinSymbol = strtoupper($stakingResult['symbol']);
    $logMsg = "Unstaked {$finalAmount} {$coinSymbol}";
    if ($penalty > 0) {
        $logMsg .= " (Penalty: {$penalty} {$coinSymbol})";
    }
    $logAmount = number_format($totalValue, 2);
    
    $logQuery = "INSERT INTO user_logs (uid, type, msg, amount, cat) VALUES (?, 'Unstaking', ?, ?, 'earn')";
    $stmt = $spexcon->prepare($logQuery);
    $stmt->bind_param("sss", $UserUID, $logMsg, $logAmount);
    $stmt->execute();

    if (!$spexcon->commit()) {
        throw new Exception('Transaction commit failed');
    }
    $spexcon->autocommit(true);

    try {
        $message = "Your {$coinSymbol} staking position has been unstaked successfully.";
        if ($penalty > 0) {
            $message .= " Early withdrawal penalty applied.";
        }
        PushSend($UserUID, "Unstaking Complete", $message, 'earn');
    } catch (Exception $e) {
        error_log("Push notification failed: " . $e->getMessage());
    }

    echo json_encode([
        'success' => true,
        'message' => 'Position unstaked successfully',
        'data' => [
            'unstaked_amount' => $finalAmount,
            'original_amount' => $originalAmount,
            'penalty' => $penalty
        ]
    ]);

} catch (Exception $e) {
    if (isset($spexcon)) {
        $spexcon->rollback();
        $spexcon->autocommit(true);
    }
    error_log("Unstake Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Operation failed: ' . $e->getMessage()]);
}
?>