<?php
/**
 * Database Class
 * Handles all database operations using OOP
 */
class Database {
    private static $connection = null;
    
    /**
     * Get database connection (Singleton pattern)
     */
    public static function getConnection() {
        if (self::$connection === null) {
            require_once __DIR__ . '/../config/database.php';
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            self::$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            self::$connection->set_charset('utf8mb4');
        }
        return self::$connection;
    }
    
    /**
     * Execute a query and return results
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
        } else {
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
     * Close database connection
     */
    public static function close() {
        if (self::$connection !== null) {
            self::$connection->close();
            self::$connection = null;
        }
    }
}

