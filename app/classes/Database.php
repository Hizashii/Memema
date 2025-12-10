<?php
/**
 * Database Class
 * 
 * Singleton pattern for database connection management.
 * All database operations should go through this class.
 */
class Database {
    private static $connection = null;
    
    /**
     * Get database connection (Singleton pattern)
     */
    public static function getConnection() {
        if (self::$connection === null) {
            // Load config if constants not defined
            if (!defined('DB_HOST')) {
                if (!defined('CINEMA_APP')) {
                    define('CINEMA_APP', true);
                }
                require_once __DIR__ . '/../config/database.php';
            }
            
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            self::$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            self::$connection->set_charset('utf8mb4');
        }
        return self::$connection;
    }
    
    /**
     * Execute a SELECT query and return all results
     */
    public static function query($sql, $params = [], $types = '') {
        $conn = self::getConnection();
        
        if (empty($params)) {
            $result = $conn->query($sql);
            if (!$result) {
                throw new Exception("Query failed: " . $conn->error);
            }
            
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            return $data;
        }
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $data = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        
        $stmt->close();
        return $data;
    }
    
    /**
     * Execute a query and return single row
     */
    public static function queryOne($sql, $params = [], $types = '') {
        $results = self::query($sql, $params, $types);
        return !empty($results) ? $results[0] : null;
    }
    
    /**
     * Execute INSERT/UPDATE/DELETE and return affected rows or insert ID
     */
    public static function execute($sql, $params = [], $types = '') {
        $conn = self::getConnection();
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        if (!empty($params) && !empty($types)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $insertId = $conn->insert_id;
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        
        return $insertId > 0 ? $insertId : $affectedRows;
    }
    
    /**
     * Get count from a table
     */
    public static function count($table, $where = '', $params = [], $types = '') {
        $sql = "SELECT COUNT(*) as count FROM " . self::escapeIdentifier($table);
        if (!empty($where)) {
            $sql .= " WHERE " . $where;
        }
        $result = self::queryOne($sql, $params, $types);
        return (int)($result['count'] ?? 0);
    }
    
    /**
     * Get sum from a table column
     */
    public static function sum($table, $column, $where = '', $params = [], $types = '') {
        $sql = "SELECT SUM(" . self::escapeIdentifier($column) . ") as total FROM " . self::escapeIdentifier($table);
        if (!empty($where)) {
            $sql .= " WHERE " . $where;
        }
        $result = self::queryOne($sql, $params, $types);
        return (float)($result['total'] ?? 0);
    }
    
    /**
     * Escape table/column identifiers
     */
    private static function escapeIdentifier($identifier) {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
    
    /**
     * Begin a transaction
     */
    public static function beginTransaction() {
        self::getConnection()->begin_transaction();
    }
    
    /**
     * Commit a transaction
     */
    public static function commit() {
        self::getConnection()->commit();
    }
    
    /**
     * Rollback a transaction
     */
    public static function rollback() {
        self::getConnection()->rollback();
    }
    
    /**
     * Close database connection
     */
    public static function close() {
        if (self::$connection !== null) {
            self::$connection->close();
            self::$connection = null;
        }
    }
}
