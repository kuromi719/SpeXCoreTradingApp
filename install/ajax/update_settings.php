<?php
/**
 * SpeX-Trading App Settings Update
 * @package SpeX-Trading V3.5
 * @author SpeXD
 * @instagram spexd
*/

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['install_config'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No database configuration found'
    ]);
    exit;
}

$config = $_SESSION['install_config'];

$allowedFields = [
    'BaseName',
    'smtp_host',
    'smtp_user',
    'smtp_pass',
    'smtp_port',
    'tawk_to'
];

$updateData = [];
foreach ($allowedFields as $field) {
    $value = trim($_POST[$field] ?? '');
    if (!empty($value)) {
        $updateData[$field] = $value;
    }
}

if (empty($updateData)) {
    echo json_encode([
        'success' => true,
        'message' => 'No settings to update',
        'updated_count' => 0
    ]);
    exit;
}

try {
    $connection = new mysqli(
        $config['host'],
        $config['username'],
        $config['password'],
        $config['database']
    );
    
    if ($connection->connect_error) {
        throw new Exception('Database connection failed: ' . $connection->connect_error);
    }
    
    $connection->set_charset("utf8mb4");
    
    $settingsExist = $connection->query("SELECT id FROM settings WHERE id = 1");
    
    if ($settingsExist && $settingsExist->num_rows > 0) {
        $setParts = [];
        $params = [];
        $types = '';
        
        foreach ($updateData as $field => $value) {
            $setParts[] = "`$field` = ?";
            $params[] = $value;
            $types .= 's';
        }
        
        $sql = "UPDATE settings SET " . implode(', ', $setParts) . " WHERE id = 1";
        $stmt = $connection->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param($types, ...$params);
            $success = $stmt->execute();
            
            if (!$success) {
                throw new Exception('Failed to update settings: ' . $stmt->error);
            }
        } else {
            throw new Exception('Failed to prepare statement: ' . $connection->error);
        }
        
    } else {
        $fields = array_keys($updateData);
        $placeholders = array_fill(0, count($updateData), '?');
        $types = str_repeat('s', count($updateData));
        
        array_unshift($updateData, 1);
        array_unshift($fields, 'id');
        array_unshift($placeholders, '?');
        $types = 'i' . $types;
        
        $sql = "INSERT INTO settings (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $connection->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param($types, ...array_values($updateData));
            $success = $stmt->execute();
            
            if (!$success) {
                throw new Exception('Failed to insert settings: ' . $stmt->error);
            }
        } else {
            throw new Exception('Failed to prepare statement: ' . $connection->error);
        }
    }
    
    try {
        $stepsFile = __DIR__ . '/../step.xml';
        if (file_exists($stepsFile)) {
            $xml = simplexml_load_file($stepsFile);
            if ($xml !== false) {
                foreach ($xml->steps->step as $step) {
                    if ((string)$step['name'] === 'SETTINGS') {
                        $step['value'] = '1';
                        break;
                    }
                }
                $xml->asXML($stepsFile);
            }
        }
    } catch (Exception $e) {
        error_log('Could not update installation step: ' . $e->getMessage());
    }
    
    $connection->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'Settings updated successfully',
        'updated_count' => count($updateData) - 1,
        'updated_fields' => array_keys($updateData)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>