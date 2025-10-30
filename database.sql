-- Cinema Booking System Database
-- This file contains all the database schema and initial data

-- Create Database
CREATE DATABASE IF NOT EXISTS cinema_booking;
USE cinema_booking;

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

-- Movies table - stores movies
CREATE TABLE IF NOT EXISTS movies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  img VARCHAR(255) NOT NULL,
  duration_minutes INT DEFAULT NULL,
  rating DECIMAL(2,1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Movie genres table - genres per movie
CREATE TABLE IF NOT EXISTS movie_genres (
  movie_id INT NOT NULL,
  genre VARCHAR(50) NOT NULL,
  PRIMARY KEY (movie_id, genre),
  FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Venues table - stores cinema venues
CREATE TABLE IF NOT EXISTS venues (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  address VARCHAR(255) NOT NULL,
  phone VARCHAR(50) NOT NULL,
  image VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Screens table - screens within venues
CREATE TABLE IF NOT EXISTS screens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  venue_id INT NOT NULL,
  screen_name VARCHAR(100) NOT NULL,
  screen_type ENUM('standard', 'premium', 'imax', 'vip') NOT NULL,
  base_price DECIMAL(6,2) NOT NULL,
  capacity INT NOT NULL,
  FOREIGN KEY (venue_id) REFERENCES venues(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Users table - basic users
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(20) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bookings table - ticket bookings
CREATE TABLE IF NOT EXISTS bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  movie_id INT NOT NULL,
  venue_id INT NOT NULL,
  screen_id INT NOT NULL,
  show_date DATE NOT NULL,
  show_time TIME NOT NULL,
  seats_count INT NOT NULL,
  total_price DECIMAL(8,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (movie_id) REFERENCES movies(id),
  FOREIGN KEY (venue_id) REFERENCES venues(id),
  FOREIGN KEY (screen_id) REFERENCES screens(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seat reservations table - individual seat bookings
CREATE TABLE IF NOT EXISTS seat_reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  screen_id INT NOT NULL,
  seat_row CHAR(1) NOT NULL,
  seat_number INT NOT NULL,
  is_wheelchair BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  FOREIGN KEY (screen_id) REFERENCES screens(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contact info table - site contacts
CREATE TABLE IF NOT EXISTS contact_info (
  id INT AUTO_INCREMENT PRIMARY KEY,
  phone VARCHAR(50) NOT NULL,
  email VARCHAR(255) NOT NULL,
  address VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contact messages table - store contact form submissions
CREATE TABLE IF NOT EXISTS contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  subject VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  status ENUM('new', 'read', 'replied') DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
-- INSERT NEWS DATA
-- =============================================
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

-- INSERT USERS DATA
INSERT INTO users (full_name, email, password, phone) VALUES
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1 (555) 123-4567');

-- INSERT CONTACT INFO DATA
INSERT INTO contact_info (phone, email, address) VALUES
('+1 (555) 123-CINE', 'info@cinemabook.com', '123 Cinema Street, Movie City, MC 12345');

-- =============================================
-- INSERT SHOWS DATA
-- =============================================
INSERT INTO shows (title, img, tag_text, tag_color) VALUES
('Oppenheimer', './assets/img/Oppenheimer.jpg', 'Drama', 'bg-cyan-600/90'),
('The Dark Knight', './assets/img/TheDarkKnight.jpg', 'Action', 'bg-emerald-600/90'),
('Free Guy', './assets/img/FreeGuy.jpg', 'Comedy', 'bg-emerald-600/90'),
('Joker', './assets/img/Joker.png', 'Drama', 'bg-teal-600/90');

-- =============================================
-- INSERT SHOWTIMES DATA
-- =============================================
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

