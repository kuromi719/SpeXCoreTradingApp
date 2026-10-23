<?php
/**
 * SpeX-Trading App Installation Finalization
 * @package SpeX-Trading V3.5
 * @author SpeXD
 * @instagram spexd
*/

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $installedFile = __DIR__ . '/../../core/system/.installed';
    $installedDir = dirname($installedFile);
    
    if (!is_dir($installedDir)) {
        if (!mkdir($installedDir, 0755, true)) {
            throw new Exception('Could not create system directory');
        }
    }
    
    $installationInfo = [
        'version' => '3.5',
        'installed_at' => date('Y-m-d H:i:s'),
        'installer_version' => 'SpeX-Core Base V3',
        'php_version' => PHP_VERSION,
        'mysql_version' => $_SESSION['install_config']['mysql_version'] ?? 'Unknown',
        'installation_id' => uniqid('spex_', true),
        'license_info'   => $_SESSION['license_info'],
        'spex_prag_file' => 'active'
    ];
    
    $installContent = "<?php\n";
    $installContent .= "/**\n";
    $installContent .= " * SpeX Trading Platform Installation Marker\n";
    $installContent .= " * DO NOT DELETE THIS FILE\n";
    $installContent .= "*/\n\n";
    $installContent .= "return " . var_export($installationInfo, true) . ";\n";
    
    if (file_put_contents($installedFile, $installContent) === false) {
        throw new Exception('Could not create .installed file');
    }

    $lockFile = __DIR__ . '/../.lock';
    $lockContent = "<?php\n";
    $lockContent .= "/**\n";
    $lockContent .= " * SpeX Trading Platform Lock Marker\n";
    $lockContent .= " * DO NOT DELETE THIS FILE\n";
    $lockContent .= "*/\n\n";
    $lockContent .= "define('THREAD_LOCKED', 1);\n";
    
    if (file_put_contents($lockFile, $lockContent) === false) {
        throw new Exception('Could not create .lock file');
    }
    
    $stepsFile = __DIR__ . '/../step.xml';
    if (file_exists($stepsFile)) {
        $xml = simplexml_load_file($stepsFile);
        if ($xml !== false) {
            foreach ($xml->steps->step as $step) {
                if ((string)$step['name'] === 'FINALIZE') {
                    $step['value'] = '1';
                    break;
                }
            }
            
            $xml->progress->installation_completed = 'true';
            $xml->progress->completed_steps = (string)$xml->progress->total_steps;
            $xml->asXML($stepsFile);
        }
    }
    
    if (isset($_SESSION['install_config'])) {
        unset($_SESSION['install_config']);
    }
    
    if (isset($_SESSION['install_loading_shown'])) {
        unset($_SESSION['install_loading_shown']);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Installation finalized successfully',
        'installation_info' => $installationInfo
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Finalization failed: ' . $e->getMessage()
    ]);
}
?>