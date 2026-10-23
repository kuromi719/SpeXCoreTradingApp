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
            getPresaleStats();
            break;
        case 'getPresales':
            getPresales();
            break;
        case 'getPresale':
            getPresale();
            break;
        case 'getPresaleDetails':
            getPresaleDetails();
            break;
        case 'addPresale':
            addPresale();
            break;
        case 'updatePresale':
            updatePresale();
            break;
        case 'duplicatePresale':
            duplicatePresale();
            break;
        case 'deletePresale':
            deletePresale();
            break;
        case 'exportCSV':
            exportPresalesCSV();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getPresaleStats() {
    global $spexcon;
    
    // Total presales
    $totalQuery = $spexcon->query("SELECT COUNT(*) as count FROM presales");
    $totalPresales = $totalQuery ? $totalQuery->fetch_assoc()['count'] : 0;
    
    // Active presales
    $activeQuery = $spexcon->query("SELECT COUNT(*) as count FROM presales WHERE status = 'active'");
    $activePresales = $activeQuery ? $activeQuery->fetch_assoc()['count'] : 0;
    
    // Total raised
    $raisedQuery = $spexcon->query("SELECT SUM(total_cost) as total FROM presale_purchases WHERE status = 'completed'");
    $totalRaised = $raisedQuery ? ($raisedQuery->fetch_assoc()['total'] ?? 0) : 0;
    
    // Total investors
    $investorsQuery = $spexcon->query("SELECT COUNT(DISTINCT user_id) as count FROM presale_purchases WHERE status = 'completed'");
    $totalInvestors = $investorsQuery ? $investorsQuery->fetch_assoc()['count'] : 0;
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_presales' => (int)$totalPresales,
            'active_presales' => (int)$activePresales,
            'total_raised' => (float)$totalRaised,
            'total_investors' => (int)$totalInvestors
        ]
    ]);
}

function getPresales() {
    global $spexcon;
    
    $search = Sanitize($_POST['search'] ?? '');
    $status = Sanitize($_POST['status'] ?? '');
    $featured = Sanitize($_POST['featured'] ?? '');
    $blockchain = Sanitize($_POST['blockchain'] ?? '');
    
    $sql = "SELECT * FROM presales WHERE 1=1";
    $params = [];
    $types = "";
    
    if (!empty($search)) {
        $sql .= " AND (coin_name LIKE ? OR coin_symbol LIKE ? OR description LIKE ?)";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= "sss";
    }
    
    if (!empty($status)) {
        $sql .= " AND status = ?";
        $params[] = $status;
        $types .= "s";
    }
    
    if ($featured !== '') {
        $sql .= " AND featured = ?";
        $params[] = (int)$featured;
        $types .= "i";
    }
    
    if (!empty($blockchain)) {
        $sql .= " AND blockchain = ?";
        $params[] = $blockchain;
        $types .= "s";
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    if (!empty($params)) {
        $stmt = $spexcon->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $spexcon->query($sql);
    }
    
    $presales = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $presales[] = $row;
        }
    }
    
    echo json_encode(['success' => true, 'presales' => $presales]);
}

function getPresale() {
    global $spexcon;
    
    $presaleId = (int)$_POST['presale_id'];
    if (!$presaleId) {
        throw new Exception("Presale ID is required");
    }
    
    $stmt = $spexcon->prepare("SELECT * FROM presales WHERE id = ?");
    $stmt->bind_param("i", $presaleId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Presale not found");
    }
    
    $presale = $result->fetch_assoc();
    echo json_encode(['success' => true, 'presale' => $presale]);
}

