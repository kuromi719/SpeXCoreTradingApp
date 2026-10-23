<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';
header('Content-Type: application/json');

if (!isset($_SESSION['adminlog']) || $_SESSION['adminlog'] !== 'appr') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? '';

switch($action) {
    case 'getSettings':
        $result = $spexcon->query("SELECT * FROM journey_settings WHERE id = 1");
        $settings = $result->fetch_assoc();
        if(!$settings) {
            $spexcon->query("INSERT INTO journey_settings (is_enabled, welcome_message, completion_message) VALUES (1, 'Welcome to your trading journey!', 'Congratulations!')");
            $settings = $spexcon->query("SELECT * FROM journey_settings WHERE id = 1")->fetch_assoc();
        }
        echo json_encode(['success' => true, 'settings' => $settings]);
        break;
        
    case 'updateSettings':
        $stmt = $spexcon->prepare("UPDATE journey_settings SET is_enabled=?, reset_period=?, welcome_message=?, completion_message=? WHERE id=1");
        $stmt->bind_param("isss", $_POST['is_enabled'], $_POST['reset_period'], $_POST['welcome_message'], $_POST['completion_message']);
        if($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Settings updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update settings']);
        }
        break;
}
?>