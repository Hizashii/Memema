<?php

class Database {
    private static $instance = null;
    private $connection = null;
    
    private function __construct() {
        require_once __DIR__ . '/../config/database.php';
        
        if (!defined('DB_HOST') || !defined('DB_USER') || !defined('DB_NAME')) {
            $errorMsg = 'Database configuration constants are not defined. Please check secrets.php.';
            $errorMsg .= ' DB_HOST: ' . (defined('DB_HOST') ? DB_HOST : 'NOT DEFINED');
            $errorMsg .= ' DB_USER: ' . (defined('DB_USER') ? DB_USER : 'NOT DEFINED');
            $errorMsg .= ' DB_NAME: ' . (defined('DB_NAME') ? DB_NAME : 'NOT DEFINED');
            throw new Exception($errorMsg);
        }
        
        $dbPass = defined('DB_PASS') ? DB_PASS : '';
        $dbUser = DB_USER;
        $dbHost = DB_HOST;
        $dbName = DB_NAME;
        
        try {
            $dsn = "mysql:host=" . $dbHost . ";dbname=" . $dbName . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, $dbUser, $dbPass, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            if (defined('APP_DEBUG') && APP_DEBUG) {
                $debugInfo = "<br><br><strong>Debug Info:</strong><br>";
                $debugInfo .= "DB_HOST: " . (defined('DB_HOST') ? htmlspecialchars(DB_HOST) : 'NOT DEFINED') . "<br>";
                $debugInfo .= "DB_USER: " . (defined('DB_USER') ? htmlspecialchars(DB_USER) : 'NOT DEFINED') . "<br>";
                $debugInfo .= "DB_NAME: " . (defined('DB_NAME') ? htmlspecialchars(DB_NAME) : 'NOT DEFINED') . "<br>";
                $debugInfo .= "DB_PASS: " . (defined('DB_PASS') ? (strlen(DB_PASS) > 0 ? 'SET (' . strlen(DB_PASS) . ' chars)' : 'EMPTY') : 'NOT DEFINED') . "<br>";
                die("Database connection failed: " . htmlspecialchars($e->getMessage()) . $debugInfo);
            } else {
                die("Database connection failed. Please contact the administrator.");
            }
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage() . " | SQL: " . $sql);
            throw $e;
        }
    }
    
    public function queryOne($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage() . " | SQL: " . $sql);
            throw $e;
        }
    }
    
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $this->connection->lastInsertId() ?: true;
        } catch (PDOException $e) {
            error_log("Database execute error: " . $e->getMessage() . " | SQL: " . $sql);
            throw $e;
        }
    }
    
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    public function commit() {
        return $this->connection->commit();
    }
    
    public function rollback() {
        return $this->connection->rollBack();
    }
}