function getPresaleDetails() {
    global $spexcon;
    
    $presaleId = (int)$_POST['presale_id'];
    if (!$presaleId) {
        throw new Exception("Presale ID is required");
    }
    
    // Get presale data
    $stmt = $spexcon->prepare("SELECT * FROM presales WHERE id = ?");
    $stmt->bind_param("i", $presaleId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Presale not found");
    }
    
    $presale = $result->fetch_assoc();
    
    // Get presale statistics
    $statsQuery = $spexcon->prepare("
        SELECT 
            COUNT(*) as total_purchases,
            COUNT(DISTINCT user_id) as total_investors,
            COALESCE(SUM(total_cost), 0) as total_raised,
            COALESCE(AVG(total_cost), 0) as avg_purchase
        FROM presale_purchases 
        WHERE presale_id = ? AND status = 'completed'
    ");
    $statsQuery->bind_param("i", $presaleId);
    $statsQuery->execute();
    $stats = $statsQuery->get_result()->fetch_assoc();
    
    echo json_encode([
        'success' => true, 
        'presale' => $presale,
        'stats' => $stats
    ]);
}

function addPresale() {
    global $spexcon;
    
    $coinName = Sanitize($_POST['coin_name']);
    $coinSymbol = Sanitize($_POST['coin_symbol']);
    $coinLogo = Sanitize($_POST['coin_logo'] ?? '');
    $description = Sanitize($_POST['description'] ?? '');
    $price = (float)$_POST['price'];
    $totalSupply = (int)$_POST['total_supply'];
    $minPurchase = (float)($_POST['min_purchase'] ?? 0.001);
    $maxPurchase = (float)($_POST['max_purchase'] ?? 1000);
    $volume24h = (float)($_POST['volume_24h'] ?? 0.1);
    $marketCap = (float)($_POST['market_cap'] ?? 0.1);
    $startDateRaw = $_POST['start_date'] ?? '';
    if (!empty($startDateRaw)) {
        $startDate = str_replace('T', ' ', $startDateRaw) . ':00'; // "2025-08-19 14:30:00"
    }
    $endDateRaw = $_POST['end_date'] ?? '';
    if (!empty($endDateRaw)) {
        $endDate   = str_replace('T', ' ', $endDateRaw) . ':00';   // "2025-08-21 16:00:00"
    }
    $status = Sanitize($_POST['status'] ?? 'upcoming');
    $featured = isset($_POST['featured']) ? 1 : 0;
    $blockchain = Sanitize($_POST['blockchain'] ?? 'Ethereum');
    $website = Sanitize($_POST['website'] ?? '');
    $whitepaper = Sanitize($_POST['whitepaper'] ?? '');
    $telegram = Sanitize($_POST['telegram'] ?? '');
    $twitter = Sanitize($_POST['twitter'] ?? '');
    $tradingEnabled = isset($_POST['trading_enabled']) ? 1 : 0;
    $claimEnabled = isset($_POST['claim_enabled']) ? 1 : 0;
    
    if (empty($coinName) || empty($coinSymbol) || $price <= 0 || $totalSupply <= 0) {
        throw new Exception("Please fill all required fields with valid values");
    }
    
    // Check if symbol already exists
    $checkStmt = $spexcon->prepare("SELECT id FROM presales WHERE coin_symbol = ?");
    $checkStmt->bind_param("s", $coinSymbol);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        throw new Exception("A presale with this symbol already exists");
    }
    
    $stmt = $spexcon->prepare("
        INSERT INTO presales (
            coin_name, coin_symbol, coin_logo, description, price, total_supply, 
            min_purchase, max_purchase, start_date, end_date, status, featured, 
            blockchain, website, whitepaper, telegram, twitter, trading_enabled, 
            claim_enabled, created_at, updated_at, volume_24h, market_cap
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?, ?)
    ");
    
    $stmt->bind_param(
        "ssssdiissssssssssiiss",
        $coinName, $coinSymbol, $coinLogo, $description, $price, $totalSupply,
        $minPurchase, $maxPurchase, $startDate, $endDate, $status, $featured,
        $blockchain, $website, $whitepaper, $telegram, $twitter, $tradingEnabled, $claimEnabled , $volume24h, $marketCap
    );
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Presale created successfully']);
    } else {
        throw new Exception("Failed to create presale: " . $spexcon->error);
    }
}

function updatePresale() {
    global $spexcon;
    
    $presaleId = (int)$_POST['presale_id'];
    if (!$presaleId) {
        throw new Exception("Presale ID is required");
    }
    
    $coinName = Sanitize($_POST['coin_name']);
    $coinSymbol = Sanitize($_POST['coin_symbol']);
    $coinLogo = Sanitize($_POST['coin_logo'] ?? '');
    $description = Sanitize($_POST['description'] ?? '');
    $price = (float)$_POST['price'];
    $totalSupply = (int)$_POST['total_supply'];
    $minPurchase = (float)($_POST['min_purchase'] ?? 0.001);
    $maxPurchase = (float)($_POST['max_purchase'] ?? 1000);
    $volume24h = (float)($_POST['volume_24h'] ?? 0.1);
    $marketCap = (float)($_POST['market_cap'] ?? 0.1);
    $startDateRaw = $_POST['start_date'] ?? '';
    if (!empty($startDateRaw)) {
        $startDate = str_replace('T', ' ', $startDateRaw) . ':00'; // "2025-08-19 14:30:00"
    }
    $endDateRaw = $_POST['end_date'] ?? '';
    if (!empty($endDateRaw)) {
        $endDate   = str_replace('T', ' ', $endDateRaw) . ':00';   // "2025-08-21 16:00:00"
    }
    $status = Sanitize($_POST['status'] ?? 'upcoming');
    $featured = isset($_POST['featured']) ? 1 : 0;
    $blockchain = Sanitize($_POST['blockchain'] ?? 'Ethereum');
    $website = Sanitize($_POST['website'] ?? '');
    $whitepaper = Sanitize($_POST['whitepaper'] ?? '');
    $telegram = Sanitize($_POST['telegram'] ?? '');
    $twitter = Sanitize($_POST['twitter'] ?? '');
    $tradingEnabled = isset($_POST['trading_enabled']) ? 1 : 0;
    $claimEnabled = isset($_POST['claim_enabled']) ? 1 : 0;
    
    if (empty($coinName) || empty($coinSymbol) || $price <= 0 || $totalSupply <= 0) {
        throw new Exception("Please fill all required fields with valid values");
    }
    
    // Check if symbol exists for other presales
    $checkStmt = $spexcon->prepare("SELECT id FROM presales WHERE coin_symbol = ? AND id != ?");
    $checkStmt->bind_param("si", $coinSymbol, $presaleId);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        throw new Exception("A presale with this symbol already exists");
    }
    
    $stmt = $spexcon->prepare("
        UPDATE presales SET 
            coin_name = ?, coin_symbol = ?, coin_logo = ?, description = ?, 
            price = ?, total_supply = ?, min_purchase = ?, max_purchase = ?, 
            start_date = ?, end_date = ?, status = ?, featured = ?, 
            blockchain = ?, website = ?, whitepaper = ?, telegram = ?, 
            twitter = ?, trading_enabled = ?, claim_enabled = ?, updated_at = NOW(), volume_24h = ?, market_cap = ?
        WHERE id = ?
    ");
    
    $stmt->bind_param(
        "ssssdiissssssssssiiiss",
        $coinName, $coinSymbol, $coinLogo, $description, $price, $totalSupply,
        $minPurchase, $maxPurchase, $startDate, $endDate, $status, $featured,
        $blockchain, $website, $whitepaper, $telegram, $twitter, $tradingEnabled, 
        $claimEnabled, $volume24h, $marketCap, $presaleId
    );
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Presale updated successfully']);
    } else {
        throw new Exception("Failed to update presale: " . $spexcon->error);
    }
}

