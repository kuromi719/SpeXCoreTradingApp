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
        case 'getStats':
            $stats = getBotStats18289();
            $response['success'] = true;
            $response['stats'] = $stats;
            break;
            
        case 'getBots':
            $bots = getBots18289();
            $response['success'] = true;
            $response['bots'] = $bots;
            break;
            
        case 'createBot':
            $botData = getBotDataFromPost18289();
            if (createBot18289($botData)) {
                $response['success'] = true;
                $response['message'] = 'Bot created successfully';
            } else {
                $response['message'] = 'Failed to create bot';
            }
            break;
            
        case 'updateBot':
            $botId = intval($_POST['bot_id']);
            $botData = getBotDataFromPost18289();
            if (updateBot18289($botId, $botData)) {
                $response['success'] = true;
                $response['message'] = 'Bot updated successfully';
            } else {
                $response['message'] = 'Failed to update bot';
            }
            break;
            
        case 'deleteBot':
            $botId = intval($_POST['bot_id']);
            if (deleteBot18289($botId)) {
                $response['success'] = true;
                $response['message'] = 'Bot deleted successfully';
            } else {
                $response['message'] = 'Failed to delete bot';
            }
            break;
            
        case 'toggleBotStatus':
            $botId = intval($_POST['bot_id']);
            $status = intval($_POST['status']);
            if (toggleBotStatus18289($botId, $status)) {
                $response['success'] = true;
                $response['message'] = 'Bot status updated successfully';
            } else {
                $response['message'] = 'Failed to update bot status';
            }
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log("Trading Bots Ajax Error: " . $e->getMessage());
}

ob_clean();
echo json_encode($response);
exit;

function getBotStats18289() {
    global $spexcon;
    
    $stats = array(
        'total_bots' => 0,
        'active_bots' => 0,
        'total_subscribers' => 0,
        'total_earnings' => 0
    );
    
    try {
        $query = "SELECT COUNT(*) as total_bots FROM trading_bots";
        $result = $spexcon->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total_bots'] = intval($row['total_bots']);
        }
        
        $query = "SELECT COUNT(*) as active_bots FROM trading_bots WHERE is_active = 1";
        $result = $spexcon->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['active_bots'] = intval($row['active_bots']);
        }
        
        $query = "SELECT COUNT(DISTINCT user_id) as total_subscribers FROM user_bot_subscriptions WHERE status = 'ACTIVE'";
        $result = $spexcon->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total_subscribers'] = intval($row['total_subscribers'] ?? 0);
        }
        
        $query = "SELECT SUM(earning_amount) as total_earnings FROM user_bot_earnings";
        $result = $spexcon->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total_earnings'] = floatval($row['total_earnings'] ?? 0);
        }
    } catch (Exception $e) {
        error_log("getBotStats18289 Error: " . $e->getMessage());
    }
    
    return $stats;
}

function getBots18289() {
    global $spexcon;
    
    try {
        $query = "SELECT tb.*, 
                         COALESCE(sub_count.count, 0) as subscriber_count,
                         COALESCE(earnings.total, 0) as total_earnings_calc
                  FROM trading_bots tb
                  LEFT JOIN (
                      SELECT bot_id, COUNT(*) as count 
                      FROM user_bot_subscriptions 
                      WHERE status = 'ACTIVE' 
                      GROUP BY bot_id
                  ) sub_count ON tb.id = sub_count.bot_id
                  LEFT JOIN (
                      SELECT bot_id, SUM(earning_amount) as total 
                      FROM user_bot_earnings 
                      GROUP BY bot_id
                  ) earnings ON tb.id = earnings.bot_id
                  ORDER BY tb.is_active DESC, tb.id DESC";
        
        $result = $spexcon->query($query);
        
        $bots = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $bots[] = $row;
            }
        }
        
        return $bots;
    } catch (Exception $e) {
        error_log("getBots18289 Error: " . $e->getMessage());
        return array();
    }
}

function getBotDataFromPost18289() {
    global $spexcon;
    
    return array(
        'name' => mysqli_real_escape_string($spexcon, $_POST['name'] ?? ''),
        'description' => mysqli_real_escape_string($spexcon, $_POST['description'] ?? ''),
        'logo' => mysqli_real_escape_string($spexcon, $_POST['logo'] ?? ''),
        'level' => mysqli_real_escape_string($spexcon, $_POST['level'] ?? 'BEGINNER'),
        'hourly_profit_percentage' => floatval($_POST['hourly_profit_percentage'] ?? 0),
        'success_rate' => floatval($_POST['success_rate'] ?? 0),
        'risk_level' => mysqli_real_escape_string($spexcon, $_POST['risk_level'] ?? 'LOW'),
        'bot_type' => mysqli_real_escape_string($spexcon, $_POST['bot_type'] ?? 'SPOT_GRID'),
        'rental_prices' => mysqli_real_escape_string($spexcon, $_POST['rental_prices'] ?? '{}'),
        'is_active' => intval($_POST['is_active'] ?? 1)
    );
}

function createBot18289($botData) {
    global $spexcon;
    
    try {
        $query = "INSERT INTO trading_bots (name, description, logo, level, hourly_profit_percentage, 
                  success_rate, risk_level, bot_type, rental_prices, is_active, created_at, updated_at) 
                  VALUES ('{$botData['name']}', '{$botData['description']}', '{$botData['logo']}', 
                  '{$botData['level']}', {$botData['hourly_profit_percentage']}, {$botData['success_rate']}, 
                  '{$botData['risk_level']}', '{$botData['bot_type']}', '{$botData['rental_prices']}', 
                  {$botData['is_active']}, NOW(), NOW())";
        
        return $spexcon->query($query);
    } catch (Exception $e) {
        error_log("createBot18289 Error: " . $e->getMessage());
        return false;
    }
}

function updateBot18289($botId, $botData) {
    global $spexcon;
    
    try {
        $query = "UPDATE trading_bots SET 
                  name = '{$botData['name']}', 
                  description = '{$botData['description']}', 
                  logo = '{$botData['logo']}', 
                  level = '{$botData['level']}', 
                  hourly_profit_percentage = {$botData['hourly_profit_percentage']}, 
                  success_rate = {$botData['success_rate']}, 
                  risk_level = '{$botData['risk_level']}', 
                  bot_type = '{$botData['bot_type']}', 
                  rental_prices = '{$botData['rental_prices']}', 
                  is_active = {$botData['is_active']}, 
                  updated_at = NOW() 
                  WHERE id = $botId";
        
        return $spexcon->query($query);
    } catch (Exception $e) {
        error_log("updateBot18289 Error: " . $e->getMessage());
        return false;
    }
}

function deleteBot18289($botId) {
    global $spexcon;
    
    try {
        $query = "DELETE FROM trading_bots WHERE id = $botId";
        return $spexcon->query($query);
    } catch (Exception $e) {
        error_log("deleteBot18289 Error: " . $e->getMessage());
        return false;
    }
}

function toggleBotStatus18289($botId, $status) {
    global $spexcon;
    
    try {
        $query = "UPDATE trading_bots SET is_active = $status, updated_at = NOW() WHERE id = $botId";
        return $spexcon->query($query);
    } catch (Exception $e) {
        error_log("toggleBotStatus18289 Error: " . $e->getMessage());
        return false;
    }
}
?>