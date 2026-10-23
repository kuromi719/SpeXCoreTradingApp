<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';
header('Content-Type: application/json');

$UID = getSession('UID');

if (empty($UID)) {
   echo json_encode(['success' => false, 'message' => 'User not logged in']);
   exit;
}

$action = $_POST['action'] ?? '';

try {
   switch($action) {
       case 'getJourneys':
           getJourneys();
           break;
       case 'getStats':
           getJourneyStats();
           break;
       case 'claimReward':
           claimReward();
           break;
       case 'updateProgress':
           updateAllProgress();
           break;
       default:
           echo json_encode(['success' => false, 'message' => 'Invalid action']);
   }
} catch (Exception $e) {
   error_log("Journey Ajax Error: " . $e->getMessage());
   echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

function getJourneys() {
   global $spexcon, $UID;
   
   try {
       // Check if journey_settings table exists
       $settingsTableExists = $spexcon->query("SHOW TABLES LIKE 'journey_settings'");
       if(!$settingsTableExists || $settingsTableExists->num_rows == 0) {
           echo json_encode([
               'success' => true, 
               'journeys' => [], 
               'settings' => ['welcome_message' => 'Journey system is not configured yet.']
           ]);
           return;
       }
       
       $settingsQuery = $spexcon->query("SELECT * FROM journey_settings WHERE id = 1");
       $settings = $settingsQuery ? $settingsQuery->fetch_assoc() : null;
       
       if(!$settings) {
           // Create default settings
           $spexcon->query("INSERT INTO journey_settings (is_enabled, welcome_message, completion_message) VALUES (1, 'Welcome to your trading journey! Complete tasks to earn rewards.', 'Congratulations! You have completed all available journeys.')");
           $settingsQuery = $spexcon->query("SELECT * FROM journey_settings WHERE id = 1");
           $settings = $settingsQuery ? $settingsQuery->fetch_assoc() : ['is_enabled' => 1, 'welcome_message' => 'Welcome to your journey!'];
       }
       
       if($settings['is_enabled'] != 1) {
           echo json_encode([
               'success' => true, 
               'journeys' => [], 
               'settings' => ['welcome_message' => 'Journey system is currently disabled.']
           ]);
           return;
       }
       
       // Check if journeys table exists
       $journeysTableExists = $spexcon->query("SHOW TABLES LIKE 'journeys'");
       if(!$journeysTableExists || $journeysTableExists->num_rows == 0) {
           echo json_encode([
               'success' => true, 
               'journeys' => [], 
               'settings' => $settings
           ]);
           return;
       }
       
       $query = $spexcon->query("
           SELECT 
               j.*,
               COALESCE(uj.progress, 0) as progress,
               COALESCE(uj.current_value, 0) as current_value,
               COALESCE(uj.target_value, 0) as target_value,
               COALESCE(uj.is_completed, 0) as is_completed,
               COALESCE(uj.is_claimed, 0) as is_claimed,
               uj.user_id as user_journey_id
           FROM journeys j
           LEFT JOIN user_journeys uj ON j.id = uj.journey_id AND uj.user_id = $UID
           WHERE j.is_active = 1
           ORDER BY j.journey_order ASC
       ");
       
       $journeys = [];
       if($query) {
           while($row = $query->fetch_assoc()) {
               if(!$row['user_journey_id']) {
                   initializeUserJourney($row['id']);
                   $row['progress'] = 0;
                   $row['current_value'] = 0;
                   $requirements = json_decode($row['requirements'], true);
                   $row['target_value'] = $requirements['target'] ?? 0;
                   $row['is_completed'] = 0;
                   $row['is_claimed'] = 0;
               }
               
               updateJourneyProgress($row['id']);
               
               // Get updated values
               $updatedQuery = $spexcon->query("
                   SELECT progress, current_value, target_value, is_completed, is_claimed 
                   FROM user_journeys 
                   WHERE user_id = $UID AND journey_id = " . $row['id']
               );
               
               if($updatedQuery) {
                   $updated = $updatedQuery->fetch_assoc();
                   if($updated) {
                       $row['progress'] = $updated['progress'];
                       $row['current_value'] = $updated['current_value'];
                       $row['target_value'] = $updated['target_value'];
                       $row['is_completed'] = $updated['is_completed'];
                       $row['is_claimed'] = $updated['is_claimed'];
                   }
               }
               
               $journeys[] = $row;
           }
       }
       
       echo json_encode([
           'success' => true,
           'journeys' => $journeys,
           'settings' => $settings
       ]);
       
   } catch (Exception $e) {
       error_log("getJourneys Error: " . $e->getMessage());
       echo json_encode(['success' => false, 'message' => 'Failed to load journeys']);
   }
}

function getJourneyStats() {
   global $spexcon, $UID;
   
   try {
       // Check if user_journeys table exists
       $userJourneysExists = $spexcon->query("SHOW TABLES LIKE 'user_journeys'");
       if(!$userJourneysExists || $userJourneysExists->num_rows == 0) {
           echo json_encode([
               'success' => true,
               'stats' => [
                   'completed' => 0,
                   'claimed' => 0,
                   'total_journeys' => 0,
                   'total_rewards' => 0
               ]
           ]);
           return;
       }
       
       $completedQuery = $spexcon->query("
           SELECT COUNT(*) as count 
           FROM user_journeys 
           WHERE user_id = $UID AND is_completed = 1
       ");
       $completed = $completedQuery ? $completedQuery->fetch_assoc()['count'] : 0;
       
       $claimedQuery = $spexcon->query("
           SELECT COUNT(*) as count 
           FROM user_journeys 
           WHERE user_id = $UID AND is_claimed = 1
       ");
       $claimed = $claimedQuery ? $claimedQuery->fetch_assoc()['count'] : 0;
       
       $totalQuery = $spexcon->query("SELECT COUNT(*) as count FROM journeys WHERE is_active = 1");
       $total = $totalQuery ? $totalQuery->fetch_assoc()['count'] : 0;
       
       $rewardsQuery = $spexcon->query("
           SELECT SUM(j.reward_amount) as total 
           FROM user_journeys uj 
           JOIN journeys j ON uj.journey_id = j.id 
           WHERE uj.user_id = $UID AND uj.is_claimed = 1
       ");
       $totalRewards = $rewardsQuery ? ($rewardsQuery->fetch_assoc()['total'] ?? 0) : 0;
       
       echo json_encode([
           'success' => true,
           'stats' => [
               'completed' => (int)$completed,
               'claimed' => (int)$claimed,
               'total_journeys' => (int)$total,
               'total_rewards' => (float)$totalRewards
           ]
       ]);
       
   } catch (Exception $e) {
       error_log("getJourneyStats Error: " . $e->getMessage());
       echo json_encode(['success' => false, 'message' => 'Failed to load stats']);
   }
}

function claimReward() {
   global $spexcon, $UID;
   
   try {
       $journeyId = (int)($_POST['journey_id'] ?? 0);
       
       if(!$journeyId) {
           throw new Exception("Journey ID is required");
       }
       
       // Check user balance blocked status
       $userQuery = $spexcon->query("SELECT balance, balance_blocked FROM users WHERE uid = $UID");
       if(!$userQuery) {
           throw new Exception("User not found");
       }
       
       $userProfile = $userQuery->fetch_assoc();
       if($userProfile['balance_blocked'] == 1) {
           throw new Exception("Your Balance is Locked by System");
       }
       
       $spexcon->autocommit(false);
       
       try {
           $journeyQuery = $spexcon->prepare("
               SELECT uj.*, j.reward_amount, j.reward_coin, j.reward_type, j.title 
               FROM user_journeys uj 
               JOIN journeys j ON uj.journey_id = j.id 
               WHERE uj.user_id = ? AND uj.journey_id = ? AND uj.is_completed = 1 AND uj.is_claimed = 0
           ");
           $journeyQuery->bind_param("ii", $UID, $journeyId);
           $journeyQuery->execute();
           $journey = $journeyQuery->get_result()->fetch_assoc();
           
           if(!$journey) {
               throw new Exception("Journey not found or already claimed");
           }
           
           $newBalance = (float)$userProfile['balance'];
           
           if($journey['reward_type'] == 'BALANCE') {
               $newBalance = (float)$userProfile['balance'] + (float)$journey['reward_amount'];
               
               $updateBalanceQuery = $spexcon->prepare("UPDATE users SET balance = ? WHERE uid = ?");
               $updateBalanceQuery->bind_param("di", $newBalance, $UID);
               
               if(!$updateBalanceQuery->execute()) {
                   throw new Exception("Failed to update balance");
               }
           }
           
           $updateJourneyQuery = $spexcon->prepare("
               UPDATE user_journeys 
               SET is_claimed = 1, claimed_at = CURRENT_TIMESTAMP 
               WHERE user_id = ? AND journey_id = ?
           ");
           $updateJourneyQuery->bind_param("ii", $UID, $journeyId);
           
           if(!$updateJourneyQuery->execute()) {
               throw new Exception("Failed to update journey status");
           }
           
           $logQuery = $spexcon->prepare("
               INSERT INTO user_logs (uid, type, msg, amount, cat, date) 
               VALUES (?, ?, ?, ?, 'rew', CURDATE())
           ");
           $type = 'Journey Reward';
           $msg = 'Journey completed: ' . $journey['title'];
           $amount = $journey['reward_amount'];
           
           $logQuery->bind_param("isss", $UID, $type, $msg, $amount);
           $logQuery->execute();
           
           $notificationMsg = "You earned {$journey['reward_amount']} {$journey['reward_coin']} from completing \"{$journey['title']}\"";
           PushSend($UID,'Journey Reward Claimed', $notificationMsg, 'Reward');
           
           $spexcon->commit();
           $spexcon->autocommit(true);
           
           echo json_encode([
               'success' => true,
               'message' => 'Reward claimed successfully',
               'reward_amount' => $journey['reward_amount'],
               'reward_coin' => $journey['reward_coin'],
               'new_balance' => $newBalance
           ]);
           
       } catch (Exception $e) {
           $spexcon->rollback();
           $spexcon->autocommit(true);
           throw $e;
       }
       
   } catch (Exception $e) {
       error_log("claimReward Error: " . $e->getMessage());
       echo json_encode(['success' => false, 'message' => $e->getMessage()]);
   }
}

function updateAllProgress() {
   global $spexcon, $UID;
   
   try {
       $journeysTableExists = $spexcon->query("SHOW TABLES LIKE 'journeys'");
       if(!$journeysTableExists || $journeysTableExists->num_rows == 0) {
           echo json_encode([
               'success' => true,
               'message' => "No journeys available"
           ]);
           return;
       }
       
       $journeysQuery = $spexcon->query("
           SELECT id FROM journeys 
           WHERE is_active = 1 
           ORDER BY journey_order ASC
       ");
       
       $updated = 0;
       if($journeysQuery) {
           while($row = $journeysQuery->fetch_assoc()) {
               if(updateJourneyProgress($row['id'])) {
                   $updated++;
               }
           }
       }
       
       echo json_encode([
           'success' => true,
           'message' => "Updated $updated journey progresses"
       ]);
       
   } catch (Exception $e) {
       error_log("updateAllProgress Error: " . $e->getMessage());
       echo json_encode(['success' => false, 'message' => 'Failed to update progress']);
   }
}

function initializeUserJourney($journeyId) {
   global $spexcon, $UID;
   
   try {
       // Check if user_journeys table exists
       $userJourneysExists = $spexcon->query("SHOW TABLES LIKE 'user_journeys'");
       if(!$userJourneysExists || $userJourneysExists->num_rows == 0) {
           return false;
       }
       
       $journeyQuery = $spexcon->prepare("SELECT requirements FROM journeys WHERE id = ?");
       $journeyQuery->bind_param("i", $journeyId);
       $journeyQuery->execute();
       $journey = $journeyQuery->get_result()->fetch_assoc();
       
       if(!$journey) return false;
       
       $requirements = json_decode($journey['requirements'], true);
       $targetValue = $requirements['target'] ?? 0;
       
       $insertQuery = $spexcon->prepare("
           INSERT IGNORE INTO user_journeys 
           (user_id, journey_id, progress, current_value, target_value) 
           VALUES (?, ?, 0, 0, ?)
       ");
       $insertQuery->bind_param("iid", $UID, $journeyId, $targetValue);
       return $insertQuery->execute();
       
   } catch (Exception $e) {
       error_log("initializeUserJourney Error: " . $e->getMessage());
       return false;
   }
}

function updateJourneyProgress($journeyId) {
    global $spexcon, $UID;
    
    try {
        $journeyQuery = $spexcon->prepare("
            SELECT j.requirements, COALESCE(uj.is_completed, 0) as is_completed 
            FROM journeys j 
            LEFT JOIN user_journeys uj ON j.id = uj.journey_id AND uj.user_id = ? 
            WHERE j.id = ?
        ");
        $journeyQuery->bind_param("ii", $UID, $journeyId);
        $journeyQuery->execute();
        $journey = $journeyQuery->get_result()->fetch_assoc();
        
        if(!$journey || $journey['is_completed'] == 1) {
            return false;
        }
        
        $requirements = json_decode($journey['requirements'], true);
        if(!$requirements || !isset($requirements['type'])) return false;
        
        $currentValue = 0;
        $targetValue = $requirements['target'] ?? 0;
        
        switch($requirements['type']) {
            case 'trade_count':
                $currentValue = getTotalTrades();
                break;
            case 'balance_amount':
                $currentValue = getCurrentBalance();
                break;
            case 'trading_volume':
                $currentValue = getTotalTradingVolume();
                break;
            case 'kyc_verified':
                $currentValue = isKYCVerified() ? 1 : 0;
                break;
            case 'staking_count':
                $currentValue = getActiveStakingCount();
                break;
            case 'bot_subscription':
                $currentValue = getActiveBotSubscriptions();
                break;
            case 'referral_count':
                $currentValue = getReferralCount();
                break;
            default:
                return false;
        }
        
        $progress = $targetValue > 0 ? min(100, ($currentValue / $targetValue) * 100) : 0;
        $isCompleted = $currentValue >= $targetValue ? 1 : 0;
        
        // Check if user journey exists
        $checkQuery = $spexcon->prepare("
            SELECT id FROM user_journeys 
            WHERE user_id = ? AND journey_id = ?
        ");
        $checkQuery->bind_param("ii", $UID, $journeyId);
        $checkQuery->execute();
        $exists = $checkQuery->get_result()->fetch_assoc();
        
        if(!$exists) {
            initializeUserJourney($journeyId);
        }
        
        $updateQuery = $spexcon->prepare("
            UPDATE user_journeys 
            SET current_value = ?, target_value = ?, progress = ?, is_completed = ?, 
                completed_at = CASE WHEN ? = 1 AND is_completed = 0 THEN CURRENT_TIMESTAMP ELSE completed_at END
            WHERE user_id = ? AND journey_id = ?
        ");
        $updateQuery->bind_param("dddiiii", $currentValue, $targetValue, $progress, $isCompleted, $isCompleted, $UID, $journeyId);
        
        return $updateQuery->execute();
        
    } catch (Exception $e) {
        error_log("updateJourneyProgress Error: " . $e->getMessage());
        return false;
    }
}

function getTotalTrades() {
   global $spexcon, $UID;
   
   try {
       $tradesTableExists = $spexcon->query("SHOW TABLES LIKE 'user_transactions'");
       if(!$tradesTableExists || $tradesTableExists->num_rows == 0) {
           return 0;
       }
       
       $query = $spexcon->query("
           SELECT COUNT(*) as count 
           FROM user_transactions 
           WHERE user_id = $UID AND transaction_type IN ('BUY', 'SELL','MARKET_BUY','LIMIT_BUY','LIMIT_SELL')
       ");
       
       return $query ? (int)($query->fetch_assoc()['count'] ?? 0) : 0;
       
   } catch (Exception $e) {
       error_log("getTotalTrades Error: " . $e->getMessage());
       return 0;
   }
}

function getCurrentBalance() {
   global $spexcon, $UID;
   
   try {
       $query = $spexcon->query("SELECT balance FROM users WHERE uid = $UID");
       return $query ? (float)($query->fetch_assoc()['balance'] ?? 0) : 0;
       
   } catch (Exception $e) {
       error_log("getCurrentBalance Error: " . $e->getMessage());
       return 0;
   }
}

function getTotalTradingVolume() {
   global $spexcon, $UID;
   
   try {
       $tradesTableExists = $spexcon->query("SHOW TABLES LIKE 'user_transactions'");
       if(!$tradesTableExists || $tradesTableExists->num_rows == 0) {
           return 0;
       }
       
       $query = $spexcon->query("
           SELECT SUM(COALESCE(total_value_usd)) as volume 
           FROM user_transactions 
           WHERE user_id = $UID AND transaction_type IN ('BUY', 'SELL','MARKET_BUY','MARKET_SELL')
       ");
       
       return $query ? (float)($query->fetch_assoc()['volume'] ?? 0) : 0;
       
   } catch (Exception $e) {
       error_log("getTotalTradingVolume Error: " . $e->getMessage());
       return 0;
   }
}

function isKYCVerified() {
   global $spexcon, $UID;
   
   try {
       $query = $spexcon->query("SELECT identification_verify FROM users WHERE uid = $UID");
       return $query ? ($query->fetch_assoc()['identification_verify'] == 1) : false;
       
   } catch (Exception $e) {
       error_log("isKYCVerified Error: " . $e->getMessage());
       return false;
   }
}

function getActiveStakingCount() {
   global $spexcon, $UID;
   
   try {
       $stakingTableExists = $spexcon->query("SHOW TABLES LIKE 'user_staking_positions'");
       if(!$stakingTableExists || $stakingTableExists->num_rows == 0) {
           return 0;
       }
       
       $query = $spexcon->query("
           SELECT COUNT(*) as count 
           FROM user_staking_positions 
           WHERE user_id = $UID AND status = 'ACTIVE'
       ");
       
       return $query ? (int)($query->fetch_assoc()['count'] ?? 0) : 0;
       
   } catch (Exception $e) {
       error_log("getActiveStakingCount Error: " . $e->getMessage());
       return 0;
   }
}

function getActiveBotSubscriptions() {
   global $spexcon, $UID;
   
   try {
       $botTableExists = $spexcon->query("SHOW TABLES LIKE 'user_bot_subscriptions'");
       if(!$botTableExists || $botTableExists->num_rows == 0) {
           return 0;
       }
       
       $query = $spexcon->query("
           SELECT COUNT(*) as count 
           FROM user_bot_subscriptions 
           WHERE user_id = $UID AND status = 'ACTIVE'
       ");
       
       return $query ? (int)($query->fetch_assoc()['count'] ?? 0) : 0;
       
   } catch (Exception $e) {
       error_log("getActiveBotSubscriptions Error: " . $e->getMessage());
       return 0;
   }
}

function getReferralCount() {
   global $spexcon, $UID;
   
   try {
       $userQuery = $spexcon->query("SELECT invitation_code FROM users WHERE uid = $UID");
       if(!$userQuery) return 0;
       
       $user = $userQuery->fetch_assoc();
       if(!$user || empty($user['invitation_code'])) return 0;
       
       $invitationTableExists = $spexcon->query("SHOW TABLES LIKE 'invitations'");
       if($invitationTableExists && $invitationTableExists->num_rows > 0) {
           $query = $spexcon->query("
               SELECT COUNT(*) as count 
               FROM invitations 
               WHERE invited_by = '$UID' AND status = 'ACCEPTED'
           ");
           
           if($query) {
               return (int)($query->fetch_assoc()['count'] ?? 0);
           }
       }
       
       return 0;
       
   } catch (Exception $e) {
       error_log("getReferralCount Error: " . $e->getMessage());
       return 0;
   }
}
?>