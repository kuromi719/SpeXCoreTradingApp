<?php
/*
 * SpeX Universal CRUD Manager - Database Operations for All Tables
*/

class CRUDManager {
    
    private $connection;
    private $lastInsertId;
    private $affectedRows;
    
    public function __construct() {
        global $spexcon;
        $this->connection = $spexcon;
        
        if (!$this->connection) {
            throw new Exception("Database connection not available");
        }
        
        $this->lastInsertId = 0;
        $this->affectedRows = 0;
    }
    
    // ==================== BASIC CRUD OPERATIONS ====================
    
    /**
     * Insert data into table
     * @param string $table
     * @param array $data
     * @return bool
     */
    public function insert($table, $data) {
        if (empty($data)) {
            return false;
        }
        
        $columns = array_keys($data);
        $placeholders = str_repeat('?,', count($columns) - 1) . '?';
        $values = array_values($data);
        
        $sql = "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES ($placeholders)";
        
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        
        $types = $this->getParamTypes($values);
        $stmt->bind_param($types, ...$values);
        
        $result = $stmt->execute();
        $this->lastInsertId = $this->connection->insert_id;
        $this->affectedRows = $this->connection->affected_rows;
        
        return $result;
    }
    
    /**
     * Update data in table
     * @param string $table
     * @param array $data
     * @param array $where
     * @return bool
     */
    public function update($table, $data, $where) {
        if (empty($data) || empty($where)) {
            return false;
        }
        
        $setClause = [];
        $setValues = [];
        foreach ($data as $key => $value) {
            $setClause[] = "`$key` = ?";
            $setValues[] = $value;
        }
        
        $whereClause = [];
        $whereValues = [];
        foreach ($where as $key => $value) {
            $whereClause[] = "`$key` = ?";
            $whereValues[] = $value;
        }
        
        $sql = "UPDATE `$table` SET " . implode(', ', $setClause) . " WHERE " . implode(' AND ', $whereClause);
        
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        
        $allValues = array_merge($setValues, $whereValues);
        $types = $this->getParamTypes($allValues);
        $stmt->bind_param($types, ...$allValues);
        
        $result = $stmt->execute();
        $this->affectedRows = $this->connection->affected_rows;
        
        return $result;
    }
    
    /**
     * Delete data from table
     * @param string $table
     * @param array $where
     * @return bool
     */
    public function delete($table, $where) {
        if (empty($where)) {
            return false;
        }
        
        $whereClause = [];
        $whereValues = [];
        foreach ($where as $key => $value) {
            $whereClause[] = "`$key` = ?";
            $whereValues[] = $value;
        }
        
        $sql = "DELETE FROM `$table` WHERE " . implode(' AND ', $whereClause);
        
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        
        $types = $this->getParamTypes($whereValues);
        $stmt->bind_param($types, ...$whereValues);
        
        $result = $stmt->execute();
        $this->affectedRows = $this->connection->affected_rows;
        
        return $result;
    }
    
