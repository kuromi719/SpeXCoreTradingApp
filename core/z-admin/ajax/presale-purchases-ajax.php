<?php
require __DIR__ . '/../../system/spex_v4_func.php';

header('Content-Type: application/json');

if (!isset($_SESSION['adminlog']) || $_SESSION['adminlog'] !== 'appr') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch($action) {
        case 'getStats':
            getPurchaseStats();
            break;
        case 'getPresaleOptions':
            getPresaleOptions();
            break;
        case 'getPurchases':
            getPurchases();
            break;
        case 'getPurchase':
            getPurchase();
            break;
        case 'getPurchaseDetails':
            getPurchaseDetails();
            break;
        case 'getUserHistory':
            getUserHistory();
            break;
        case 'updatePurchase':
            updatePurchase();
            break;
        case 'approvePurchase':
            approvePurchase();
            break;
        case 'cancelPurchase':
            cancelPurchase();
            break;
        case 'processClaim':
            processClaim();
            break;
        case 'bulkProcessClaims':
            bulkProcessClaims();
            break;
        case 'exportCSV':
            exportPurchasesCSV();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getPurchaseStats() {
    global $spexcon;
    
    // Total purchases
    $totalQuery = $spexcon->query("SELECT COUNT(*) as count FROM presale_purchases");
    $totalPurchases = $totalQuery ? $totalQuery->fetch_assoc()['count'] : 0;
    
    // Completed purchases
    $completedQuery = $spexcon->query("SELECT COUNT(*) as count FROM presale_purchases WHERE status = 'completed'");
    $completedPurchases = $completedQuery ? $completedQuery->fetch_assoc()['count'] : 0;
    
    // Total volume
    $volumeQuery = $spexcon->query("SELECT SUM(total_cost) as total FROM presale_purchases WHERE status = 'completed'");
    $totalVolume = $volumeQuery ? ($volumeQuery->fetch_assoc()['total'] ?? 0) : 0;
    
    // Claimed tokens
    $claimedQuery = $spexcon->query("SELECT SUM(amount) as total FROM presale_purchases WHERE claimed = 1");
    $claimedTokens = $claimedQuery ? ($claimedQuery->fetch_assoc()['total'] ?? 0) : 0;
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_purchases' => (int)$totalPurchases,
            'completed_purchases' => (int)$completedPurchases,
            'total_volume' => (float)$totalVolume,
            'claimed_tokens' => (float)$claimedTokens
        ]
    ]);
}

function getPresaleOptions() {
    global $spexcon;
    
    $query = $spexcon->query("SELECT id, coin_name, coin_symbol FROM presales ORDER BY coin_name ASC");
    $presales = [];
    
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            $presales[] = $row;
        }
    }
    
    echo json_encode(['success' => true, 'presales' => $presales]);
}

function getPurchases() {
    global $spexcon;
    
    $search = Sanitize($_POST['search'] ?? '');
    $status = Sanitize($_POST['status'] ?? '');
    $presaleId = Sanitize($_POST['presale_id'] ?? '');
    $claimed = Sanitize($_POST['claimed'] ?? '');
    $date = Sanitize($_POST['date'] ?? '');
    
    $sql = "
        SELECT 
            pp.*,
            p.coin_name,
            p.coin_symbol,
            p.coin_logo,
            p.blockchain,
            p.status as presale_status,
            u.mail as user_email
        FROM presale_purchases pp
        LEFT JOIN presales p ON pp.presale_id = p.id
        LEFT JOIN users u ON pp.user_id = u.uid
        WHERE 1=1
    ";
    
    $params = [];
    $types = "";
    
    if (!empty($search)) {
        $sql .= " AND (p.coin_name LIKE ? OR p.coin_symbol LIKE ? OR u.mail LIKE ? OR pp.user_id LIKE ?)";
        $searchParam = "%$search%";
        $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
        $types .= "ssss";
    }
    
    if (!empty($status)) {
        $sql .= " AND pp.status = ?";
        $params[] = $status;
        $types .= "s";
    }
    
    if (!empty($presaleId)) {
        $sql .= " AND pp.presale_id = ?";
        $params[] = (int)$presaleId;
        $types .= "i";
    }
    
    if ($claimed !== '') {
        $sql .= " AND pp.claimed = ?";
        $params[] = (int)$claimed;
        $types .= "i";
    }
    
    if (!empty($date)) {
        $sql .= " AND DATE(pp.created_at) = ?";
        $params[] = $date;
        $types .= "s";
    }
    
    $sql .= " ORDER BY pp.created_at DESC";
    
    if (!empty($params)) {
        $stmt = $spexcon->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $spexcon->query($sql);
    }
    
    $purchases = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $purchases[] = $row;
        }
    }
    
    echo json_encode(['success' => true, 'purchases' => $purchases]);
}

