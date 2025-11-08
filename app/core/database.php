<?php
require_once __DIR__ . '/../config/database.php';

function getMovie($id) {
  return executePreparedQuery("SELECT * FROM movies WHERE id = ?", [$id], 'i');
}

function getVenue($id) {
  return executePreparedQuery("SELECT * FROM venues WHERE id = ?", [$id], 'i');
}

function getScreen($id) {
  return executePreparedQuery("SELECT * FROM screens WHERE id = ?", [$id], 'i');
}

function getBookedSeats($screenId, $showDate, $showTime) {
  $query = "
    SELECT sr.seat_row, sr.seat_number 
    FROM seat_reservations sr 
    JOIN bookings b ON sr.booking_id = b.id 
    WHERE b.screen_id = ? 
    AND b.show_date = ? 
    AND b.show_time = ?
  ";
  return executePreparedQuery($query, [$screenId, $showDate, $showTime], 'iss');
}

function insertBooking($userId, $movieId, $venueId, $screenId, $showDate, $showTime, $seatsCount, $totalPrice) {
  $query = "INSERT INTO bookings (user_id, movie_id, venue_id, screen_id, show_date, show_time, seats_count, total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
  $conn = getDBConnection();
  $stmt = $conn->prepare($query);
  $stmt->bind_param('iiiissid', $userId, $movieId, $venueId, $screenId, $showDate, $showTime, $seatsCount, $totalPrice);
  $stmt->execute();
  $bookingId = $conn->insert_id;
  $stmt->close();
  return $bookingId;
}

function insertSeatReservation($bookingId, $screenId, $seatRow, $seatNumber, $isWheelchair) {
  $query = "INSERT INTO seat_reservations (booking_id, screen_id, seat_row, seat_number, is_wheelchair) VALUES (?, ?, ?, ?, ?)";
  return executePreparedQuery($query, [$bookingId, $screenId, $seatRow, $seatNumber, $isWheelchair], 'iisii');
}

function getBasePath() {
  static $basePath = null;
  if ($basePath === null) {
    $requestUri = strtolower(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '');
    $scriptName = strtolower($_SERVER['SCRIPT_NAME'] ?? '');
    
    if (preg_match('#^/(cinema|Cinema)/#i', $requestUri) || preg_match('#/(cinema|Cinema)/#i', $scriptName)) {
      $actualPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
      if (preg_match('#^/([^/]+)/#', $actualPath, $matches)) {
        $basePath = '/' . $matches[1];
      } else {
        $basePath = '/cinema';
      }
    } else {
      $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
      if ($scriptDir === '/' || $scriptDir === '.') {
        $basePath = '';
      } else {
        $basePath = rtrim($scriptDir, '/');
        $basePathLower = strtolower($basePath);
        if (strpos($basePathLower, '/public/frontend') !== false) {
          $basePath = str_ireplace('/public/frontend', '', $basePath);
          $basePath = rtrim($basePath, '/');
        }
        if (strpos($basePathLower, '/admin') !== false) {
          $basePath = str_ireplace('/admin', '', $basePath);
          $basePath = rtrim($basePath, '/');
        }
      }
    }
  }
  return $basePath;
}

function getImagePath($imagePath) {
  $base = getBasePath();
  
  if (empty($imagePath)) {
    return $base . '/assets/img/default.jpg';
  }
  
  if (strpos($imagePath, 'http') === 0) {
    return $imagePath;
  }
  
  if (preg_match('#^/(Cinema|cinema)/#i', $imagePath)) {
    $imagePath = preg_replace('#^/(Cinema|cinema)/#i', $base . '/', $imagePath);
    return $imagePath;
  }
  
  if (strpos($imagePath, './') === 0) {
    $imagePath = str_replace('./', '', $imagePath);
    if (strpos($imagePath, '/') !== 0) {
      $imagePath = '/' . $imagePath;
    }
    return $base . $imagePath;
  }
  
  if (strpos($imagePath, '/') === 0) {
    return $base . $imagePath;
  }
  
  $filename = basename($imagePath);
  return $base . '/assets/img/' . $filename;
}