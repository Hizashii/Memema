<?php
define('CINEMA_APP', true);

// Load environment configuration
require_once __DIR__ . '/env.php';

// Database configuration from environment
define('DB_HOST', Env::get('DB_HOST', 'localhost'));
define('DB_USER', Env::get('DB_USER', 'root'));
define('DB_PASS', Env::get('DB_PASS', '')); 
define('DB_NAME', Env::get('DB_NAME', 'Cinema'));

function getDBConnection() {
  static $connection = null;
  if ($connection === null) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $connection->set_charset('utf8mb4');
  }
  return $connection;
}

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

// Execute INSERT and return the new ID
function executeInsert($query, $params = [], $types = '') {
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
  
  $insertId = $conn->insert_id;
  $stmt->close();
  return $insertId;
}

function closeDBConnection() {
  $conn = getDBConnection();
  if ($conn) {
    $conn->close();
  }
}

