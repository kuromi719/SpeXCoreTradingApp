<?php
/**
 * SpeX-Trading App Database Connection Test
 * @package SpeX-Trading V3.5
 * @author SpeXD
 * @instagram spexd
*/

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $host = trim($_POST['host'] ?? 'localhost');
    $username = trim($_POST['username'] ?? 'root');
    $password = $_POST['password'] ?? '';
    $database = trim($_POST['database'] ?? 'db_lin');
    $port = intval($_POST['port'] ?? 3306);
    $backupDays = intval($_POST['backup_days'] ?? 12);

    if (empty($host) || empty($username) || empty($database)) {
        throw new Exception('Please fill in all required fields (Host, Username, Database Name)');
    }

    if ($port < 1 || $port > 65535) {
        throw new Exception('Invalid port number. Please enter a port between 1 and 65535');
    }

    if ($backupDays < 7 || $backupDays > 60) {
        throw new Exception('Backup days must be between 7 and 60');
    }

    $connection = new mysqli($host, $username, $password, null, $port);
    
    if ($connection->connect_error) {
        throw new Exception("Connection failed: " . $connection->connect_error);
    }
    
    $serverVersion = $connection->server_info;
    $mysqlVersion = floatval($serverVersion);
    
    if ($mysqlVersion < 5.6) {
        throw new Exception("MySQL version $serverVersion is not supported. Please use MySQL 5.6+ or MariaDB 10.0+");
    }
    
    $dbExists = $connection->select_db($database);
    
    if (!$dbExists) {
        $charset = $mysqlVersion >= 5.7 ? 'utf8mb4' : 'utf8';
        $collation = $mysqlVersion >= 5.7 ? 'utf8mb4_unicode_ci' : 'utf8_turkish_ci';
        
        $createDb = $connection->query("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET $charset COLLATE $collation");
        if (!$createDb) {
            throw new Exception("Cannot create database '$database': " . $connection->error);
        }
        
        $connection->select_db($database);
        $message = "Database connection successful! Database '$database' created with $charset character set.";
    } else {
        $connection->select_db($database);
        
        $charsetResult = $connection->query("SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = '$database'");
        $charsetInfo = $charsetResult ? $charsetResult->fetch_assoc() : null;
        
        $currentCharset = $charsetInfo ? $charsetInfo['DEFAULT_CHARACTER_SET_NAME'] : 'unknown';
        $currentCollation = $charsetInfo ? $charsetInfo['DEFAULT_COLLATION_NAME'] : 'unknown';
        
        $tableCheck = $connection->query("SHOW TABLES LIKE 'users'");
        if ($tableCheck && $tableCheck->num_rows > 0) {
            $message = "Database connection successful! Warning: Database '$database' already contains tables. Current charset: $currentCharset ($currentCollation)";
        } else {
            $message = "Database connection successful! Database '$database' is ready. Current charset: $currentCharset ($currentCollation)";
        }
    }
    
    $privileges = [];
    try {
        $privilegeResult = $connection->query("SHOW GRANTS FOR CURRENT_USER()");
        if ($privilegeResult) {
            while ($row = $privilegeResult->fetch_array()) {
                $privileges[] = $row[0];
            }
        }
    } catch (Exception $e) {
        // Ignore privilege check errors
    }
    
    $hasRequiredPrivileges = true; // Assume true if we can't check
    
    if (!empty($privileges)) {
        $hasRequiredPrivileges = false;
        foreach ($privileges as $privilege) {
            if (stripos($privilege, 'ALL PRIVILEGES') !== false || 
                (stripos($privilege, 'CREATE') !== false && 
                 stripos($privilege, 'ALTER') !== false && 
                 stripos($privilege, 'DROP') !== false && 
                 stripos($privilege, 'INSERT') !== false && 
                 stripos($privilege, 'SELECT') !== false && 
                 stripos($privilege, 'UPDATE') !== false && 
                 stripos($privilege, 'DELETE') !== false)) {
                $hasRequiredPrivileges = true;
                break;
            }
        }
    }
    
    if (!$hasRequiredPrivileges) {
        $message .= " Warning: User may have limited privileges.";
    }
    
    $currentCharsetConn = $connection->character_set_name();
    if ($mysqlVersion >= 5.7 && !in_array($currentCharsetConn, ['utf8mb4', 'utf8'])) {
        $connection->set_charset('utf8mb4');
    } elseif ($mysqlVersion < 5.7 && $currentCharsetConn !== 'utf8') {
        $connection->set_charset('utf8');
    }
    
    $_SESSION['install_config'] = [
        'host' => $host,
        'username' => $username,
        'password' => $password,
        'database' => $database,
        'port' => $port,
        'backup_days' => $backupDays,
        'mysql_version' => $serverVersion,
        'charset' => $connection->character_set_name(),
        'collation' => $currentCollation ?? 'auto-detected'
    ];
    
    $connection->close();
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'details' => [
            'mysql_version' => $serverVersion,
            'charset' => $currentCharset ?? $connection->character_set_name(),
            'collation' => $currentCollation ?? 'auto-detected',
            'privileges_checked' => !empty($privileges),
            'charset_compatible' => true
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (Error $e) {
    echo json_encode([
        'success' => false,
        'message' => 'PHP Error: ' . $e->getMessage()
    ]);
} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Fatal Error: ' . $e->getMessage()
    ]);
}
?>