    /**
     * Select data from table
     * @param string $table
     * @param array $where
     * @param string $columns
     * @param string $orderBy
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function select($table, $where = [], $columns = '*', $orderBy = '', $limit = 0, $offset = 0) {
        $sql = "SELECT $columns FROM `$table`";
        $params = [];
        
        if (!empty($where)) {
            $whereClause = [];
            foreach ($where as $key => $value) {
                $whereClause[] = "`$key` = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        
        if ($limit > 0) {
            $sql .= " LIMIT $limit";
            if ($offset > 0) {
                $sql .= " OFFSET $offset";
            }
        }
        
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        
        if (!empty($params)) {
            $types = $this->getParamTypes($params);
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Find single record
     * @param string $table
     * @param array $where
     * @param string $columns
     * @return array|null
     */
    public function find($table, $where, $columns = '*') {
        $result = $this->select($table, $where, $columns, '', 1);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Check if record exists
     * @param string $table
     * @param array $where
     * @return bool
     */
    public function exists($table, $where) {
        $result = $this->select($table, $where, 'COUNT(*) as count', '', 1);
        return !empty($result) && $result[0]['count'] > 0;
    }
    
    /**
     * Count records
     * @param string $table
     * @param array $where
     * @return int
     */
    public function count($table, $where = []) {
        $result = $this->select($table, $where, 'COUNT(*) as count', '', 1);
        return !empty($result) ? (int)$result[0]['count'] : 0;
    }
    
    // ==================== ADVANCED OPERATIONS ====================
    
    /**
     * Execute custom query
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        
        if (!empty($params)) {
            $types = $this->getParamTypes($params);
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        $this->affectedRows = $this->connection->affected_rows;
        return [];
    }
    
    /**
     * Execute custom query and return single row
     * @param string $sql
     * @param array $params
     * @return array|null
     */
    public function queryOne($sql, $params = []) {
        $result = $this->query($sql, $params);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Insert or Update (UPSERT)
     * @param string $table
     * @param array $data
     * @param array $updateData
     * @return bool
     */
    public function insertOrUpdate($table, $data, $updateData = []) {
        if (empty($data)) {
            return false;
        }
        
        $columns = array_keys($data);
        $placeholders = str_repeat('?,', count($columns) - 1) . '?';
        $values = array_values($data);
        
        $sql = "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES ($placeholders)";
        
        if (!empty($updateData)) {
            $updateClause = [];
            foreach ($updateData as $key => $value) {
                $updateClause[] = "`$key` = ?";
                $values[] = $value;
            }
            $sql .= " ON DUPLICATE KEY UPDATE " . implode(', ', $updateClause);
        } else {
            foreach ($columns as $column) {
                $updateClause[] = "`$column` = VALUES(`$column`)";
            }
            $sql .= " ON DUPLICATE KEY UPDATE " . implode(', ', $updateClause);
        }
        
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        
        $types = $this->getParamTypes($values);
        $stmt->bind_param($types, ...$values);
        
        $result = $stmt->execute();
        $this->lastInsertId = $this->connection->insert_id;
        $this->affectedRows = $this->connection->affected_rows;
        
        return $result;
    }
    
    /**
     * Join tables and select
     * @param string $mainTable
     * @param array $joins
     * @param array $where
     * @param string $columns
     * @param string $orderBy
     * @param int $limit
     * @return array
     */
    public function joinSelect($mainTable, $joins, $where = [], $columns = '*', $orderBy = '', $limit = 0) {
        $sql = "SELECT $columns FROM `$mainTable`";
        
        foreach ($joins as $join) {
            $type = $join['type'] ?? 'LEFT';
            $table = $join['table'];
            $on = $join['on'];
            $sql .= " $type JOIN `$table` ON $on";
        }
        
        $params = [];
        if (!empty($where)) {
            $whereClause = [];
            foreach ($where as $key => $value) {
                $whereClause[] = "$key = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        
        if ($limit > 0) {
            $sql .= " LIMIT $limit";
        }
        
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        
        if (!empty($params)) {
            $types = $this->getParamTypes($params);
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // ==================== UTILITY FUNCTIONS ====================
    
    /**
     * Get parameter types for bind_param
     * @param array $values
     * @return string
     */
    private function getParamTypes($values) {
        $types = '';
        foreach ($values as $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        return $types;
    }
    
    /**
     * Get last insert ID
     * @return int
     */
    public function getLastInsertId() {
        return $this->lastInsertId;
    }
    
    /**
     * Get affected rows
     * @return int
     */
    public function getAffectedRows() {
        return $this->affectedRows;
    }
    
    /**
     * Begin transaction
     * @return bool
     */
    public function beginTransaction() {
        return $this->connection->begin_transaction();
    }
    
    /**
     * Commit transaction
     * @return bool
     */
    public function commit() {
        return $this->connection->commit();
    }
    
    /**
     * Rollback transaction
     * @return bool
     */
    public function rollback() {
        return $this->connection->rollback();
    }
    
    /**
     * Get connection
     * @return mysqli
     */
    public function getConnection() {
        return $this->connection;
    }
    
    // ==================== CRYPTO SPECIFIC HELPERS ====================
    
    /**
     * Get coin data for widgets
     * @param string $coinId
     * @return array|null
     */
    public function getCoinForWidget($coinId) {
        return $this->queryOne("
            SELECT 
                c.id,
                c.name,
                c.symbol,
                cmd.current_price_usd,
                cmd.price_change_percentage_24h,
                cmd.price_change_percentage_7d,
                cmd.market_cap_usd,
                cmd.market_cap_rank,
                cmd.total_volume_usd,
                cmd.high_24h_usd,
                cmd.low_24h_usd,
                ci.large as logo
            FROM coins c
            LEFT JOIN coin_market_data cmd ON c.id = cmd.coin_id
            LEFT JOIN coin_images ci ON c.id = ci.coin_id
            WHERE c.id = ?
        ", [$coinId]);
    }
    
    /**
     * Get top coins
     * @param int $limit
     * @return array
     */
    public function getTopCoins($limit = 100) {
        return $this->query("
            SELECT 
                c.id,
                c.name,
                c.symbol,
                cmd.current_price_usd,
                cmd.market_cap_usd,
                cmd.market_cap_rank,
                cmd.price_change_percentage_24h,
                ci.small as logo
            FROM coins c
            JOIN coin_market_data cmd ON c.id = cmd.coin_id
            LEFT JOIN coin_images ci ON c.id = ci.coin_id
            WHERE cmd.market_cap_rank > 0
            ORDER BY cmd.market_cap_rank ASC
            LIMIT ?
        ", [$limit]);
    }
    
    /**
     * Search coins
     * @param string $query
     * @param int $limit
     * @return array
     */
    public function searchCoins($query, $limit = 20) {
        $searchTerm = "%$query%";
        return $this->query("
            SELECT 
                c.id,
                c.name,
                c.symbol,
                cmd.current_price_usd,
                cmd.market_cap_rank,
                cmd.price_change_percentage_24h,
                ci.small as logo
            FROM coins c
            LEFT JOIN coin_market_data cmd ON c.id = cmd.coin_id
            LEFT JOIN coin_images ci ON c.id = ci.coin_id
            WHERE c.name LIKE ? OR c.symbol LIKE ?
            ORDER BY cmd.market_cap_rank ASC
            LIMIT ?
        ", [$searchTerm, $searchTerm, $limit]);
    }
    
    /**
     * Format price for display
     * @param float $price
     * @return string
     */
    public function formatPrice($price) {
        if ($price >= 1) {
            return '$' . number_format($price, 2);
        } elseif ($price >= 0.01) {
            return '$' . number_format($price, 4);
        } else {
            return '$' . number_format($price, 8);
        }
    }
    
    /**
     * Get price change color
     * @param float $change
     * @return string
     */
    public function getPriceChangeColor($change) {
        if ($change > 0) return 'success';
        if ($change < 0) return 'danger';
        return 'secondary';
    }
}
?>