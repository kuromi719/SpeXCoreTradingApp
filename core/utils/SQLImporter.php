<?php
/**
 * SpeX-Trading App SQL Importer
 * @package SpeX-Trading V3.5
 * @author SpeXD
 * @instagram spexd
*/

declare(strict_types=1);

class SQLImporter {
    private $connection;
    private $errors = [];
    private $executed = [];
    private $totalQueries = 0;
    private $executedCount = 0;
    
    public function __construct($host, $username, $password, $database = null) {
        $this->connection = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", 
                                   $username, $password, 
                                   [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }
    
    public function importSQL($sqlFile) {
        if (!file_exists($sqlFile)) {
            throw new Exception("SQL file not found: " . $sqlFile);
        }
        
        $sql = file_get_contents($sqlFile);
        if ($sql === false) {
            throw new Exception("Could not read SQL file: " . $sqlFile);
        }

        return $this->importSQLFromString($sql);
    }
    
    public function importSQLFromString($sqlContent) {
        $this->errors = [];
        $this->executed = [];
        $this->totalQueries = 0;
        $this->executedCount = 0;
        
        $this->dropAll();
        $this->processSQLContent($sqlContent);
        
        return [
            'success' => count($this->errors) === 0 && $this->executedCount > 0,
            'total_queries' => $this->totalQueries,
            'executed_queries' => $this->executedCount,
            'errors' => $this->errors,
            'executed' => $this->executed
        ];
    }
    
    private function dropAll(): void {
        try {
            $this->connection->exec("SET FOREIGN_KEY_CHECKS = 0");
            $this->connection->exec("SET sql_mode = ''");
            
            $tables = $this->connection->query("SHOW FULL TABLES")->fetchAll(PDO::FETCH_NUM);
            foreach ($tables as $row) {
                $this->connection->exec("DROP TABLE IF EXISTS `{$row[0]}`");
            }

            $procedures = $this->connection->query("SHOW PROCEDURE STATUS WHERE Db = DATABASE()")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($procedures as $row) {
                $this->connection->exec("DROP PROCEDURE IF EXISTS `{$row['Name']}`");
            }

            $functions = $this->connection->query("SHOW FUNCTION STATUS WHERE Db = DATABASE()")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($functions as $row) {
                $this->connection->exec("DROP FUNCTION IF EXISTS `{$row['Name']}`");
            }

            $triggers = $this->connection->query("SHOW TRIGGERS")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($triggers as $row) {
                $this->connection->exec("DROP TRIGGER IF EXISTS `{$row['Trigger']}`");
            }

            $this->connection->exec("SET FOREIGN_KEY_CHECKS = 1");
        } catch (Exception $e) {
            $this->errors[] = [
                'query' => 'DROP ALL',
                'error' => $e->getMessage(),
                'errno' => 0
            ];
        }
    }
    
    private function processSQLContent($sqlContent) {
        $lines = explode("\n", $sqlContent);

        $queries = [];
        $currentQuery = '';
        $delimiter = ';';
        $customDelimiter = false;

        foreach ($lines as $line) {
            $trimmedLine = trim($line);

            if ($trimmedLine === '' || str_starts_with($trimmedLine, '--') || str_starts_with($trimmedLine, '#')) {
                continue;
            }

            if (preg_match('/^DELIMITER\s+(.+)$/i', $trimmedLine, $matches)) {
                $delimiter = $matches[1];
                $customDelimiter = $delimiter !== ';';
                continue;
            }

            $currentQuery .= $line . "\n";

            if ($customDelimiter) {
                if (str_ends_with(trim($line), $delimiter)) {
                    $query = substr($currentQuery, 0, strrpos($currentQuery, $delimiter));
                    $this->executeQuery(trim($query));
                    $currentQuery = '';
                }
            } else {
                if (str_ends_with(trim($line), ';')) {
                    $query = substr($currentQuery, 0, strrpos($currentQuery, ';'));
                    $this->executeQuery(trim($query));
                    $currentQuery = '';
                }
            }
        }

        if (trim($currentQuery) !== '') {
            $this->executeQuery(trim($currentQuery));
        }
    }
    
    private function executeQuery($query) {
        $this->totalQueries++;
        
        try {
            $this->connection->exec($query);
            $this->executedCount++;
            
            if (count($this->executed) < 20) {
                $this->executed[] = 'Query #' . $this->totalQueries . ': ' . substr($query, 0, 80) . (strlen($query) > 80 ? '...' : '');
            }
        } catch (Exception $e) {
            if (count($this->errors) < 10) {
                $this->errors[] = [
                    'query' => 'Query #' . $this->totalQueries . ': ' . substr($query, 0, 80) . '...',
                    'error' => $e->getMessage(),
                    'errno' => $e->getCode()
                ];
            }
        }
    }
    
    public function createDatabase($databaseName) {
        $query = "CREATE DATABASE IF NOT EXISTS `$databaseName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        $this->connection->exec($query);
        $this->connection->exec("USE `$databaseName`");
        return true;
    }
    
    public function testConnection() {
        try {
            $this->connection->query("SELECT 1");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function getExecutedQueries() {
        return $this->executed;
    }
    
    public function getTotalQueries() {
        return $this->totalQueries;
    }
    
    public function getExecutedCount() {
        return $this->executedCount;
    }
    
    public function close() {
        $this->connection = null;
    }
    
    public function __destruct() {
        $this->close();
    }
}
?>