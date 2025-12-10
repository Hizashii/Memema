-- =============================================
-- CINEMA DATABASE SCHEMA
-- =============================================
-- Version: 2.0
-- Updated: Fixed foreign key constraints, added views and triggers
-- =============================================

-- Create database if it doesn't exist and select it
CREATE DATABASE IF NOT EXISTS cinema CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cinema;

-- Drop tables in reverse order of dependencies
DROP TABLE IF EXISTS seat_reservations;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS showtimes;
DROP TABLE IF EXISTS shows;
DROP TABLE IF EXISTS screens;
DROP TABLE IF EXISTS movie_genres;
DROP TABLE IF EXISTS movies;
DROP TABLE IF EXISTS venues;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS contact_info;
DROP TABLE IF EXISTS news;
DROP TABLE IF EXISTS booking_audit;

-- =============================================
-- NEWS TABLE
-- =============================================
CREATE TABLE IF NOT EXISTS news (
  id INT AUTO_INCREMENT PRIMARY KEY,
  img VARCHAR(255) NOT NULL,
  title VARCHAR(255) NOT NULL,
  excerpt TEXT NOT NULL,
  url VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- MOVIES TABLE
-- =============================================
CREATE TABLE IF NOT EXISTS movies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  img VARCHAR(255) NOT NULL,
  duration_minutes INT DEFAULT NULL,
  rating DECIMAL(2,1) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_title (title),
  INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- MOVIE GENRES TABLE
-- =============================================
CREATE TABLE IF NOT EXISTS movie_genres (
  movie_id INT NOT NULL,
  genre VARCHAR(50) NOT NULL,
  PRIMARY KEY (movie_id, genre),
  FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- VENUES TABLE
-- =============================================
CREATE TABLE IF NOT EXISTS venues (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  address VARCHAR(255) NOT NULL,
  phone VARCHAR(50) NOT NULL,
  image VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- SCREENS TABLE
-- =============================================
CREATE TABLE IF NOT EXISTS screens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  venue_id INT NOT NULL,
  screen_name VARCHAR(100) NOT NULL,
  screen_type ENUM('standard', 'premium', 'imax', 'vip') NOT NULL,
  base_price DECIMAL(6,2) NOT NULL,
  capacity INT NOT NULL,
  FOREIGN KEY (venue_id) REFERENCES venues(id) ON DELETE CASCADE,
  INDEX idx_venue (venue_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- USERS TABLE
-- =============================================
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(20) DEFAULT NULL,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- BOOKINGS TABLE
-- Fixed: Using SET NULL for foreign keys to prevent constraint failures
-- =============================================
CREATE TABLE IF NOT EXISTS bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT DEFAULT NULL,
  movie_id INT DEFAULT NULL,
  venue_id INT DEFAULT NULL,
  screen_id INT DEFAULT NULL,
  show_date DATE NOT NULL,
  show_time TIME NOT NULL,
  seats_count INT NOT NULL,
  total_price DECIMAL(8,2) NOT NULL,
  status ENUM('confirmed', 'cancelled', 'completed') DEFAULT 'confirmed',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  -- Foreign keys with SET NULL to prevent constraint failures on delete
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE SET NULL,
  FOREIGN KEY (venue_id) REFERENCES venues(id) ON DELETE SET NULL,
  FOREIGN KEY (screen_id) REFERENCES screens(id) ON DELETE SET NULL,
  -- Indexes for common queries
  INDEX idx_user (user_id),
  INDEX idx_movie (movie_id),
  INDEX idx_show_date (show_date),
  INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- SEAT RESERVATIONS TABLE
-- =============================================
CREATE TABLE IF NOT EXISTS seat_reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  screen_id INT DEFAULT NULL,
  seat_row CHAR(1) NOT NULL,
  seat_number INT NOT NULL,
  is_wheelchair BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  FOREIGN KEY (screen_id) REFERENCES screens(id) ON DELETE SET NULL,
  INDEX idx_booking (booking_id),
  UNIQUE KEY unique_seat_booking (booking_id, seat_row, seat_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- CONTACT INFO TABLE
-- =============================================
CREATE TABLE IF NOT EXISTS contact_info (
  id INT AUTO_INCREMENT PRIMARY KEY,
  phone VARCHAR(50) NOT NULL,
  email VARCHAR(255) NOT NULL,
  address VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- CONTACT MESSAGES TABLE
-- =============================================
CREATE TABLE IF NOT EXISTS contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  subject VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  status ENUM('new', 'read', 'replied') DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_status (status),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- SHOWS TABLE
-- =============================================
CREATE TABLE IF NOT EXISTS shows (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  img VARCHAR(255) NOT NULL,
  tag_text VARCHAR(50) NOT NULL,
  tag_color VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- SHOWTIMES TABLE
-- =============================================
CREATE TABLE IF NOT EXISTS showtimes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  show_id INT NOT NULL,
  time VARCHAR(20) NOT NULL,
  FOREIGN KEY (show_id) REFERENCES shows(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- BOOKING AUDIT TABLE (for triggers)
-- =============================================
CREATE TABLE IF NOT EXISTS booking_audit (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  action ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
  old_status VARCHAR(20) DEFAULT NULL,
  new_status VARCHAR(20) DEFAULT NULL,
  old_total DECIMAL(8,2) DEFAULT NULL,
  new_total DECIMAL(8,2) DEFAULT NULL,
  changed_by VARCHAR(100) DEFAULT NULL,
  changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_booking (booking_id),
  INDEX idx_action (action),
  INDEX idx_changed_at (changed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- VIEWS
-- =============================================

-- View: Movie details with genres
CREATE OR REPLACE VIEW view_movies_with_genres AS
SELECT 
    m.id,
    m.title,
    m.img,
    m.duration_minutes,
    m.rating,
    m.created_at,
    GROUP_CONCAT(mg.genre ORDER BY mg.genre SEPARATOR ', ') AS genres
FROM movies m
LEFT JOIN movie_genres mg ON m.id = mg.movie_id
GROUP BY m.id, m.title, m.img, m.duration_minutes, m.rating, m.created_at;

-- View: Booking details with all related info
CREATE OR REPLACE VIEW view_booking_details AS
SELECT 
    b.id AS booking_id,
    b.show_date,
    b.show_time,
    b.seats_count,
    b.total_price,
    b.status,
    b.created_at AS booking_date,
    u.id AS user_id,
    u.full_name AS user_name,
    u.email AS user_email,
    m.id AS movie_id,
    m.title AS movie_title,
    m.img AS movie_image,
    v.id AS venue_id,
    v.name AS venue_name,
    v.address AS venue_address,
    s.id AS screen_id,
    s.screen_name,
    s.screen_type
FROM bookings b
LEFT JOIN users u ON b.user_id = u.id
LEFT JOIN movies m ON b.movie_id = m.id
LEFT JOIN venues v ON b.venue_id = v.id
LEFT JOIN screens s ON b.screen_id = s.id;

-- View: Venue with screens summary
CREATE OR REPLACE VIEW view_venue_screens AS
SELECT 
    v.id AS venue_id,
    v.name AS venue_name,
    v.address,
    v.phone,
    v.image,
    COUNT(s.id) AS total_screens,
    SUM(s.capacity) AS total_capacity,
    MIN(s.base_price) AS min_price,
    MAX(s.base_price) AS max_price
FROM venues v
LEFT JOIN screens s ON v.id = s.venue_id
GROUP BY v.id, v.name, v.address, v.phone, v.image;

-- View: Daily booking statistics
CREATE OR REPLACE VIEW view_daily_stats AS
SELECT 
    DATE(created_at) AS booking_date,
    COUNT(*) AS total_bookings,
    SUM(total_price) AS total_revenue,
    SUM(seats_count) AS total_seats,
    AVG(total_price) AS avg_booking_value
FROM bookings
WHERE status = 'confirmed'
GROUP BY DATE(created_at)
ORDER BY booking_date DESC;

-- View: Popular movies (by booking count)
CREATE OR REPLACE VIEW view_popular_movies AS
SELECT 
    m.id,
    m.title,
    m.img,
    m.rating,
    COUNT(b.id) AS booking_count,
    SUM(b.seats_count) AS total_seats_sold,
    SUM(b.total_price) AS total_revenue
FROM movies m
LEFT JOIN bookings b ON m.id = b.movie_id AND b.status = 'confirmed'
GROUP BY m.id, m.title, m.img, m.rating
ORDER BY booking_count DESC;

-- =============================================
-- TRIGGERS
-- =============================================

-- Trigger: Log booking insertions
DELIMITER //
CREATE TRIGGER trg_booking_insert
AFTER INSERT ON bookings
FOR EACH ROW
BEGIN
    INSERT INTO booking_audit (booking_id, action, new_status, new_total, changed_at)
    VALUES (NEW.id, 'INSERT', NEW.status, NEW.total_price, NOW());
END//
DELIMITER ;

-- Trigger: Log booking updates
DELIMITER //
CREATE TRIGGER trg_booking_update
AFTER UPDATE ON bookings
FOR EACH ROW
BEGIN
    INSERT INTO booking_audit (
        booking_id, action, old_status, new_status, old_total, new_total, changed_at
    )
    VALUES (
        NEW.id, 'UPDATE', OLD.status, NEW.status, OLD.total_price, NEW.total_price, NOW()
    );
END//
DELIMITER ;

-- Trigger: Log booking deletions
DELIMITER //
CREATE TRIGGER trg_booking_delete
BEFORE DELETE ON bookings
FOR EACH ROW
BEGIN
    INSERT INTO booking_audit (booking_id, action, old_status, old_total, changed_at)
    VALUES (OLD.id, 'DELETE', OLD.status, OLD.total_price, NOW());
END//
DELIMITER ;

-- Trigger: Auto-update movie timestamp when genres change
DELIMITER //
CREATE TRIGGER trg_genre_insert
AFTER INSERT ON movie_genres
FOR EACH ROW
BEGIN
    UPDATE movies SET updated_at = NOW() WHERE id = NEW.movie_id;
END//
DELIMITER ;

DELIMITER //
CREATE TRIGGER trg_genre_delete
AFTER DELETE ON movie_genres
FOR EACH ROW
BEGIN
    UPDATE movies SET updated_at = NOW() WHERE id = OLD.movie_id;
END//
DELIMITER ;

-- =============================================
-- INSERT SAMPLE DATA
-- =============================================

-- INSERT NEWS DATA
INSERT INTO news (img, title, excerpt, url) VALUES
('./assets/img/OutsideCinema.jpg', 'Local Film Festival Kicks Off', 'The annual city film festival began last night…', '#'),
('./assets/img/AbsoluteRobotAi.jpg', 'Director on AI & Cinema', 'Renowned director Maria Chen shared her insights…', '#'),
('./assets/img/movieMarathon.png', 'Classic Movie Marathon', 'Get ready for a nostalgic trip as CinemaBook announces…', '#');

-- INSERT MOVIES DATA
INSERT INTO movies (title, img, duration_minutes, rating) VALUES
('Oppenheimer', './assets/img/Oppenheimer.jpg', 180, 4.8),
('The Dark Knight', './assets/img/TheDarkKnight.jpg', 152, 4.9),
('Joker', './assets/img/Joker.png', 122, 4.6),
('The Godfather', './assets/img/TheGodFather.jpg', 175, 4.9),
('Pulp Fiction', './assets/img/PulpFiction.jpg', 154, 4.7),
('Alien', './assets/img/Alien.jpg', 117, 4.5),
('Free Guy', './assets/img/FreeGuy.jpg', 115, 4.2),
('Brave', './assets/img/Brave.jpg', 93, 4.1),
('7 Samurai', './assets/img/7Samurai.jpg', 207, 4.8),
('The Power of the Dog', './assets/img/ThePowerOfTheDog.jpg', 126, 4.3),
('Wreck-It Ralph', './assets/img/WreckItRalph.jpg', 101, 4.0),
('The Lorax', './assets/img/TheLorax.jpg', 86, 3.8);

-- INSERT MOVIE GENRES DATA
INSERT INTO movie_genres (movie_id, genre) VALUES
(1, 'Drama'), (1, 'Biography'),
(2, 'Action'), (2, 'Crime'),
(3, 'Drama'), (3, 'Crime'),
(4, 'Crime'), (4, 'Drama'),
(5, 'Crime'), (5, 'Drama'),
(6, 'Horror'), (6, 'Sci-Fi'),
(7, 'Comedy'), (7, 'Action'),
(8, 'Animation'), (8, 'Family'),
(9, 'Drama'), (9, 'Action'),
(10, 'Drama'), (10, 'Western'),
(11, 'Animation'), (11, 'Comedy'),
(12, 'Animation'), (12, 'Family');

-- INSERT VENUES DATA
INSERT INTO venues (name, address, phone, image) VALUES
('Downtown Cinema', '123 Main Street, Downtown, DC 12345', '+1 (555) 123-4567', './assets/img/OutsideCinema.jpg'),
('Grand Plex', '456 Entertainment Blvd, Midtown, MP 23456', '+1 (555) 234-5678', './assets/img/cinemaMan.jpg'),
('Sunset Drive-In', '789 Sunset Highway, Suburbia, SH 34567', '+1 (555) 345-6789', './assets/img/movieMarathon.png'),
('Majestic Theaters', '321 Historic Avenue, Old Town, OT 45678', '+1 (555) 456-7890', './assets/img/warsStar.jpg');

-- INSERT SCREENS DATA
INSERT INTO screens (venue_id, screen_name, screen_type, base_price, capacity) VALUES
-- Downtown Cinema screens
(1, 'Screen 1', 'standard', 12.50, 150),
(1, 'Screen 2', 'premium', 15.00, 120),
(1, 'Screen 3', 'imax', 18.00, 200),
-- Grand Plex screens
(2, 'Main Hall', 'imax', 20.00, 300),
(2, 'Screen A', 'premium', 16.00, 100),
(2, 'Screen B', 'standard', 13.00, 80),
(2, 'VIP Lounge', 'vip', 25.00, 30),
-- Sunset Drive-In screens
(3, 'Outdoor Screen', 'standard', 10.00, 200),
(3, 'Indoor Theater', 'premium', 14.00, 100),
-- Majestic Theaters screens
(4, 'Historic Hall', 'premium', 17.00, 150),
(4, 'Screen 1', 'standard', 12.00, 100),
(4, 'Screen 2', 'standard', 12.00, 100);

-- INSERT USERS DATA (password is 'password' hashed with bcrypt)
INSERT INTO users (full_name, email, password, phone) VALUES
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1 (555) 123-4567');

-- INSERT CONTACT INFO DATA
INSERT INTO contact_info (phone, email, address) VALUES
('+1 (555) 123-CINE', 'info@cinemabook.com', '123 Cinema Street, Movie City, MC 12345');

-- INSERT SHOWS DATA
INSERT INTO shows (title, img, tag_text, tag_color) VALUES
('Oppenheimer', './assets/img/Oppenheimer.jpg', 'Drama', 'bg-cyan-600/90'),
('The Dark Knight', './assets/img/TheDarkKnight.jpg', 'Action', 'bg-emerald-600/90'),
('Free Guy', './assets/img/FreeGuy.jpg', 'Comedy', 'bg-emerald-600/90'),
('Joker', './assets/img/Joker.png', 'Drama', 'bg-teal-600/90');

-- INSERT SHOWTIMES DATA
-- Oppenheimer (show_id = 1)
INSERT INTO showtimes (show_id, time) VALUES
(1, '10:00 AM'),
(1, '01:30 PM'),
(1, '04:00 PM');

-- The Dark Knight (show_id = 2)
INSERT INTO showtimes (show_id, time) VALUES
(2, '11:00 AM'),
(2, '02:15 PM'),
(2, '05:00 PM');

-- Free Guy (show_id = 3)
INSERT INTO showtimes (show_id, time) VALUES
(3, '10:30 AM'),
(3, '01:00 PM'),
(3, '03:45 PM');

-- Joker (show_id = 4)
INSERT INTO showtimes (show_id, time) VALUES
(4, '12:00 PM'),
(4, '02:45 PM'),
(4, '05:30 PM');
