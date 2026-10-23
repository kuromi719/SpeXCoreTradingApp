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
            getLogStats();
            break;
        case 'getLogFiles':
            getLogFiles();
            break;
        case 'getLogs':
            getLogs2();
            break;
        case 'getLogFileContent':
            getLogFileContent();
            break;
        case 'clearAllLogs':
            clearAllLogs2();
            break;
        case 'deleteLogFile':
            deleteLogFile();
            break;
        case 'downloadAll':
            downloadAllLogs();
            break;
        case 'downloadFile':
            downloadLogFile();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getLogDir() {
    return __DIR__ . '/../../system/Logs/';
}

function formatBytes($size, $precision = 2) {
    if ($size == 0) return '0 B';
    
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    $base = log($size, 1024);
    
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $units[floor($base)];
}

function getLogStats() {
    $logDir = getLogDir();
    
    if (!is_dir($logDir)) {
        echo json_encode([
            'success' => true,
            'stats' => [
                'error_count' => 0,
                'warning_count' => 0,
                'info_count' => 0,
                'file_count' => 0
            ]
        ]);
        return;
    }
    
    $logFiles = glob($logDir . '*.log');
    $errorCount = 0;
    $warningCount = 0;
    $infoCount = 0;
    
    foreach ($logFiles as $file) {
        if (is_readable($file)) {
            $content = file_get_contents($file);
            $errorCount += substr_count($content, '| Error:');
            $warningCount += substr_count($content, '| Warning:');
            $infoCount += substr_count($content, '| Info:');
        }
    }
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'error_count' => $errorCount,
            'warning_count' => $warningCount,
            'info_count' => $infoCount,
            'file_count' => count($logFiles)
        ]
    ]);
}

function getLogFiles() {
    $logDir = getLogDir();
    
    if (!is_dir($logDir)) {
        echo json_encode(['success' => true, 'files' => []]);
        return;
    }
    
    $logFiles = glob($logDir . '*.log');
    $files = [];
    
    foreach ($logFiles as $file) {
        $filename = basename($file);
        $size = formatBytes(filesize($file));
        $modified = date('d.m.Y H:i:s', filemtime($file));
        
        $files[] = [
            'name' => $filename,
            'size' => $size,
            'modified' => $modified,
            'path' => $file
        ];
    }
    
    // Sort by modification time (newest first)
    usort($files, function($a, $b) {
        return filemtime($logDir . $b['name']) - filemtime($logDir . $a['name']);
    });
    
    echo json_encode(['success' => true, 'files' => $files]);
}

function getLogs2() {
    $logDir = getLogDir();
    
    if (!is_dir($logDir)) {
        echo json_encode(['success' => true, 'logs' => []]);
        return;
    }
    
    $logFiles = glob($logDir . '*.log');
    $allLogs = [];
    
    // Sort files by modification time (newest first)
    usort($logFiles, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });
    
    // Read recent logs (last 1000 entries)
    $totalEntries = 0;
    foreach ($logFiles as $file) {
        if ($totalEntries >= 1000) break;
        
        if (is_readable($file)) {
            $logs = parseLogFile($file);
            $allLogs = array_merge($allLogs, $logs);
            $totalEntries += count($logs);
        }
    }
    
    // Sort all logs by timestamp (newest first)
    usort($allLogs, function($a, $b) {
        return strtotime($b['timestamp']) - strtotime($a['timestamp']);
    });
    
    // Limit to 1000 most recent entries
    $allLogs = array_slice($allLogs, 0, 1000);
    
    echo json_encode(['success' => true, 'logs' => $allLogs]);
}

function getLogFileContent() {
    $filename = $_POST['filename'] ?? '';
    if (empty($filename)) {
        throw new Exception('Filename is required');
    }
    
    $logDir = getLogDir();
    $filepath = $logDir . basename($filename); // Sanitize filename
    
    if (!file_exists($filepath)) {
        throw new Exception('Log file not found');
    }
    
    $logs = parseLogFile($filepath);
    
    // Sort by timestamp (newest first)
    usort($logs, function($a, $b) {
        return strtotime($b['timestamp']) - strtotime($a['timestamp']);
    });
    
    echo json_encode(['success' => true, 'logs' => $logs]);
}

