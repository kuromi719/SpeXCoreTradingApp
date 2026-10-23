<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';
header('Content-Type: application/json');

if (!isset($_SESSION['adminlog']) || $_SESSION['adminlog'] !== 'appr') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? '';

switch($action) {
    case 'getUserJourneys':
        $search = $_POST['search'] ?? '';
        $status = $_POST['status'] ?? '';
        
        $where = "WHERE 1=1";
        if($search) $where .= " AND (u.mail LIKE '%$search%' OR u.uid = '$search')";
        if($status == 'completed') $where .= " AND uj.is_completed = 1 AND uj.is_claimed = 0";
        if($status == 'claimed') $where .= " AND uj.is_claimed = 1";
        if($status == 'active') $where .= " AND uj.is_completed = 0";
        
        $result = $spexcon->query("
            SELECT uj.*, u.mail as user_email, j.title as journey_title, j.reward_amount 
            FROM user_journeys uj 
            JOIN users u ON uj.user_id = u.uid 
            JOIN journeys j ON uj.journey_id = j.id 
            $where 
            ORDER BY uj.created_at DESC 
            LIMIT 100
        ");
        
        $journeys = [];
        while($row = $result->fetch_assoc()) $journeys[] = $row;
        echo json_encode(['success' => true, 'journeys' => $journeys]);
        break;
        
    case 'getUserJourney':
        $userId = (int)$_POST['user_id'];
        $journeyId = (int)$_POST['journey_id'];
        $result = $spexcon->query("SELECT * FROM user_journeys WHERE user_id = $userId AND journey_id = $journeyId");
        echo json_encode(['success' => true, 'journey' => $result->fetch_assoc()]);
        break;
        
    case 'updateUserJourney':
        $userId = (int)$_POST['user_id'];
        $journeyId = (int)$_POST['journey_id'];
        $currentValue = (float)$_POST['current_value'];
        $isCompleted = (int)$_POST['is_completed'];
        $isClaimed = (int)$_POST['is_claimed'];
        
        $stmt = $spexcon->prepare("UPDATE user_journeys SET current_value=?, is_completed=?, is_claimed=? WHERE user_id=? AND journey_id=?");
        $stmt->bind_param("diiii", $currentValue, $isCompleted, $isClaimed, $userId, $journeyId);
        
        if($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'User journey updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update user journey']);
        }
        break;
}
?>