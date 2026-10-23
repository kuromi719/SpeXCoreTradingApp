<?php
/**
 * SpeX-Trading App System Requirements Checker
 * @package SpeX-Trading V3.5
 * @author SpeXD
 * @instagram spexd
*/

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkPHPVersion() {
    $currentVersion = PHP_VERSION;
    $requiredVersion = '7.4.0';
    $recommendedVersion = '8.0.0';
    
    if (version_compare($currentVersion, $requiredVersion, '>=')) {
        $status = version_compare($currentVersion, $recommendedVersion, '>=') ? 'passed' : 'warning';
        $description = $status === 'passed' ? 
            'PHP version is compatible and recommended' : 
            'PHP version is compatible but we recommend PHP 8.0+';
    } else {
        $status = 'failed';
        $description = 'PHP version is too old. Please upgrade to PHP 7.4 or newer';
    }
    
    return [
        'name' => 'PHP Version',
        'description' => $description,
        'status' => $status,
        'value' => "Current: $currentVersion | Required: $requiredVersion+"
    ];
}

function checkExtension($extension, $required = true) {
    $loaded = extension_loaded($extension);
    
    return [
        'name' => ucfirst($extension) . ' Extension',
        'description' => $loaded ? 
            ucfirst($extension) . ' extension is available' : 
            ucfirst($extension) . ' extension is missing and ' . ($required ? 'required' : 'recommended'),
        'status' => $loaded ? 'passed' : ($required ? 'failed' : 'warning'),
        'value' => $loaded ? 'Enabled' : 'Disabled'
    ];
}

function checkDirectoryPermissions($path, $name) {
    $fullPath = '../../' . $path;
    
    if (!file_exists($fullPath)) {
        if (!mkdir($fullPath, 0755, true)) {
            return [
                'name' => $name . ' Directory',
                'description' => 'Directory does not exist and cannot be created',
                'status' => 'failed',
                'value' => 'Not writable'
            ];
        }
    }
    
    $writable = is_writable($fullPath);
    
    return [
        'name' => $name . ' Directory',
        'description' => $writable ? 
            'Directory has proper write permissions' : 
            'Directory is not writable. Please set permissions to 755 or 777',
        'status' => $writable ? 'passed' : 'failed',
        'value' => $writable ? 'Writable' : 'Not writable'
    ];
}

function checkPHPSetting($setting, $name, $recommendedValue, $type = 'minimum') {
    $currentValue = ini_get($setting);
    $currentBytes = $setting === 'memory_limit' ? parseSize($currentValue) : $currentValue;
    $recommendedBytes = $setting === 'memory_limit' ? parseSize($recommendedValue) : $recommendedValue;
    
    if ($type === 'minimum') {
        $status = $currentBytes >= $recommendedBytes ? 'passed' : 'warning';
        $description = $status === 'passed' ? 
            "$name is sufficient" : 
            "$name is below recommended value";
    } elseif ($type === 'exact') {
        $status = $currentValue == $recommendedValue ? 'passed' : 'warning';
        $description = $status === 'passed' ? 
            "$name is properly configured" : 
            "$name should be set to $recommendedValue";
    } else {
        $status = 'passed';
        $description = "$name is configured";
    }
    
    return [
        'name' => $name,
        'description' => $description,
        'status' => $status,
        'value' => "Current: $currentValue | Recommended: $recommendedValue"
    ];
}

function parseSize($size) {
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
    $size = preg_replace('/[^0-9\.]/', '', $size);
    
    if ($unit) {
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    } else {
        return round($size);
    }
}

function checkDatabaseConnection() {
    if (!extension_loaded('mysqli')) {
        return [
            'name' => 'Database Connection Test',
            'description' => 'MySQLi extension is required for database connectivity',
            'status' => 'failed',
            'value' => 'MySQLi not available'
        ];
    }
    
    return [
        'name' => 'Database Connection Test',
        'description' => 'MySQLi extension is available for database connections',
        'status' => 'passed',
        'value' => 'MySQLi available'
    ];
}

function checkServerSoftware() {
    $server = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
    
    if (stripos($server, 'apache') !== false || stripos($server, 'nginx') !== false) {
        $status = 'passed';
        $description = 'Web server is compatible';
    } else {
        $status = 'warning';
        $description = 'Web server compatibility unknown';
    }
    
    return [
        'name' => 'Web Server',
        'description' => $description,
        'status' => $status,
        'value' => $server
    ];
}

function checkHTTPS() {
    $isHTTPS = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
               $_SERVER['SERVER_PORT'] == 443 ||
               (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    
    return [
        'name' => 'HTTPS Security',
        'description' => $isHTTPS ? 
            'Site is running on secure HTTPS connection' : 
            'Site is not using HTTPS. SSL certificate is recommended for production',
        'status' => $isHTTPS ? 'passed' : 'warning',
        'value' => $isHTTPS ? 'Enabled' : 'Disabled'
    ];
}

try {
    $requirements = [
        checkPHPVersion(),
        checkExtension('mysqli', true),
        checkExtension('curl', true),
        checkExtension('gd', true),
        checkExtension('openssl', true),
        checkExtension('json', true),
        checkExtension('mbstring', true),
        checkExtension('fileinfo', true),
        checkExtension('zip', false),
        checkDatabaseConnection(),
        checkPHPSetting('memory_limit', 'Memory Limit', '128M', 'minimum'),
        checkPHPSetting('max_execution_time', 'Max Execution Time', '300', 'minimum'),
        checkPHPSetting('upload_max_filesize', 'Upload Max Filesize', '10M', 'minimum'),
        checkPHPSetting('post_max_size', 'Post Max Size', '10M', 'minimum'),
        checkDirectoryPermissions('core/system', 'Core System'),
        checkDirectoryPermissions('core/system/Logs', 'Logs'),
        checkDirectoryPermissions('install', 'Installation'),
        checkServerSoftware(),
        checkHTTPS()
    ];
    
    $summary = [
        'passed' => 0,
        'warning' => 0,
        'failed' => 0
    ];
    
    foreach ($requirements as $req) {
        $summary[$req['status']]++;
    }
    
    echo json_encode([
        'success' => true,
        'requirements' => $requirements,
        'summary' => $summary,
        'can_proceed' => $summary['failed'] === 0,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to check system requirements: ' . $e->getMessage(),
        'requirements' => [],
        'summary' => ['passed' => 0, 'warning' => 0, 'failed' => 0],
        'can_proceed' => false
    ]);
}
?>