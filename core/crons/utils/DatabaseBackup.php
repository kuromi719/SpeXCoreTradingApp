<?php
/*
 * SpeX-DataBase-BACKUP CRON-JOB  
 * Time : Every 1d
 */
require_once __DIR__ . '/../../system/spex_v4_func.php';
global $spexcon;
global $DataBaseBakcupDay;
class DatabaseBackupManager {
    private $globalConnection;
    private $backupDay;
    private $baseBackupPath = __DIR__ . '/../../database-backups/';
    
    public function __construct($spexcon, $DataBaseBakcupDay) {
        $this->globalConnection = $spexcon;
        $this->backupDay = $DataBaseBakcupDay;
    }
    
    private function createBackupDirectory($date) {
        $path = $this->baseBackupPath . $date;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        return $path;
    }
    
    private function getAllTables() {
        $tables = [];
        $result = $this->globalConnection->query('SHOW TABLES');
        while ($row = $result->fetch_array()) {
            $tables[] = $row[0];
        }
        return $tables;
    }
    
    private function backupDatabase($connection, $dbName, $backupPath) {
        $tables = $this->getAllTables();
        $totalSize = 0;
        $sqlContent = "";
        
        $sqlContent .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $sqlContent .= "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n";
        $sqlContent .= "SET AUTOCOMMIT=0;\n";
        $sqlContent .= "START TRANSACTION;\n";
        $sqlContent .= "CREATE DATABASE IF NOT EXISTS `$dbName`;\n";
        $sqlContent .= "USE `$dbName`;\n\n";
        
        foreach ($tables as $table) {
            $result = $connection->query("SHOW CREATE TABLE `$table`");
            if ($result && $row = $result->fetch_row()) {
                $sqlContent .= "DROP TABLE IF EXISTS `$table`;\n";
                $sqlContent .= $row[1] . ";\n\n";
                
                $totalSize += strlen($sqlContent);
                if ($totalSize > 200000000) {
                    $this->writeBackupFile($backupPath, $dbName, $sqlContent, 'structure');
                    $sqlContent = "";
                    $totalSize = 0;
                }
            }
        }
        
        if (!empty($sqlContent)) {
            $this->writeBackupFile($backupPath, $dbName, $sqlContent, 'structure');
        }
        
        $sqlContent = "";
        $totalSize = 0;
        
        foreach ($tables as $table) {
            $result = $connection->query("SELECT * FROM `$table`");
            
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $keys = array_keys($row);
                    $values = array_map(function($value) use ($connection) {
                        if ($value === null) return 'NULL';
                        return "'" . $connection->real_escape_string($value) . "'";
                    }, $row);
                    
                    $sqlContent .= "INSERT INTO `$table` (`" . implode('`, `', $keys) . "`) VALUES (" . implode(", ", $values) . ");\n";
                    
                    $totalSize += strlen($sqlContent);
                    if ($totalSize > 300000000) {
                        $this->writeBackupFile($backupPath, $dbName, $sqlContent, 'data');
                        $sqlContent = "";
                        $totalSize = 0;
                    }
                }
            }
        }
        
        if (!empty($sqlContent)) {
            $sqlContent .= "COMMIT;\n";
            $sqlContent .= "SET FOREIGN_KEY_CHECKS=1;\n";
            $this->writeBackupFile($backupPath, $dbName, $sqlContent, 'data');
        }
    }
    
    private function writeBackupFile($backupPath, $dbName, $content, $type) {
        static $structureCounter = 1;
        static $dataCounter = 1;
        
        if ($type === 'structure') {
            $filename = sprintf('%s/%s_structure_%02d.sql', $backupPath, $dbName, $structureCounter);
            $structureCounter++;
        } else {
            $filename = sprintf('%s/%s_data_%02d.sql', $backupPath, $dbName, $dataCounter);
            $dataCounter++;
        }
        
        if (!file_exists(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }
        
        file_put_contents($filename, $content);
    }
    
    public function execute() {
        try {
            $date = date('d.m.Y');
            $backupPath = $this->createBackupDirectory($date);
            
            $this->backupDatabase($this->globalConnection, 'MainDataBase', $backupPath);
            
            return true;
        } catch (Exception $e) {
            error_log('Backup Error: ' . $e->getMessage());
            return false;
        }
    }
}

$backupManager = new DatabaseBackupManager($spexcon, $DataBaseBakcupDay);
$backupManager->execute();
?>