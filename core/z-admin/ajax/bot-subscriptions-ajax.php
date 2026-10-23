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
        case 'getSubscriptionStats18289':
            $stats = getSubscriptionStats18289();
            $response['success'] = true;
            $response['stats'] = $stats;
            break;
            
        case 'getSubscriptions18289':
            $status = isset($_POST['status']) ? Sanitize($_POST['status']) : '';
            $subscriptions = getSubscriptions18289($status);
            $response['success'] = true;
            $response['subscriptions'] = $subscriptions;
            break;
            
        case 'getSubscriptionEarnings18289':
            $subscriptionId = intval($_POST['subscription_id']);
            $earningsData = getSubscriptionEarnings18289($subscriptionId);
            $response['success'] = true;
            $response['earnings'] = $earningsData['earnings'];
            $response['stats'] = $earningsData['stats'];
            break;
            
        case 'cancelSubscription18289':
            $subscriptionId = intval($_POST['subscription_id']);
            if (cancelSubscription18289($subscriptionId)) {
                $response['success'] = true;
                $response['message'] = 'Subscription cancelled successfully';
            } else {
                $response['message'] = 'Failed to cancel subscription';
            }
            break;
            
        case 'deleteSubscription18289':
            $subscriptionId = intval($_POST['subscription_id']);
            if (deleteSubscription18289($subscriptionId)) {
                $response['success'] = true;
                $response['message'] = 'Subscription deleted successfully';
            } else {
                $response['message'] = 'Failed to delete subscription';
            }
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log("Bot Subscriptions Ajax Error: " . $e->getMessage());
}

ob_clean();
echo json_encode($response);
exit;

function getSubscriptionStats18289() {
    global $spexcon;
    
    $stats = array(
        'total_subscriptions' => 0,
        'active_subscriptions' => 0,
        'total_revenue' => 0,
        'total_earnings' => 0
    );
    
    try {
        $query = "SELECT COUNT(*) as total_subscriptions FROM user_bot_subscriptions";
        $result = $spexcon->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total_subscriptions'] = intval($row['total_subscriptions']);
        }
        
        $query = "SELECT COUNT(*) as active_subscriptions FROM user_bot_subscriptions WHERE status = 'ACTIVE'";
        $result = $spexcon->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['active_subscriptions'] = intval($row['active_subscriptions']);
        }
        
        $query = "SELECT SUM(payment_amount) as total_revenue FROM user_bot_subscriptions";
        $result = $spexcon->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total_revenue'] = floatval($row['total_revenue'] ?? 0);
        }
        
        $query = "SELECT SUM(earning_amount) as total_earnings FROM user_bot_earnings";
        $result = $spexcon->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total_earnings'] = floatval($row['total_earnings'] ?? 0);
        }
    } catch (Exception $e) {
        error_log("getSubscriptionStats18289 Error: " . $e->getMessage());
    }
    
    return $stats;
}

function getSubscriptions18289($status) {
    global $spexcon;
    
    try {
        $whereClause = '';
        if (!empty($status)) {
            $status = mysqli_real_escape_string($spexcon, $status);
            $whereClause = "WHERE ubs.status = '$status'";
        }
        
        $query = "SELECT ubs.*, 
                         u.mail as user_email,
                         tb.name as bot_name,
                         tb.logo as bot_logo,
                         tb.bot_type
                  FROM user_bot_subscriptions ubs
                  LEFT JOIN users u ON ubs.user_id = u.uid
                  LEFT JOIN trading_bots tb ON ubs.bot_id = tb.id
                  $whereClause
                  ORDER BY ubs.subscription_start DESC";
        
        $result = $spexcon->query($query);
        $subscriptions = array();
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $subscriptions[] = $row;
            }
        }
        
        return $subscriptions;
    } catch (Exception $e) {
        error_log("getSubscriptions18289 Error: " . $e->getMessage());
        return array();
    }
}

function getSubscriptionEarnings18289($subscriptionId) {
    global $spexcon;
    
    try {
        $query = "SELECT * FROM user_bot_earnings 
                  WHERE subscription_id = $subscriptionId 
                  ORDER BY earning_hour DESC";
        
        $result = $spexcon->query($query);
        $earnings = array();
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $earnings[] = $row;
            }
        }
        
        $statsQuery = "SELECT 
                       COUNT(*) as total_earnings_count,
                       SUM(earning_amount) as total_amount,
                       AVG(earning_amount) as average_earning,
                       MAX(earning_amount) as max_earning,
                       MIN(earning_amount) as min_earning
                       FROM user_bot_earnings 
                       WHERE subscription_id = $subscriptionId";
        
        $statsResult = $spexcon->query($statsQuery);
        $stats = array();
        if ($statsResult) {
            $stats = $statsResult->fetch_assoc();
        }
        
        return array(
            'earnings' => $earnings,
            'stats' => $stats
        );
    } catch (Exception $e) {
        error_log("getSubscriptionEarnings18289 Error: " . $e->getMessage());
        return array('earnings' => array(), 'stats' => array());
    }
}

function cancelSubscription18289($subscriptionId) {
    global $spexcon;
    
    try {
        $query = "UPDATE user_bot_subscriptions 
                  SET status = 'CANCELLED' 
                  WHERE id = $subscriptionId AND status = 'ACTIVE'";
        return $spexcon->query($query);
    } catch (Exception $e) {
        error_log("cancelSubscription18289 Error: " . $e->getMessage());
        return false;
    }
}

function deleteSubscription18289($subscriptionId) {
    global $spexcon;
    
    try {
        $spexcon->query("DELETE FROM user_bot_earnings WHERE subscription_id = $subscriptionId");
        $query = "DELETE FROM user_bot_subscriptions WHERE id = $subscriptionId";
        return $spexcon->query($query);
    } catch (Exception $e) {
        error_log("deleteSubscription18289 Error: " . $e->getMessage());
        return false;
    }
}
?>