function parseLogFile($filepath) {
    $logs = [];
    
    if (!is_readable($filepath)) {
        return $logs;
    }
    
    $content = file_get_contents($filepath);
    $lines = explode("\n", $content);
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        
        // Parse log format: 2025-01-23 12:34:56 | Error: [2] Division by zero in /path/file.php on line 123
        if (preg_match('/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\s*\|\s*(Error|Warning|Info|Debug):\s*(.+?)(?:\sin\s(.+?)\son\sline\s(\d+))?$/', $line, $matches)) {
            $timestamp = $matches[1];
            $level = $matches[2];
            $message = $matches[3];
            $file = isset($matches[4]) ? $matches[4] : '';
            $lineNum = isset($matches[5]) ? $matches[5] : '';
            
            $logs[] = [
                'timestamp' => $timestamp,
                'date' => date('Y-m-d', strtotime($timestamp)),
                'level' => strtoupper($level),
                'message' => $message,
                'file' => $file,
                'line' => $lineNum,
                'trace' => '' // Could be extended to capture stack traces
            ];
        }
    }
    
    return $logs;
}

function clearAllLogs2() {
    $logDir = getLogDir();
    
    if (!is_dir($logDir)) {
        echo json_encode(['success' => true, 'message' => 'No log directory found']);
        return;
    }
    
    $logFiles = glob($logDir . '*.log');
    $deletedCount = 0;
    
    foreach ($logFiles as $file) {
        if (unlink($file)) {
            $deletedCount++;
        }
    }
    
    echo json_encode([
        'success' => true, 
        'message' => "Deleted $deletedCount log files"
    ]);
}

function deleteLogFile() {
    $filename = $_POST['filename'] ?? '';
    if (empty($filename)) {
        throw new Exception('Filename is required');
    }
    
    $logDir = getLogDir();
    $filepath = $logDir . basename($filename);
    
    if (!file_exists($filepath)) {
        throw new Exception('Log file not found');
    }
    
    if (unlink($filepath)) {
        echo json_encode(['success' => true, 'message' => 'Log file deleted successfully']);
    } else {
        throw new Exception('Failed to delete log file');
    }
}

function downloadAllLogs() {
    $logDir = getLogDir();
    
    if (!is_dir($logDir)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'No log directory found']);
        exit;
    }
    
    $logFiles = glob($logDir . '*.log');
    
    if (empty($logFiles)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'No log files found']);
        exit;
    }
    
    // Create ZIP file
    $zipFile = sys_get_temp_dir() . '/system_logs_' . date('Y-m-d_H-i-s') . '.zip';
    $zip = new ZipArchive();
    
    if ($zip->open($zipFile, ZipArchive::CREATE) !== TRUE) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Cannot create ZIP file']);
        exit;
    }
    
    foreach ($logFiles as $file) {
        $zip->addFile($file, basename($file));
    }
    
    $zip->close();
    
    // Set headers for download
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="system_logs_' . date('Y-m-d_H-i-s') . '.zip"');
    header('Content-Length: ' . filesize($zipFile));
    
    // Output file and clean up
    readfile($zipFile);
    unlink($zipFile);
    exit;
}

function downloadLogFile() {
    $filename = $_GET['filename'] ?? '';
    if (empty($filename)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Filename is required']);
        exit;
    }
    
    $logDir = getLogDir();
    $filepath = $logDir . basename($filename);
    
    if (!file_exists($filepath)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'File not found']);
        exit;
    }
    
    // Set headers for download
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Content-Length: ' . filesize($filepath));
    
    // Output file
    readfile($filepath);
    exit;
}

?>