function duplicatePresale() {
    echo json_encode(['success' => false, 'message' => 'Duplicate is not available.']);
}

function deletePresale() {
    global $spexcon;
    
    $presaleId = (int)$_POST['presale_id'];
    if (!$presaleId) {
        throw new Exception("Presale ID is required");
    }
    
    // Check if presale has purchases
    $checkStmt = $spexcon->prepare("SELECT COUNT(*) as count FROM presale_purchases WHERE presale_id = ?");
    $checkStmt->bind_param("i", $presaleId);
    $checkStmt->execute();
    $purchaseCount = $checkStmt->get_result()->fetch_assoc()['count'];
    
    if ($purchaseCount > 0) {
        throw new Exception("Cannot delete presale with existing purchases. Cancel the presale instead.");
    }
    
    $stmt = $spexcon->prepare("DELETE FROM presales WHERE id = ?");
    $stmt->bind_param("i", $presaleId);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Presale deleted successfully']);
        } else {
            throw new Exception("Presale not found");
        }
    } else {
        throw new Exception("Failed to delete presale: " . $spexcon->error);
    }
}

function exportPresalesCSV() {
    global $spexcon;
    
    $search = Sanitize($_GET['search'] ?? '');
    $status = Sanitize($_GET['status'] ?? '');
    $featured = Sanitize($_GET['featured'] ?? '');
    $blockchain = Sanitize($_GET['blockchain'] ?? '');
    
    $sql = "SELECT * FROM presales WHERE 1=1";
    $params = [];
    $types = "";
    
    if (!empty($search)) {
        $sql .= " AND (coin_name LIKE ? OR coin_symbol LIKE ? OR description LIKE ?)";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= "sss";
    }
    
    if (!empty($status)) {
        $sql .= " AND status = ?";
        $params[] = $status;
        $types .= "s";
    }
    
    if ($featured !== '') {
        $sql .= " AND featured = ?";
        $params[] = (int)$featured;
        $types .= "i";
    }
    
    if (!empty($blockchain)) {
        $sql .= " AND blockchain = ?";
        $params[] = $blockchain;
        $types .= "s";
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    if (!empty($params)) {
        $stmt = $spexcon->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $spexcon->query($sql);
    }
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="presales_export_' . date('Y-m-d_H-i-s') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    fputcsv($output, [
        'ID', 'Coin Name', 'Symbol', 'Price USD', 'Total Supply', 'Sold Amount', 
        'Progress %', 'Min Purchase', 'Max Purchase', 'Start Date', 'End Date', 
        'Status', 'Featured', 'Blockchain', 'Website', 'Created At'
    ]);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $progress = $row['total_supply'] > 0 ? ($row['sold_amount'] / $row['total_supply'] * 100) : 0;
            
            fputcsv($output, [
                $row['id'],
                $row['coin_name'],
                $row['coin_symbol'],
                $row['price'],
                $row['total_supply'],
                $row['sold_amount'],
                number_format($progress, 2),
                $row['min_purchase'],
                $row['max_purchase'],
                $row['start_date'],
                $row['end_date'],
                $row['status'],
                $row['featured'] ? 'Yes' : 'No',
                $row['blockchain'],
                $row['website'],
                $row['created_at']
            ]);
        }
    }
    
    fclose($output);
    exit;
}
?>