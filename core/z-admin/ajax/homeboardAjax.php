<?php
require __DIR__ . '/../../system/spex_v4_func.php';

header('Content-Type: application/json');

if (!isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'No action specified']);
    exit;
}

$action = $_POST['action'];

try {
    switch ($action) {
        case 'getBoardImages':
            getBoardImages();
            break;
            
        case 'uploadBoardImage':
            uploadBoardImage();
            break;
            
        case 'deleteBoardImage':
            deleteBoardImage();
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

function getBoardImages() {
    global $spexcon;
    
    $stmt = $spexcon->prepare("SELECT id, img, date FROM pins ORDER BY date DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $images]);
}

function uploadBoardImage() {
    global $spexcon;
    
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'No image uploaded or upload error']);
        return;
    }
    
    $uploadFile = $_FILES['image'];
    
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!in_array($uploadFile['type'], $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG and GIF allowed']);
        return;
    }
    
    if ($uploadFile['size'] > 5 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 5MB']);
        return;
    }
    
    $uploadDir = __DIR__ . '/../../uploads/board/';
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
            return;
        }
    }

    $fileExtension = strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION));
    $fileName = 'board_' . time() . '_' . uniqid() . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;
    
    if (!move_uploaded_file($uploadFile['tmp_name'], $filePath)) {
        echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file']);
        return;
    }
    
    $dbPath = 'core/uploads/board/' . $fileName;
    $stmt = $spexcon->prepare("INSERT INTO pins (img, date) VALUES (?, NOW())");
    $stmt->bind_param("s", $dbPath);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Board image uploaded successfully']);
    } else {
        unlink($filePath);
        echo json_encode(['success' => false, 'message' => 'Failed to save image info to database']);
    }
}

function deleteBoardImage() {
    global $spexcon;
    
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid image ID']);
        return;
    }
    
    $id = intval($_POST['id']);
    
    $stmt = $spexcon->prepare("SELECT img FROM pins WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Image not found']);
        return;
    }
    
    $row = $result->fetch_assoc();
    $imagePath = $row['img'];
    
    $stmt = $spexcon->prepare("DELETE FROM pins WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $fullPath = __DIR__ . '/../../' . str_replace('core/', '', $imagePath);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
        
        echo json_encode(['success' => true, 'message' => 'Board image deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete image from database']);
    }
}
?>