<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';

header('Content-Type: application/json');

global $spexcon;

try {
    $UID = getSession('UID');
    
    if (!$UID) {
        echo json_encode(['error' => 'User not logged in']);
        exit;
    }
    
    $stmt = $spexcon->prepare("SELECT * FROM user_logs WHERE uid = ? AND cat = 'rew' ORDER BY date DESC LIMIT 50");
    $stmt->bind_param("i", $UID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $RewOTP = [];
    while ($row = $result->fetch_assoc()) {
        $RewOTP[] = $row;
    }
    
    if (empty($RewOTP)) {
        $html = '<tr><td colspan="5" class="text-center py-4">No rewards found</td></tr>';
    } else {
        $html = '';
        foreach ($RewOTP as $reward) {
            $html .= '<tr>
            <td>' . htmlspecialchars($reward['type']) . '</td>
            <td>' . $reward['amount'] . '</td>
            <td>'. htmlspecialchars($reward['msg']) .'</td>
            <td>'. htmlspecialchars($reward['date']) .'</td>
            </tr>';
        }
    }
    
    echo json_encode(['html' => $html]);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>