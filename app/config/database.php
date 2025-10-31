<?php
define('CINEMA_APP', true);

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cinema_booking');

function getDBConnection() {
  static $connection = null;
  
  if ($connection === null) {
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($connection->connect_error) {
      throw new Exception("Database connection failed: " . $connection->connect_error);
    }
    $connection->set_charset("utf8mb4");
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

function closeDBConnection() {
  $conn = getDBConnection();
  if ($conn) {
    $conn->close();
  }
}

