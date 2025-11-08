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

function getImagePath($imagePath) {
  if (empty($imagePath)) {
    return '/Cinema/assets/img/default.jpg';
  }
  
  if (strpos($imagePath, '/Cinema/') === 0) {
    return $imagePath;
  }
  
  $filename = basename($imagePath);
  
  return '/Cinema/assets/img/' . $filename;
}