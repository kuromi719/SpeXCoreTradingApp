<?php
/**
 * SpeX-Trading App Database Info
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

if (!isset($_SESSION['license_info'])) {
    echo json_encode([
        'success' => false,
        'message' => 'License information not found'
    ]);
    exit;
}

$config = $_SESSION['install_config'];
$licenseInfo = $_SESSION['license_info'];

try {
    echo json_encode([
        'success' => true,
        'database_name' => $config['database'],
        'host' => $config['host'],
        'sql_source' => 'Licensed API',
        'license_key' => $licenseInfo['license_key'],
        'domain' => $licenseInfo['domain'],
        'estimated_time' => '2-5 minutes'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to get database info: ' . $e->getMessage()
    ]);
}
?>