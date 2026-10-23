<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../../system/spex_v4_func.php';

if (!getSession('Login')) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!isset($spexcon) || $spexcon->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    $checkQuery = $spexcon->prepare("SELECT status FROM user_identifications WHERE user_id = ?");
    if (!$checkQuery) {
        throw new Exception("Database prepare failed: " . $spexcon->error);
    }
    
    $checkQuery->bind_param("i", $UserUID);
    $checkQuery->execute();
    $existing = $checkQuery->get_result()->fetch_assoc();

    if ($existing && $existing['status'] === 'APPROVED') {
        echo json_encode(['success' => false, 'message' => 'Identity already verified']);
        exit;
    }

    if ($existing && $existing['status'] === 'PENDING') {
        echo json_encode(['success' => false, 'message' => 'Identity verification already submitted and pending review']);
        exit;
    }

    $requiredFields = ['firstName', 'lastName', 'identityNumber', 'birthDate'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => ucfirst($field) . ' is required']);
            exit;
        }
    }

    $birthDate = $_POST['birthDate'];
    if (!DateTime::createFromFormat('Y-m-d', $birthDate)) {
        echo json_encode(['success' => false, 'message' => 'Invalid birth date format']);
        exit;
    }

    $birthDateObj = new DateTime($birthDate);
    $now = new DateTime();
    $age = $now->diff($birthDateObj)->y;

    if ($age < 18) {
        echo json_encode(['success' => false, 'message' => 'You must be at least 18 years old']);
        exit;
    }

    if (!isset($_FILES['frontImage']) || !isset($_FILES['backImage'])) {
        echo json_encode(['success' => false, 'message' => 'Both front and back images are required']);
        exit;
    }

    $frontImage = $_FILES['frontImage'];
    $backImage = $_FILES['backImage'];

    if ($frontImage['error'] !== UPLOAD_ERR_OK || $backImage['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'File upload error']);
        exit;
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!in_array($frontImage['type'], $allowedTypes) || !in_array($backImage['type'], $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Only JPG, JPEG, and PNG files are allowed']);
        exit;
    }

    // Validate file sizes (5MB)
    $maxSize = 5 * 1024 * 1024;
    if ($frontImage['size'] > $maxSize || $backImage['size'] > $maxSize) {
        echo json_encode(['success' => false, 'message' => 'File size must be less than 5MB']);
        exit;
    }

    $spexcon->autocommit(false);
    
    $uploadDir = __DIR__ . '/../../uploads/identifications/';
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }
    
    $frontFileName = $UserUID . '_front_' . time() . '_' . uniqid() . '.' . pathinfo($frontImage['name'], PATHINFO_EXTENSION);
    $backFileName = $UserUID . '_back_' . time() . '_' . uniqid() . '.' . pathinfo($backImage['name'], PATHINFO_EXTENSION);
    
    $frontPath = $uploadDir . $frontFileName;
    $backPath = $uploadDir . $backFileName;
    
    if (!move_uploaded_file($frontImage['tmp_name'], $frontPath)) {
        throw new Exception('Failed to upload front image');
    }
    
    if (!move_uploaded_file($backImage['tmp_name'], $backPath)) {
        if (file_exists($frontPath)) unlink($frontPath);
        throw new Exception('Failed to upload back image');
    }
    
    if ($existing) {
        $updateQuery = $spexcon->prepare("
            UPDATE user_identifications 
            SET identity_number = ?, serial_number = ?, first_name = ?, last_name = ?, 
                birth_date = ?, front_image = ?, back_image = ?, status = 'PENDING',
                admin_notes = NULL, updated_at = NOW()
            WHERE user_id = ?
        ");
        if (!$updateQuery) {
            throw new Exception("Update prepare failed: " . $spexcon->error);
        }
        
        $serialNumber = $_POST['serialNumber'] ?? '';
        $updateQuery->bind_param("sssssssi", 
            $_POST['identityNumber'], 
            $serialNumber,
            $_POST['firstName'],
            $_POST['lastName'],
            $birthDate,
            $frontFileName,
            $backFileName,
            $UserUID
        );
        
        if (!$updateQuery->execute()) {
            throw new Exception("Update execution failed: " . $updateQuery->error);
        }
    } else {
        $insertQuery = $spexcon->prepare("
            INSERT INTO user_identifications 
            (user_id, identity_number, serial_number, first_name, last_name, 
             birth_date, front_image, back_image, status, submitted_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'PENDING', NOW())
        ");
        if (!$insertQuery) {
            throw new Exception("Insert prepare failed: " . $spexcon->error);
        }
        
        $serialNumber = $_POST['serialNumber'] ?? '';
        $insertQuery->bind_param("isssssss",
            $UserUID,
            $_POST['identityNumber'],
            $serialNumber,
            $_POST['firstName'],
            $_POST['lastName'],
            $birthDate,
            $frontFileName,
            $backFileName
        );
        
        if (!$insertQuery->execute()) {
            throw new Exception("Insert execution failed: " . $insertQuery->error);
        }
    }
    
    try {
        if (function_exists('newUserLog')) {
            newUserLog($UserUID, 'Identity Verification', 'Identity verification submitted for review', '', 'security');
        }
    } catch (Exception $e) {
        error_log("User log failed: " . $e->getMessage());
    }
    
    try {
        if (function_exists('PushSend')) {
            PushSend($UserUID, 'Identity Verification Submitted', 
                      'Your identity verification has been submitted and is under review', 
                      'verification');
        }
    } catch (Exception $e) {
        error_log("Push notification failed: " . $e->getMessage());
    }
    
    $spexcon->commit();
    $spexcon->autocommit(true);
    
    echo json_encode([
        'success' => true,
        'message' => 'Identity verification submitted successfully'
    ]);
    
} catch (Exception $e) {
    if (isset($spexcon)) {
        $spexcon->rollback();
        $spexcon->autocommit(true);
    }
    
    if (isset($frontPath) && file_exists($frontPath)) unlink($frontPath);
    if (isset($backPath) && file_exists($backPath)) unlink($backPath);
    
    error_log("Identity submission error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error processing verification: ' . $e->getMessage()
    ]);
}
?>