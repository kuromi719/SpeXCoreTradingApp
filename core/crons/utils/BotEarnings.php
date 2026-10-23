<?php
/*
 * SpeX-Bot-Earnings CRON-JOB with Referral System
 * 
 * Time : Every 1h
 * 
*/

require_once __DIR__ . '/../../system/spex_v4_func.php';
global $spexcon;
function BotlogMessage($message) {
    $timestamp = date('Y-m-d H:i:s');
    echo "[$timestamp] $message\n";
    
    $logFile = __DIR__ . '/../logs/bot_earnings.log';
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    if (file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX) === false) {
        error_log("Failed to write to bot earnings log file");
    }
}

function processReferralCommission($userId, $earningAmount, $botName) {
    global $spexcon;
    
    try {
        $refQuery = "SELECT user_uid FROM invitations WHERE ref_uid = ? LIMIT 1";
        $stmt = $spexcon->prepare($refQuery);
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $refResult = $stmt->get_result();
        
        if ($refResult && $refData = $refResult->fetch_assoc()) {
            $referrerUid = $refData['user_uid'];
            
            $countQuery = "SELECT COUNT(*) as ref_count FROM invitations WHERE user_uid = ?";
            $stmt = $spexcon->prepare($countQuery);
            $stmt->bind_param("s", $referrerUid);
            $stmt->execute();
            $countResult = $stmt->get_result();
            $refCount = $countResult->fetch_assoc()['ref_count'];
            
            $settingsQuery = "SELECT ref_lvl_percentage1, ref_lvl_percentage2, ref_lvl_percentage3, 
                             ref_lvl_percentage4, ref_lvl_percentage5 FROM settings WHERE id = 1";
            $settingsResult = $spexcon->query($settingsQuery);
            $settings = $settingsResult->fetch_assoc();
            
            $commissionRate = 0;
            if ($refCount >= 13) {
                $commissionRate = $settings['ref_lvl_percentage5'];
            } elseif ($refCount >= 10) {
                $commissionRate = $settings['ref_lvl_percentage4'];
            } elseif ($refCount >= 6) {
                $commissionRate = $settings['ref_lvl_percentage3'];
            } elseif ($refCount >= 3) {
                $commissionRate = $settings['ref_lvl_percentage2'];
            } elseif ($refCount >= 1) {
                $commissionRate = $settings['ref_lvl_percentage1'];
            }
            
            if ($commissionRate > 0) {
                $commissionAmount = ($earningAmount * $commissionRate) / 100;
                
                $updateBalanceQuery = "UPDATE users SET balance = balance + ? WHERE uid = ?";
                $stmt = $spexcon->prepare($updateBalanceQuery);
                $stmt->bind_param("ds", $commissionAmount, $referrerUid);
                $stmt->execute();
                
                $transactionQuery = "INSERT INTO user_transactions 
                                   (user_id, coin_id, transaction_type, amount, price_per_unit_usd, 
                                    total_value_usd, status, notes) 
                                   VALUES (?, 'usdt', 'REWARD', ?, 1.0, ?, 'COMPLETED', ?)";
                $notes = "Referral commission from user $userId bot earnings ($botName)";
                $stmt = $spexcon->prepare($transactionQuery);
                $stmt->bind_param("sdds", $referrerUid, $commissionAmount, $commissionAmount, $notes);
                $stmt->execute();
                
                $logMsg = "Referral commission earned from bot trading";
                $logAmount = number_format($commissionAmount, 4);
                $logQuery = "INSERT INTO user_logs (uid, type, msg, amount, cat) 
                           VALUES (?, 'referral_bonus', ?, ?, 'ref')";
                $stmt = $spexcon->prepare($logQuery);
                $stmt->bind_param("sss", $referrerUid, $logMsg, $logAmount);
                $stmt->execute();
                
                PushSend($referrerUid, 'Referral Commission', 
                        'You earned $' . number_format($commissionAmount, 4) . ' commission from referral bot earnings', 
                        'referral');
                
                BotlogMessage("Referral commission: $" . number_format($commissionAmount, 4) . 
                          " paid to user {$referrerUid} (rate: {$commissionRate}%)");
                
                return $commissionAmount;
            }
        }
    } catch (Exception $e) {
        BotlogMessage("ERROR processing referral commission: " . $e->getMessage());
    }
    
    return 0;
}

