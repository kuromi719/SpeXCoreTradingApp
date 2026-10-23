<?php
require __DIR__ . '/../../system/spex_v4_func.php';

header('Content-Type: application/json');

if (!isset($_SESSION['adminlog']) || $_SESSION['adminlog'] !== 'appr') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    switch($action) {
        case 'getAccounts':
            getAccounts();
            break;
        case 'getAccount':
            getAccount();
            break;
        case 'addAccount':
            addAccount();
            break;
        case 'updateAccount':
            updateAccount();
            break;
        case 'deleteAccount':
            deleteAccount();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getAccounts() {
    global $spexcon;
    
    $query = $spexcon->query("SELECT * FROM deposit_accounts ORDER BY id DESC");
    $accounts = [];
    
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            $accounts[] = $row;
        }
    }
    
    echo json_encode(['success' => true, 'accounts' => $accounts]);
}

function getAccount() {
    global $spexcon;
    
    $accountId = (int)$_POST['account_id'];
    if (!$accountId) {
        throw new Exception("Account ID is required");
    }
    
    $stmt = $spexcon->prepare("SELECT * FROM deposit_accounts WHERE id = ?");
    $stmt->bind_param("i", $accountId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Account not found");
    }
    
    $account = $result->fetch_assoc();
    echo json_encode(['success' => true, 'account' => $account]);
}

function addAccount() {
    global $spexcon;
    
    $name = Sanitize($_POST['name']);
    $address = Sanitize($_POST['address']);
    
    if (empty($name) || empty($address)) {
        throw new Exception("Name and address are required");
    }
    
    $allowedNames = ['BTC', 'ETH', 'USDT', 'TRC20', 'BEP20'];
    if (!in_array($name, $allowedNames)) {
        throw new Exception("Invalid name. Must be one of: " . implode(', ', $allowedNames));
    }
        
    $qrPath = null;
    
    // Handle QR upload
    if (isset($_FILES['qr_file']) && $_FILES['qr_file']['error'] === 0) {
        $uploadDir = __DIR__ . '/../../uploads/qr/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileName = time() . '_' . uniqid() . '.' . pathinfo($_FILES['qr_file']['name'], PATHINFO_EXTENSION);
        $uploadPath = $uploadDir . $fileName;
        $qrPath = 'core/uploads/qr/' . $fileName;
        
        if (!move_uploaded_file($_FILES['qr_file']['tmp_name'], $uploadPath)) {
            throw new Exception("Failed to upload QR image");
        }
    }
    
    $stmt = $spexcon->prepare("INSERT INTO deposit_accounts (name, address, qr, date) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $name, $address, $qrPath);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Account added successfully']);
    } else {
        throw new Exception("Failed to add account: " . $spexcon->error);
    }
}

function updateAccount() {
    global $spexcon;
    
    $accountId = (int)$_POST['account_id'];
    $name = Sanitize($_POST['name']);
    $address = Sanitize($_POST['address']);
    
    if (!$accountId || empty($name) || empty($address)) {
        throw new Exception("Account ID, name and address are required");
    }
    
    $allowedNames = ['BTC', 'ETH', 'USDT', 'TRC20', 'BEP20'];
    if (!in_array($name, $allowedNames)) {
        throw new Exception("Invalid name. Must be one of: " . implode(', ', $allowedNames));
    }
    
    
    // Get current QR
    $currentStmt = $spexcon->prepare("SELECT qr FROM deposit_accounts WHERE id = ?");
    $currentStmt->bind_param("i", $accountId);
    $currentStmt->execute();
    $current = $currentStmt->get_result()->fetch_assoc();
    $qrPath = $current['qr'];
    
    // Handle QR upload
    if (isset($_FILES['qr_file']) && $_FILES['qr_file']['error'] === 0) {
        // Delete old QR if exists
        if ($qrPath && file_exists(__DIR__ . '/../../../' . $qrPath)) {
            unlink(__DIR__ . '/../../../' . $qrPath);
        }
        
        $uploadDir = __DIR__ . '/../../uploads/qr/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileName = time() . '_' . uniqid() . '.' . pathinfo($_FILES['qr_file']['name'], PATHINFO_EXTENSION);
        $uploadPath = $uploadDir . $fileName;
        $qrPath = 'core/uploads/qr/' . $fileName;
        
        if (!move_uploaded_file($_FILES['qr_file']['tmp_name'], $uploadPath)) {
            throw new Exception("Failed to upload QR image");
        }
    }
    
    $stmt = $spexcon->prepare("UPDATE deposit_accounts SET name = ?, address = ?, qr = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $address, $qrPath, $accountId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Account updated successfully']);
    } else {
        throw new Exception("Failed to update account: " . $spexcon->error);
    }
}

function deleteAccount() {
    global $spexcon;
    
    $accountId = (int)$_POST['account_id'];
    if (!$accountId) {
        throw new Exception("Account ID is required");
    }
    
    // Get QR path before deleting
    $qrStmt = $spexcon->prepare("SELECT qr FROM deposit_accounts WHERE id = ?");
    $qrStmt->bind_param("i", $accountId);
    $qrStmt->execute();
    $result = $qrStmt->get_result()->fetch_assoc();
    
    $stmt = $spexcon->prepare("DELETE FROM deposit_accounts WHERE id = ?");
    $stmt->bind_param("i", $accountId);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Delete QR file if exists
            if ($result && $result['qr'] && file_exists(__DIR__ . '/../../../' . $result['qr'])) {
                unlink(__DIR__ . '/../../../' . $result['qr']);
            }
            echo json_encode(['success' => true, 'message' => 'Account deleted successfully']);
        } else {
            throw new Exception("Account not found");
        }
    } else {
        throw new Exception("Failed to delete account: " . $spexcon->error);
    }
}
?>