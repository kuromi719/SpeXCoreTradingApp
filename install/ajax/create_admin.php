<?php
/**
 * SpeX-Trading App Admin Creation
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
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Username and password are required'
    ]);
    exit;
}

if (strlen($username) < 3) {
    echo json_encode([
        'success' => false,
        'message' => 'Username must be at least 3 characters long'
    ]);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode([
        'success' => false,
        'message' => 'Password must be at least 6 characters long'
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
    
    $existingAdmin = $connection->prepare("SELECT id FROM admin WHERE user = ?");
    $existingAdmin->bind_param("s", $username);
    $existingAdmin->execute();
    $result = $existingAdmin->get_result();
    
    if ($result->num_rows > 0) {
        throw new Exception('Username already exists');
    }
    
    $hashedPassword = md5($password);
    $allowedPages = '*';
    $isSuper = 1;
    
    $stmt = $connection->prepare("INSERT INTO admin (user, pass, allowed_pages, isSuper) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $username, $hashedPassword, $allowedPages, $isSuper);
    
    if ($stmt->execute()) {
        $adminId = $connection->insert_id;
        
        try {
            $stepsFile = __DIR__ . '/../step.xml';
            if (file_exists($stepsFile)) {
                $xml = simplexml_load_file($stepsFile);
                if ($xml !== false) {
                    foreach ($xml->steps->step as $step) {
                        if ((string)$step['name'] === 'ADMIN_CREATE') {
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
        
        echo json_encode([
            'success' => true,
            'message' => 'Administrator account created successfully',
            'admin_id' => $adminId,
            'username' => $username
        ]);
        
    } else {
        throw new Exception('Failed to create admin account: ' . $connection->error);
    }
    
    $connection->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>