try {
    BotlogMessage("=== Bot Earnings Calculation Started ===");
    
    if (!isset($spexcon) || $spexcon->connect_error) {
        throw new Exception("Database connection failed");
    }
    
  
    $spexcon->autocommit(false);

    $activeSubscriptionsQuery = "
        SELECT 
            ubs.id,
            ubs.user_id,
            ubs.bot_id,
            ubs.payment_amount,
            ubs.hourly_profit_rate,
            ubs.subscription_start,
            ubs.subscription_end,
            ubs.last_earning_time,
            tb.name as bot_name
        FROM user_bot_subscriptions ubs
        LEFT JOIN trading_bots tb ON ubs.bot_id = tb.id
        WHERE ubs.status = 'ACTIVE'
        AND ubs.subscription_end > NOW()
        AND ubs.subscription_start <= NOW()
        AND (
            ubs.last_earning_time IS NULL 
            OR TIMESTAMPDIFF(HOUR, ubs.last_earning_time, NOW()) >= 1
        )
        ORDER BY ubs.id ASC
    ";

    $result = $spexcon->query($activeSubscriptionsQuery);
    
    if (!$result) {
        throw new Exception("Query failed: " . $spexcon->error);
    }

    $processedSubscriptions = 0;
    $totalEarningsGenerated = 0;
    $totalReferralCommissions = 0;
    $errors = 0;

    while ($row = $result->fetch_assoc()) {
        try {
            $subscriptionId = $row['id'];
            $userId = $row['user_id'];
            $botId = $row['bot_id'];
            $paymentAmount = floatval($row['payment_amount']);
            $hourlyProfitRate = floatval($row['hourly_profit_rate']);
            $lastEarningTime = $row['last_earning_time'];
            $subscriptionEnd = $row['subscription_end'];
            $botName = $row['bot_name'] ?? 'Trading Bot';

            $currentTime = new DateTime();
            $endTime = new DateTime($subscriptionEnd);
            
            if ($lastEarningTime) {
                $lastTime = new DateTime($lastEarningTime);
                $hoursDiff = $currentTime->diff($lastTime)->h + ($currentTime->diff($lastTime)->days * 24);
            } else {
                $startTime = new DateTime($row['subscription_start']);
                $hoursDiff = $currentTime->diff($startTime)->h + ($currentTime->diff($startTime)->days * 24);
            }

            $hoursToCalculate = min($hoursDiff, 24);
            
            if ($currentTime > $endTime) {
                $hoursToCalculate = 0;
                if ($lastEarningTime) {
                    $lastTime = new DateTime($lastEarningTime);
                    if ($lastTime < $endTime) {
                        $hoursToCalculate = $endTime->diff($lastTime)->h + ($endTime->diff($lastTime)->days * 24);
                    }
                }
            }

            if ($hoursToCalculate > 0 && $paymentAmount > 0 && $hourlyProfitRate > 0) {
                $earningAmount = ($paymentAmount * $hourlyProfitRate / 100) * $hoursToCalculate;

                if ($earningAmount > 0) {
                    try {
                        $updateQuery = "UPDATE user_bot_subscriptions 
                                      SET last_earning_time = NOW(), 
                                          total_earned = total_earned + ? 
                                      WHERE id = ?";
                        $stmt = $spexcon->prepare($updateQuery);
                        $stmt->bind_param("di", $earningAmount, $subscriptionId);
                        $stmt->execute();

                        $insertEarningQuery = "INSERT INTO user_bot_earnings 
                                             (subscription_id, user_id, bot_id, earning_amount, earning_percentage, earning_hour) 
                                             VALUES (?, ?, ?, ?, ?, NOW())";
                        $stmt = $spexcon->prepare($insertEarningQuery);
                        $stmt->bind_param("iiddd", $subscriptionId, $userId, $botId, $earningAmount, $hourlyProfitRate);
                        $stmt->execute();

                        $updateUserBalanceQuery = "UPDATE users SET balance = balance + ? WHERE uid = ?";
                        $stmt = $spexcon->prepare($updateUserBalanceQuery);
                        $stmt->bind_param("di", $earningAmount, $userId);
                        $stmt->execute();

                        $updateBotStatsQuery = "UPDATE trading_bots 
                                              SET total_profit_generated = total_profit_generated + ? 
                                              WHERE id = ?";
                        $stmt = $spexcon->prepare($updateBotStatsQuery);
                        $stmt->bind_param("di", $earningAmount, $botId);
                        $stmt->execute();

                        $transactionQuery = "INSERT INTO user_transactions 
                                           (user_id, coin_id, transaction_type, amount, price_per_unit_usd, 
                                            total_value_usd, status, notes) 
                                           VALUES (?, 'usdt', 'REWARD', ?, 1.0, ?, 'COMPLETED', ?)";
                        $notes = "Bot earnings from $botName";
                        $stmt = $spexcon->prepare($transactionQuery);
                        $stmt->bind_param("idds", $userId, $earningAmount, $earningAmount, $notes);
                        $stmt->execute();

                        $logMsg = "Earned " . number_format($earningAmount, 4) . " USDT from {$botName} trading bot";
                        $logAmount = number_format($earningAmount, 4);
                        $logQuery = "INSERT INTO user_logs (uid, type, msg, amount, cat) 
                                   VALUES (?, 'Bot Earnings', ?, ?, 'bot')";
                        $stmt = $spexcon->prepare($logQuery);
                        $stmt->bind_param("sss", $userId, $logMsg, $logAmount);
                        $stmt->execute();

                        PushSend($userId, 'Bot Earnings', 
                                "You earned $" . number_format($earningAmount, 4) . " from $botName", 
                                'bot');

                        $processedSubscriptions++;
                        $totalEarningsGenerated += $earningAmount;
                        
                        $referralCommission = processReferralCommission($userId, $earningAmount, $botName);
                        $totalReferralCommissions += $referralCommission;
                        
                        BotlogMessage("Generated $" . number_format($earningAmount, 4) . 
                                  " for user {$userId} from bot {$botName} (package: $" . 
                                  number_format($paymentAmount, 2) . ", rate: {$hourlyProfitRate}%, hours: {$hoursToCalculate})");
                        
                    } catch (Exception $e) {
                        $errors++;
                        BotlogMessage("ERROR processing earnings for subscription {$subscriptionId}: " . $e->getMessage());
                    }
                }
            }
            
        } catch (Exception $e) {
            $errors++;
            BotlogMessage("ERROR processing subscription {$subscriptionId}: " . $e->getMessage());
        }
    }

    try {
        $expiredSubscriptionsQuery = "UPDATE user_bot_subscriptions 
                                    SET status = 'COMPLETED' 
                                    WHERE status = 'ACTIVE' AND subscription_end <= NOW()";
        $expiredResult = $spexcon->query($expiredSubscriptionsQuery);
        $expiredCount = $spexcon->affected_rows;
        
        if ($expiredCount > 0) {
            BotlogMessage("Marked {$expiredCount} subscriptions as COMPLETED");
        }
    } catch (Exception $e) {
        BotlogMessage("ERROR updating expired subscriptions: " . $e->getMessage());
        $expiredCount = 0;
    }

    // Update sysinfo
    try {
        $spexcon->query("INSERT INTO system_info (key_name, value) VALUES ('last_bot_earnings_calc', NOW()) 
                         ON DUPLICATE KEY UPDATE value = NOW()");
    } catch (Exception $e) {
        BotlogMessage("Failed to update system_info: " . $e->getMessage());
    }

    $spexcon->commit();
    $spexcon->autocommit(true);

    BotlogMessage("Processed {$processedSubscriptions} active subscriptions");
    BotlogMessage("Total earnings generated: $" . number_format($totalEarningsGenerated, 4));
    BotlogMessage("Total referral commissions: $" . number_format($totalReferralCommissions, 4));
    BotlogMessage("Expired subscriptions: " . $expiredCount);
    if ($errors > 0) {
        BotlogMessage("Errors encountered: {$errors}");
    }
    BotlogMessage("=== Bot Earnings Calculation Completed ===");

    echo json_encode([
        'success' => true,
        'processed_subscriptions' => $processedSubscriptions,
        'total_earnings_generated' => $totalEarningsGenerated,
        'total_referral_commissions' => $totalReferralCommissions,
        'expired_subscriptions' => $expiredCount,
        'errors' => $errors,
        'timestamp' => date('Y-m-d H:i:s')
    ]);

} catch (Exception $e) {
    if (isset($spexcon)) {
        $spexcon->rollback();
        $spexcon->autocommit(true);
    }
    BotlogMessage("FATAL ERROR: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit(1);
}
?>