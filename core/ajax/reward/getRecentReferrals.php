<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';

header('Content-Type: application/json');

try {
    $UID = getSession('UID');
    
    if (!$UID) {
        throw new Exception('User not logged in');
    }
    
    global $spexcon;
    
    $query = $spexcon->prepare("
        SELECT COALESCE(u.mail, 'Unknown User') as email, i.date, i.ref_uid,
               CASE 
                   WHEN u.uid IS NULL THEN 'deleted'
                   WHEN u.last_login IS NOT NULL THEN 'active' 
                   ELSE 'pending' 
               END as status
        FROM invitations i 
        LEFT JOIN users u ON i.ref_uid = u.uid 
        WHERE i.user_uid = ? 
        ORDER BY i.date DESC 
        LIMIT 10
    ");
    $query->bind_param("i", $UID);
    $query->execute();
    $result = $query->get_result();
    
    $referrals = [];
    while ($row = $result->fetch_assoc()) {
        $referrals[] = [
            'email' => $row['email'] !== 'Unknown User' ? Censor($row['email'], 'email') : 'UID: ' . $row['ref_uid'],
            'date' => $row['date'],
            'status' => $row['status']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $referrals
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>