function getPurchase() {
    global $spexcon;
    
    $purchaseId = (int)$_POST['purchase_id'];
    if (!$purchaseId) {
        throw new Exception("Purchase ID is required");
    }
    
    $stmt = $spexcon->prepare("SELECT * FROM presale_purchases WHERE id = ?");
    $stmt->bind_param("i", $purchaseId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Purchase not found");
    }
    
    $purchase = $result->fetch_assoc();
    echo json_encode(['success' => true, 'purchase' => $purchase]);
}

function getPurchaseDetails() {
    global $spexcon;
    
    $purchaseId = (int)$_POST['purchase_id'];
    if (!$purchaseId) {
        throw new Exception("Purchase ID is required");
    }
    
    $stmt = $spexcon->prepare("
        SELECT 
            pp.*,
            p.coin_name,
            p.coin_symbol,
            p.coin_logo,
            p.blockchain,
            p.status as presale_status,
            p.description,
            p.website,
            p.telegram,
            p.twitter,
            u.mail as user_email
        FROM presale_purchases pp
        LEFT JOIN presales p ON pp.presale_id = p.id
        LEFT JOIN users u ON pp.user_id = u.uid
        WHERE pp.id = ?
    ");
    $stmt->bind_param("i", $purchaseId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Purchase not found");
    }
    
    $purchase = $result->fetch_assoc();
    echo json_encode(['success' => true, 'purchase' => $purchase]);
}

function getUserHistory() {
    global $spexcon;
    
    $userId = (int)$_POST['user_id'];
    if (!$userId) {
        throw new Exception("User ID is required");
    }
    
    // Get user info
    $userStmt = $spexcon->prepare("SELECT uid, mail, balance FROM users WHERE uid = ?");
    $userStmt->bind_param("i", $userId);
    $userStmt->execute();
    $user = $userStmt->get_result()->fetch_assoc();
    
    if (!$user) {
        throw new Exception("User not found");
    }
    
    // Get purchases
    $purchasesStmt = $spexcon->prepare("
        SELECT 
            pp.*,
            p.coin_name,
            p.coin_symbol
        FROM presale_purchases pp
        LEFT JOIN presales p ON pp.presale_id = p.id
        WHERE pp.user_id = ?
        ORDER BY pp.created_at DESC
    ");
    $purchasesStmt->bind_param("i", $userId);
    $purchasesStmt->execute();
    $result = $purchasesStmt->get_result();
    
    $purchases = [];
    while ($row = $result->fetch_assoc()) {
        $purchases[] = $row;
    }
    
    echo json_encode(['success' => true, 'user' => $user, 'purchases' => $purchases]);
}

function updatePurchase() {
    global $spexcon;
    
    $purchaseId = (int)$_POST['purchase_id'];
    $amount = (float)$_POST['amount'];
    $price = (float)$_POST['price'];
    $totalCost = (float)$_POST['total_cost'];
    $status = Sanitize($_POST['status']);
    $claimed = isset($_POST['claimed']) ? 1 : 0;
    
    if (!$purchaseId || $amount <= 0 || $price <= 0 || $totalCost <= 0) {
        throw new Exception("Please provide valid values");
    }
    
    $stmt = $spexcon->prepare("
        UPDATE presale_purchases 
        SET amount = ?, price = ?, total_cost = ?, status = ?, claimed = ?,
            claimed_at = CASE WHEN claimed = 1 AND ? = 1 AND claimed_at IS NULL THEN NOW() ELSE claimed_at END
        WHERE id = ?
    ");
    $stmt->bind_param("dddsiiii", $amount, $price, $totalCost, $status, $claimed, $claimed, $purchaseId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Purchase updated successfully']);
    } else {
        throw new Exception("Failed to update purchase: " . $spexcon->error);
    }
}

function approvePurchase() {
    global $spexcon;
    
    $purchaseId = (int)$_POST['purchase_id'];
    if (!$purchaseId) {
        throw new Exception("Purchase ID is required");
    }
    
    $stmt = $spexcon->prepare("UPDATE presale_purchases SET status = 'completed' WHERE id = ? AND status = 'pending'");
    $stmt->bind_param("i", $purchaseId);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Update presale sold amount
            $updateSoldStmt = $spexcon->prepare("
                UPDATE presales p 
                SET sold_amount = (
                    SELECT COALESCE(SUM(amount), 0) 
                    FROM presale_purchases pp 
                    WHERE pp.presale_id = p.id AND pp.status = 'completed'
                )
                WHERE p.id = (SELECT presale_id FROM presale_purchases WHERE id = ?)
            ");
            $updateSoldStmt->bind_param("i", $purchaseId);
            $updateSoldStmt->execute();
            
            echo json_encode(['success' => true, 'message' => 'Purchase approved successfully']);
        } else {
            throw new Exception("Purchase not found or already processed");
        }
    } else {
        throw new Exception("Failed to approve purchase: " . $spexcon->error);
    }
}

function cancelPurchase() {
    global $spexcon;
    
    $purchaseId = (int)$_POST['purchase_id'];
    if (!$purchaseId) {
        throw new Exception("Purchase ID is required");
    }
    
    $stmt = $spexcon->prepare("UPDATE presale_purchases SET status = 'cancelled' WHERE id = ? AND status = 'pending'");
    $stmt->bind_param("i", $purchaseId);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Purchase cancelled successfully']);
        } else {
            throw new Exception("Purchase not found or already processed");
        }
    } else {
        throw new Exception("Failed to cancel purchase: " . $spexcon->error);
    }
}

