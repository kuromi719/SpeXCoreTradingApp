<?php
require __DIR__ . '/../../system/spex_v4_func.php';

header('Content-Type: text/html; charset=utf-8');
ob_start();

try {
    if (!isset($_POST['action'])) {
        throw new Exception('Action not specified');
    }
    
    $action = $_POST['action'];
    $response = array('success' => false);
    
    switch ($action) {
        case 'getStakingStats18289':
            $stats = getStakingStats18289();
            $response['success'] = true;
            $response['stats'] = $stats;
            break;
            
        case 'getStakings18289':
            $status = isset($_POST['status']) ? Sanitize($_POST['status']) : '';
            $stakings = getStakings18289($status);
            $response['success'] = true;
            $response['stakings'] = $stakings;
            break;
            
        case 'claimRewards18289':
            $stakingId = intval($_POST['staking_id']);
            $amount = floatval($_POST['amount']);
            if (claimRewards18289($stakingId, $amount)) {
                $response['success'] = true;
                $response['message'] = 'Rewards claimed successfully';
            } else {
                $response['message'] = 'Failed to claim rewards';
            }
            break;
            
        case 'unstakePosition18289':
            $stakingId = intval($_POST['staking_id']);
            if (unstakePosition18289($stakingId)) {
                $response['success'] = true;
                $response['message'] = 'Position unstaked successfully';
            } else {
                $response['message'] = 'Failed to unstake position';
            }
            break;
            
        case 'deleteStaking18289':
            $stakingId = intval($_POST['staking_id']);
            if (deleteStaking18289($stakingId)) {
                $response['success'] = true;
                $response['message'] = 'Staking deleted successfully';
            } else {
                $response['message'] = 'Failed to delete staking';
            }
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log("Staking Positions Ajax Error: " . $e->getMessage());
}

ob_clean();
echo json_encode($response);
exit;

function getStakingStats18289() {
    global $spexcon;
    
    $stats = array(
        'total_stakings' => 0,
        'active_stakings' => 0,
        'total_stake_value' => 0,
        'total_rewards' => 0
    );
    
    try {
        $query = "SELECT COUNT(*) as total_stakings FROM user_staking_positions";
        $result = $spexcon->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total_stakings'] = intval($row['total_stakings']);
        }
        
        $query = "SELECT COUNT(*) as active_stakings FROM user_staking_positions WHERE status = 'ACTIVE'";
        $result = $spexcon->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['active_stakings'] = intval($row['active_stakings']);
        }
        
        $query = "SELECT SUM(stake_value_usd) as total_stake_value FROM user_staking_positions WHERE status = 'ACTIVE'";
        $result = $spexcon->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total_stake_value'] = floatval($row['total_stake_value'] ?? 0);
        }
        
        $query = "SELECT SUM(rewards_earned) as total_rewards FROM user_staking_positions";
        $result = $spexcon->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total_rewards'] = floatval($row['total_rewards'] ?? 0);
        }
    } catch (Exception $e) {
        error_log("getStakingStats18289 Error: " . $e->getMessage());
    }
    
    return $stats;
}

function getStakings18289($status) {
    global $spexcon;
    
    try {
        $whereClause = '';
        if (!empty($status)) {
            $status = mysqli_real_escape_string($spexcon, $status);
            $whereClause = "WHERE usp.status = '$status'";
        }
        
        $query = "SELECT usp.*, 
                         u.mail as user_email,
                         c.name as coin_name,
                         c.symbol as coin_symbol,
                         ci.small as coin_image
                  FROM user_staking_positions usp
                  LEFT JOIN users u ON usp.user_id = u.uid
                  LEFT JOIN coins c ON usp.coin_id = c.id
                  LEFT JOIN coin_images ci ON c.id = ci.coin_id
                  $whereClause
                  ORDER BY usp.stake_start_date DESC";
        
        $result = $spexcon->query($query);
        $stakings = array();
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $stakings[] = $row;
            }
        }
        
        return $stakings;
    } catch (Exception $e) {
        error_log("getStakings18289 Error: " . $e->getMessage());
        return array();
    }
}

function claimRewards18289($stakingId, $amount) {
    global $spexcon;
    
    try {
        $query = "UPDATE user_staking_positions 
                  SET rewards_claimed = rewards_claimed + $amount,
                      last_reward_date = NOW()
                  WHERE id = $stakingId AND status = 'ACTIVE'";
        return $spexcon->query($query);
    } catch (Exception $e) {
        error_log("claimRewards18289 Error: " . $e->getMessage());
        return false;
    }
}

function unstakePosition18289($stakingId) {
    global $spexcon;
    
    try {
        $query = "UPDATE user_staking_positions 
                  SET status = 'COMPLETED',
                      stake_end_date = NOW(),
                      rewards_claimed = rewards_earned
                  WHERE id = $stakingId AND status = 'ACTIVE'";
        return $spexcon->query($query);
    } catch (Exception $e) {
        error_log("unstakePosition18289 Error: " . $e->getMessage());
        return false;
    }
}

function deleteStaking18289($stakingId) {
    global $spexcon;
    
    try {
        $query = "DELETE FROM user_staking_positions WHERE id = $stakingId";
        return $spexcon->query($query);
    } catch (Exception $e) {
        error_log("deleteStaking18289 Error: " . $e->getMessage());
        return false;
    }
}
?>