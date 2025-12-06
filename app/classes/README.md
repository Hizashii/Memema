# Object-Oriented Programming (OOP) Implementation

This directory contains all the OOP classes for the Cinema application. The project has been refactored to use Object-Oriented Programming principles as required.

## Classes Overview

### 1. Database Class (`Database.php`)
- **Purpose**: Handles all database operations using a Singleton pattern
- **Methods**:
  - `getConnection()` - Get database connection (Singleton)
  - `query($sql, $params, $types)` - Execute SELECT queries
  - `queryOne($sql, $params, $types)` - Execute SELECT and return single row
  - `execute($sql, $params, $types)` - Execute INSERT/UPDATE/DELETE
  - `close()` - Close database connection

**Example Usage:**
```php
require_once __DIR__ . '/../app/classes/autoload.php';

// Get all movies
$movies = Database::query("SELECT * FROM movies");

// Get single movie
$movie = Database::queryOne("SELECT * FROM movies WHERE id = ?", [1], 'i');

// Insert new record
$id = Database::execute(
    "INSERT INTO movies (title, img) VALUES (?, ?)",
    ['Movie Title', './assets/img/movie.jpg'],
    'ss'
);
```

### 2. ImageUpload Class (`ImageUpload.php`)
- **Purpose**: Handles image upload operations
- **Methods**:
  - `upload($file, $prefix)` - Upload an image file
  - `delete($filename)` - Delete an image file

**Example Usage:**
```php
require_once __DIR__ . '/../app/classes/autoload.php';

$imageUpload = new ImageUpload();
$fileInfo = $imageUpload->upload($_FILES['image']);
// Returns: ['filename', 'path', 'relative_path', 'url']
```

### 3. Movie Class (`Movie.php`)
- **Purpose**: Handles movie CRUD operations
- **Methods**:
  - `getAll()` - Get all movies with genres
  - `getById($id)` - Get movie by ID
  - `getGenres($movieId)` - Get genres for a movie
  - `create()` - Create new movie
  - `update()` - Update movie
  - `delete($id)` - Delete movie

**Example Usage:**
```php
require_once __DIR__ . '/../app/classes/autoload.php';

// Create new movie
$movie = new Movie();
$movie->setTitle('Oppenheimer');
$movie->setImg('./assets/img/oppenheimer.jpg');
$movie->setDurationMinutes(180);
$movie->setRating(4.8);
$movie->setGenres(['Drama', 'Biography']);
$movieId = $movie->create();

// Get all movies
$movies = Movie::getAll();

// Update movie
$movie = new Movie(Movie::getById($movieId));
$movie->setRating(4.9);
$movie->update();
```

### 4. Venue Class (`Venue.php`)
- **Purpose**: Handles venue CRUD operations
- **Methods**:
  - `getAll()` - Get all venues with screens
  - `getById($id)` - Get venue by ID
  - `getScreens($venueId)` - Get screens for a venue
  - `create()` - Create new venue
  - `update()` - Update venue
  - `delete($id)` - Delete venue

**Example Usage:**
```php
require_once __DIR__ . '/../app/classes/autoload.php';

$venue = new Venue();
$venue->setName('Downtown Cinema');
$venue->setAddress('123 Main Street');
$venue->setPhone('+1 (555) 123-4567');
$venue->setImage('./assets/img/venue.jpg');
$venueId = $venue->create();
```

### 5. News Class (`News.php`)
- **Purpose**: Handles news CRUD operations
- **Methods**:
  - `getAll($limit)` - Get all news items
  - `getById($id)` - Get news by ID
  - `create()` - Create new news item
  - `update()` - Update news item
  - `delete($id)` - Delete news item

**Example Usage:**
```php
require_once __DIR__ . '/../app/classes/autoload.php';

$news = new News();
$news->setTitle('Local Film Festival');
$news->setExcerpt('The festival begins...');
$news->setImg('./assets/img/festival.jpg');
$news->setUrl('#');
$news->create();
```

### 6. Booking Class (`Booking.php`)
- **Purpose**: Handles booking operations
- **Methods**:
  - `getAll()` - Get all bookings with related data
  - `getById($id)` - Get booking by ID
  - `getByUserId($userId)` - Get bookings for a user
  - `getBookedSeats($screenId, $showDate, $showTime)` - Get booked seats
  - `create($seats)` - Create new booking with seats
  - `delete($id)` - Delete booking

**Example Usage:**
```php
require_once __DIR__ . '/../app/classes/autoload.php';

$booking = new Booking();
$booking->setUserId(1);
$booking->setMovieId(1);
$booking->setVenueId(1);
$booking->setScreenId(1);
$booking->setShowDate('2024-01-15');
$booking->setShowTime('14:00:00');
$booking->setSeatsCount(2);
$booking->setTotalPrice(25.00);

$seats = [
    ['row' => 'A', 'number' => 1, 'is_wheelchair' => false],
    ['row' => 'A', 'number' => 2, 'is_wheelchair' => false]
];
$bookingId = $booking->create($seats);
```

### 7. User Class (`User.php`)
- **Purpose**: Handles user operations
- **Methods**:
  - `getAll()` - Get all users (without passwords)
  - `getById($id)` - Get user by ID
  - `getByEmail($email)` - Get user by email
  - `create()` - Create new user (password is hashed)
  - `update()` - Update user
  - `verifyPassword($password)` - Verify user password
  - `delete($id)` - Delete user

**Example Usage:**
```php
require_once __DIR__ . '/../app/classes/autoload.php';

// Create user
$user = new User();
$user->setFullName('John Doe');
$user->setEmail('john@example.com');
$user->setPassword('securepassword');
$user->setPhone('+1 (555) 123-4567');
$userId = $user->create();

// Verify password
$user = new User(User::getByEmail('john@example.com'));
if ($user->verifyPassword('securepassword')) {
    // Login successful
}
```

## How to Use Classes

1. **Include the autoloader:**
```php
require_once __DIR__ . '/../app/classes/autoload.php';
```

2. **Use static methods for queries:**
```php
$movies = Movie::getAll();
$movie = Movie::getById(1);
```

3. **Use instance methods for create/update:**
```php
$movie = new Movie();
$movie->setTitle('New Movie');
$movie->create();
```

## OOP Principles Used

1. **Encapsulation**: All classes use private properties with public getters/setters
2. **Single Responsibility**: Each class handles one entity (Movie, Venue, etc.)
3. **Singleton Pattern**: Database class uses Singleton for connection management
4. **Static Methods**: Used for query operations that don't require instance state
5. **Instance Methods**: Used for operations that modify object state (create/update)

## Image Storage

- **Location**: All images are stored in `/assets/img/` directory
- **Naming**: Images are automatically renamed with unique IDs: `img_[uniqid]_[timestamp].[ext]`
- **Path Format**: Images are stored with relative paths: `./assets/img/filename.jpg`
- **URL Format**: Full URLs are generated using `getBasePath()`: `/Cinema/assets/img/filename.jpg`