function processClaim() {
    global $spexcon;
    
    $purchaseId = (int)$_POST['purchase_id'];
    if (!$purchaseId) {
        throw new Exception("Purchase ID is required");
    }
    
    // Get purchase details
    $purchaseStmt = $spexcon->prepare("
        SELECT pp.*, p.coin_symbol 
        FROM presale_purchases pp 
        LEFT JOIN presales p ON pp.presale_id = p.id 
        WHERE pp.id = ? AND pp.status = 'completed' AND pp.claimed = 0
    ");
    $purchaseStmt->bind_param("i", $purchaseId);
    $purchaseStmt->execute();
    $purchase = $purchaseStmt->get_result()->fetch_assoc();
    
    if (!$purchase) {
        throw new Exception("Purchase not found or not eligible for claim");
    }
    
    // Update purchase as claimed
    $claimStmt = $spexcon->prepare("UPDATE presale_purchases SET claimed = 1, claimed_at = NOW() WHERE id = ?");
    $claimStmt->bind_param("i", $purchaseId);
    
    if ($claimStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Claim processed successfully']);
    } else {
        throw new Exception("Failed to process claim: " . $spexcon->error);
    }
}

function bulkProcessClaims() {
    global $spexcon;
    
    $purchaseIds = $_POST['purchase_ids'] ?? [];
    if (empty($purchaseIds)) {
        throw new Exception("No purchases selected");
    }
    
    $processed = 0;
    $spexcon->begin_transaction();
    
    try {
        foreach ($purchaseIds as $purchaseId) {
            $purchaseId = (int)$purchaseId;
            
            // Get purchase details
            $purchaseStmt = $spexcon->prepare("
                SELECT pp.*, p.coin_symbol 
                FROM presale_purchases pp 
                LEFT JOIN presales p ON pp.presale_id = p.id 
                WHERE pp.id = ? AND pp.status = 'completed' AND pp.claimed = 0
            ");
            $purchaseStmt->bind_param("i", $purchaseId);
            $purchaseStmt->execute();
            $purchase = $purchaseStmt->get_result()->fetch_assoc();
            
            if ($purchase) {
                // Update purchase as claimed
                $claimStmt = $spexcon->prepare("UPDATE presale_purchases SET claimed = 1, claimed_at = NOW() WHERE id = ?");
                $claimStmt->bind_param("i", $purchaseId);
                $claimStmt->execute();
                
                $processed++;
            }
        }
        
        $spexcon->commit();
        echo json_encode(['success' => true, 'processed' => $processed, 'message' => "$processed claims processed successfully"]);
        
    } catch (Exception $e) {
        $spexcon->rollback();
        throw new Exception("Failed to process bulk claims: " . $e->getMessage());
    }
}

function exportPurchasesCSV() {
    global $spexcon;
    
    $search = Sanitize($_GET['search'] ?? '');
    $status = Sanitize($_GET['status'] ?? '');
    $presaleId = Sanitize($_GET['presale_id'] ?? '');
    $claimed = Sanitize($_GET['claimed'] ?? '');
    $date = Sanitize($_GET['date'] ?? '');
    
    $sql = "
        SELECT 
            pp.*,
            p.coin_name,
            p.coin_symbol,
            u.mail as user_email
        FROM presale_purchases pp
        LEFT JOIN presales p ON pp.presale_id = p.id
        LEFT JOIN users u ON pp.user_id = u.uid
        WHERE 1=1
    ";
    
    $params = [];
    $types = "";
    
    if (!empty($search)) {
        $sql .= " AND (p.coin_name LIKE ? OR p.coin_symbol LIKE ? OR u.mail LIKE ?)";
        $searchParam = "%$search%";
        $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        $types .= "sss";
    }
    
    if (!empty($status)) {
        $sql .= " AND pp.status = ?";
        $params[] = $status;
        $types .= "s";
    }
    
    if (!empty($presaleId)) {
        $sql .= " AND pp.presale_id = ?";
        $params[] = (int)$presaleId;
        $types .= "i";
    }
    
    if ($claimed !== '') {
        $sql .= " AND pp.claimed = ?";
        $params[] = (int)$claimed;
        $types .= "i";
    }
    
    if (!empty($date)) {
        $sql .= " AND DATE(pp.created_at) = ?";
        $params[] = $date;
        $types .= "s";
    }
    
    $sql .= " ORDER BY pp.created_at DESC";
    
    if (!empty($params)) {
        $stmt = $spexcon->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $spexcon->query($sql);
    }
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="presale_purchases_export_' . date('Y-m-d_H-i-s') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    fputcsv($output, [
        'Purchase ID', 'User ID', 'Email', 'Presale', 'Coin Symbol', 
        'Amount', 'Price USD', 'Total Cost USD', 'Status', 'Claimed', 
        'Purchase Date', 'Claim Date'
    ]);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['id'],
                $row['user_id'],
                $row['user_email'] ?: 'Not Available',
                $row['coin_name'],
                $row['coin_symbol'],
                $row['amount'],
                $row['price'],
                $row['total_cost'],
                $row['status'],
                $row['claimed'] ? 'Yes' : 'No',
                $row['created_at'],
                $row['claimed_at'] ?: 'Not Claimed'
            ]);
        }
    }
    
    fclose($output);
    exit;
}
?>