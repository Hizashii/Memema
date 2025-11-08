<?php
define('CINEMA_APP', true);

define('DB_HOST', 'sql108.infinityfree.com');
define('DB_USER', 'if0_40366111');
define('DB_PASS', 'iSaulkMzylk'); 
define('DB_NAME', 'if0_40366111_Cinema');

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

function closeDBConnection() {
  $conn = getDBConnection();
  if ($conn) {
    $conn->close();
  }
}

