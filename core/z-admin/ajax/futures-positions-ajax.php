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
        case 'getPositionStats18289':
            $stats = getPositionStats18289();
            $response['success'] = true;
            $response['stats'] = $stats;
            break;
            
        case 'getPositions18289':
            $status = isset($_POST['status']) ? Sanitize($_POST['status']) : '';
            $type = isset($_POST['type']) ? Sanitize($_POST['type']) : '';
            $positions = getPositions18289($status, $type);
            $response['success'] = true;
            $response['positions'] = $positions;
            break;
            
        case 'closePosition18289':
            $positionId = intval($_POST['position_id']);
            if (closePosition18289($positionId)) {
                $response['success'] = true;
                $response['message'] = 'Position closed successfully';
            } else {
                $response['message'] = 'Failed to close position';
            }
            break;
            
        case 'liquidatePosition18289':
            $positionId = intval($_POST['position_id']);
            if (liquidatePosition18289($positionId)) {
                $response['success'] = true;
                $response['message'] = 'Position liquidated successfully';
            } else {
                $response['message'] = 'Failed to liquidate position';
            }
            break;
            
        case 'deletePosition18289':
            $positionId = intval($_POST['position_id']);
            if (deletePosition18289($positionId)) {
                $response['success'] = true;
                $response['message'] = 'Position deleted successfully';
            } else {
                $response['message'] = 'Failed to delete position';
            }
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log("Futures Positions Ajax Error: " . $e->getMessage());
}

ob_clean();
echo json_encode($response);
exit;

function getPositionStats18289() {
    global $spexcon;
    
    $stats = array(
        'total_positions' => 0,
        'open_positions' => 0,
        'total_margin' => 0,
        'total_pnl' => 0
    );
    
    try {
        $query = "SELECT COUNT(*) as total_positions FROM user_futures_positions";
        $result = $spexcon->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total_positions'] = intval($row['total_positions']);
        }
        
        $query = "SELECT COUNT(*) as open_positions FROM user_futures_positions WHERE status = 'OPEN'";
        $result = $spexcon->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['open_positions'] = intval($row['open_positions']);
        }
        
        $query = "SELECT SUM(margin_amount) as total_margin FROM user_futures_positions WHERE status = 'OPEN'";
        $result = $spexcon->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total_margin'] = floatval($row['total_margin'] ?? 0);
        }
        
        $query = "SELECT SUM(unrealized_pnl) as total_pnl FROM user_futures_positions";
        $result = $spexcon->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total_pnl'] = floatval($row['total_pnl'] ?? 0);
        }
    } catch (Exception $e) {
        error_log("getPositionStats18289 Error: " . $e->getMessage());
    }
    
    return $stats;
}

function getPositions18289($status, $type) {
    global $spexcon;
    
    try {
        $whereConditions = array();
        
        if (!empty($status)) {
            $status = mysqli_real_escape_string($spexcon, $status);
            $whereConditions[] = "ufp.status = '$status'";
        }
        
        if (!empty($type)) {
            $type = mysqli_real_escape_string($spexcon, $type);
            $whereConditions[] = "ufp.position_type = '$type'";
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        $query = "SELECT ufp.*, 
                         u.mail as user_email,
                         c.name as coin_name,
                         c.symbol as coin_symbol,
                         ci.small as coin_image
                  FROM user_futures_positions ufp
                  LEFT JOIN users u ON ufp.user_id = u.uid
                  LEFT JOIN coins c ON ufp.coin_id = c.id
                  LEFT JOIN coin_images ci ON c.id = ci.coin_id
                  $whereClause
                  ORDER BY ufp.opened_at DESC";
        
        $result = $spexcon->query($query);
        $positions = array();
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $positions[] = $row;
            }
        }
        
        return $positions;
    } catch (Exception $e) {
        error_log("getPositions18289 Error: " . $e->getMessage());
        return array();
    }
}

function closePosition18289($positionId) {
    global $spexcon;
    
    try {
        $query = "UPDATE user_futures_positions 
                  SET status = 'CLOSED', closed_at = NOW() 
                  WHERE id = $positionId AND status = 'OPEN'";
        return $spexcon->query($query);
    } catch (Exception $e) {
        error_log("closePosition18289 Error: " . $e->getMessage());
        return false;
    }
}

function liquidatePosition18289($positionId) {
    global $spexcon;
    
    try {
        $query = "UPDATE user_futures_positions 
                  SET status = 'LIQUIDATED', closed_at = NOW() 
                  WHERE id = $positionId AND status = 'OPEN'";
        return $spexcon->query($query);
    } catch (Exception $e) {
        error_log("liquidatePosition18289 Error: " . $e->getMessage());
        return false;
    }
}

function deletePosition18289($positionId) {
    global $spexcon;
    
    try {
        $query = "DELETE FROM user_futures_positions WHERE id = $positionId";
        return $spexcon->query($query);
    } catch (Exception $e) {
        error_log("deletePosition18289 Error: " . $e->getMessage());
        return false;
    }
}
?>