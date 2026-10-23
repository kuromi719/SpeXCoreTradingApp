<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';
header('Content-Type: application/json');

if (!isset($_SESSION['adminlog']) || $_SESSION['adminlog'] !== 'appr') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? '';

switch($action) {
    case 'getJourneys':
        $result = $spexcon->query("SELECT * FROM journeys ORDER BY journey_order ASC");
        $journeys = [];
        while($row = $result->fetch_assoc()) $journeys[] = $row;
        echo json_encode(['success' => true, 'journeys' => $journeys]);
        break;
        
    case 'getJourney':
        $id = (int)$_POST['id'];
        $result = $spexcon->query("SELECT * FROM journeys WHERE id = $id");
        echo json_encode(['success' => true, 'journey' => $result->fetch_assoc()]);
        break;
        
    case 'createJourney':
        $stmt = $spexcon->prepare("INSERT INTO journeys (title, description, icon, reward_amount, requirements, journey_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdsii", $_POST['title'], $_POST['description'], $_POST['icon'], $_POST['reward_amount'], $_POST['requirements'], $_POST['journey_order'], $_POST['is_active']);
        if($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Journey created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create journey']);
        }
        break;
        
    case 'updateJourney':
        $id = (int)$_POST['id'];
        $stmt = $spexcon->prepare("UPDATE journeys SET title=?, description=?, icon=?, reward_amount=?, requirements=?, journey_order=?, is_active=? WHERE id=?");
        $stmt->bind_param("sssdsiii", $_POST['title'], $_POST['description'], $_POST['icon'], $_POST['reward_amount'], $_POST['requirements'], $_POST['journey_order'], $_POST['is_active'], $id);
        if($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Journey updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update journey']);
        }
        break;
        
    case 'deleteJourney':
        $id = (int)$_POST['id'];
        if($spexcon->query("DELETE FROM journeys WHERE id = $id")) {
            echo json_encode(['success' => true, 'message' => 'Journey deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete journey']);
        }
        break;
}
?>