<?php
/**
 * Database Configuration
 * 
 * This file handles the database connection for the Cinema Booking System
 * Update the constants below with your database credentials
 */

// Define app constant for security
define('CINEMA_APP', true);

// Database Configuration Constants
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cinema_booking');

/**
 * Get Database Connection
 * 
 * @return mysqli Database connection object
 * @throws Exception if connection fails
 */
function getDBConnection() {
  static $connection = null;
  
  if ($connection === null) {
    // Create connection
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($connection->connect_error) {
      throw new Exception("Database connection failed: " . $connection->connect_error);
    }
    
    // Set charset to utf8mb4 for proper emoji and special character support
    $connection->set_charset("utf8mb4");
  }
  
  return $connection;
}

/**
 * Execute a SELECT query and return all results
 * 
 * @param string $query SQL query to execute
 * @return array Array of associative arrays containing results
 * @throws Exception if query fails
 */
function executeQuery($query) {
  $conn = getDBConnection();
  $result = $conn->query($query);
  
  if (!$result) {
    throw new Exception("Query failed: " . $conn->error);
  }
  
  $data = [];
  while ($row = $result->fetch_assoc()) {
    $data[] = $row;
  }
  
  return $data;
}

/**
 * Execute a prepared statement with parameters
 * 
 * @param string $query SQL query with placeholders
 * @param array $params Parameters to bind
 * @param string $types Parameter types (e.g., "ssi" for string, string, int)
 * @return array Array of associative arrays containing results
 * @throws Exception if query fails
 */
function executePreparedQuery($query, $params = [], $types = '') {
  $conn = getDBConnection();
  $stmt = $conn->prepare($query);
  
  if (!$stmt) {
    throw new Exception("Prepare failed: " . $conn->error);
  }
  
  if (!empty($params)) {
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
 * Close database connection
 */
function closeDBConnection() {
  $conn = getDBConnection();
  if ($conn) {
    $conn->close();
  }
}

