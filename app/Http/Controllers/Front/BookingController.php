<?php
/**
 * Booking Controller
 */

class BookingController extends Controller {
    private $movieRepository;
    private $venueRepository;
    private $bookingRepository;
    private $bookingService;
    
    public function __construct() {
        parent::__construct();
        $this->movieRepository = new MovieRepository();
        $this->venueRepository = new VenueRepository();
        $this->bookingRepository = new BookingRepository();
        $this->bookingService = new BookingService();
    }
    
    public function index() {
        $movieId = (int)($this->get('movie_id') ?? 0);
        
        if ($movieId > 0) {
            return $this->showSeatSelection($movieId);
        }
        
        try {
            $movies = $this->movieRepository->findAll();
        } catch (Exception $e) {
            $movies = [];
            $error = "Unable to load movies. Please try again later.";
        }
        
        $this->view('front/booking/index', [
            'movies' => $movies,
            'error' => $error ?? null
        ]);
    }
    
    private function showSeatSelection($movieId) {
        try {
            $movie = $this->movieRepository->findById($movieId);
            
            if (!$movie) {
                $base = $this->getBasePath();
                header('Location: ' . $base . '/public/index.php?route=/booking&error=' . urlencode('Movie not found.'));
                exit;
            }
            
            $venues = $this->venueRepository->findAll();
            $showtimes = [
                '10:00', '13:00', '16:00', '19:00', '22:00'
            ];
            
        } catch (Exception $e) {
            $base = $this->getBasePath();
            header('Location: ' . $base . '/public/index.php?route=/booking&error=' . urlencode('Unable to load booking information.'));
            exit;
        }
        
        $bookedSeats = [];
        $screenId = (int)($this->get('screen_id') ?? 0);
        $showDate = $this->get('show_date') ?? '';
        $showTime = $this->get('show_time') ?? '';
        
        if ($screenId > 0 && !empty($showDate) && !empty($showTime)) {
            try {
                $bookedSeatsData = $this->bookingService->getBookedSeats($screenId, $showDate, $showTime);
                foreach ($bookedSeatsData as $seat) {
                    // Ensure seat key matches the format used in JavaScript (row + number as string)
                    // Handle both old format (numeric) and new format (string like "M1")
                    $seatNumber = (string)$seat['seat_number'];
                    $seatKey = $seat['seat_row'] . $seatNumber;
                    $bookedSeats[$seatKey] = true;
                }
                error_log("Loaded " . count($bookedSeats) . " booked seats for screen $screenId, date $showDate, time $showTime");
                error_log("Booked seat keys: " . implode(', ', array_keys($bookedSeats)));
            } catch (Exception $e) {
                error_log("Error loading booked seats in controller: " . $e->getMessage());
            }
        }
        
        $this->view('front/booking/seat-selection', [
            'movie' => $movie,
            'venues' => $venues ?? [],
            'showtimes' => $showtimes,
            'csrfToken' => Csrf::generate(),
            'error' => $this->get('error'),
            'bookedSeats' => $bookedSeats,
            'screen_id' => $screenId,
            'show_date' => $showDate,
            'show_time' => $showTime
        ]);
    }
    
    public function checkout() {
        $authService = new AuthService();
        if (!$authService->isUserLoggedIn()) {
            $redirectParams = [];
            if (isset($_GET['movie_id'])) $redirectParams['movie_id'] = $_GET['movie_id'];
            if (isset($_GET['venue_id'])) $redirectParams['venue_id'] = $_GET['venue_id'];
            if (isset($_GET['screen_id'])) $redirectParams['screen_id'] = $_GET['screen_id'];
            if (isset($_GET['show_date'])) $redirectParams['show_date'] = $_GET['show_date'];
            if (isset($_GET['show_time'])) $redirectParams['show_time'] = $_GET['show_time'];
            if (isset($_GET['seats'])) $redirectParams['seats'] = $_GET['seats'];
            
            $redirectUrl = '/booking/checkout';
            if (!empty($redirectParams)) {
                $redirectUrl .= '?' . http_build_query($redirectParams);
            }
            
            $this->redirect('/login?redirect=' . urlencode($redirectUrl));
            return;
        }
        
        $movieId = (int)($this->post('movie_id') ?? $this->get('movie_id') ?? 0);
        $venueId = (int)($this->post('venue_id') ?? $this->get('venue_id') ?? 0);
        $screenId = (int)($this->post('screen_id') ?? $this->get('screen_id') ?? 0);
        $showDate = $this->post('show_date') ?? $this->get('show_date') ?? '';
        $showTime = $this->post('show_time') ?? $this->get('show_time') ?? '';
        
        // Get seats - check multiple sources
        $seatsParam = null;
        if (isset($_POST['seats']) && $_POST['seats'] !== '') {
            $seatsParam = $_POST['seats'];
        } elseif (isset($_GET['seats']) && $_GET['seats'] !== '') {
            $seatsParam = $_GET['seats'];
        } elseif ($this->get('seats') !== null && $this->get('seats') !== '') {
            $seatsParam = $this->get('seats');
        }
        
        // Parse seats
        $seats = [];
        if ($seatsParam !== null && $seatsParam !== '') {
            if (is_string($seatsParam)) {
                $seats = array_values(array_filter(array_map('trim', explode(',', $seatsParam))));
            } elseif (is_array($seatsParam)) {
                $seats = array_values(array_filter(array_map('trim', $seatsParam)));
            }
        }
        
        // Debug logging
        error_log("Checkout - movieId: $movieId, venueId: $venueId, screenId: $screenId");
        error_log("Checkout - showDate: $showDate, showTime: $showTime");
        error_log("Checkout - seatsParam: " . var_export($seatsParam, true));
        error_log("Checkout - seats array: " . var_export($seats, true));
        error_log("Checkout - \$_GET: " . print_r($_GET, true));
        
        if ($movieId <= 0 || $venueId <= 0 || $screenId <= 0 || empty($showDate) || empty($showTime) || empty($seats)) {
            $base = $this->getBasePath();
            $errorDetails = [];
            if ($movieId <= 0) $errorDetails[] = 'movie_id missing';
            if ($venueId <= 0) $errorDetails[] = 'venue_id missing';
            if ($screenId <= 0) $errorDetails[] = 'screen_id missing';
            if (empty($showDate)) $errorDetails[] = 'show_date missing';
            if (empty($showTime)) $errorDetails[] = 'show_time missing';
            if (empty($seats)) $errorDetails[] = 'seats missing';
            error_log("Checkout validation failed: " . implode(', ', $errorDetails));
            header('Location: ' . $base . '/public/index.php?route=/booking&error=' . urlencode('Missing booking information. Please start over.'));
            exit;
        }
        
        try {
            $movie = $this->movieRepository->findById($movieId);
            if (!$movie) {
                throw new Exception('Movie not found.');
            }
            
            $venues = $this->venueRepository->findAll();
            $venue = null;
            $screen = null;
            
            foreach ($venues as $v) {
                if ($v['id'] == $venueId) {
                    $venue = $v;
                    foreach ($v['screens'] as $s) {
                        if ($s['id'] == $screenId) {
                            $screen = $s;
                            break;
                        }
                    }
                    break;
                }
            }
            
            if (!$venue || !$screen) {
                throw new Exception('Venue or screen not found.');
            }
            
            $seatCount = count($seats);
            $totalPrice = $screen['base_price'] * $seatCount;
            
            $this->view('front/booking/checkout', [
                'movie' => $movie,
                'venue' => $venue,
                'screen' => $screen,
                'showDate' => $showDate,
                'showTime' => $showTime,
                'seats' => $seats,
                'totalPrice' => $totalPrice
            ]);
            
        } catch (Exception $e) {
            $base = $this->getBasePath();
            header('Location: ' . $base . '/public/index.php?route=/booking&error=' . urlencode($e->getMessage()));
            exit;
        }
    }
    
    public function process() {
        if (!$this->isPost()) {
            $base = $this->getBasePath();
            header('Location: ' . $base . '/public/index.php?route=/booking');
            exit;
        }
        
        Csrf::validate();
        
        $movieId = (int)$this->post('movie_id', 0);
        $venueId = (int)$this->post('venue_id', 0);
        $screenId = (int)$this->post('screen_id', 0);
        $showDate = $this->post('show_date', '');
        $showTime = $this->post('show_time', '');
        $seatsParam = $this->post('seats', []);
        
        // Accept seats as both string and array (from checkout form)
        if (is_string($seatsParam)) {
            $seats = array_values(array_filter(array_map('trim', explode(',', $seatsParam))));
        } else {
            $seats = is_array($seatsParam) ? array_values(array_filter(array_map('trim', $seatsParam))) : [];
        }
        
        $errors = [];
        
        if ($movieId <= 0) {
            $errors[] = 'Please select a movie';
        }
        
        if ($venueId <= 0) {
            $errors[] = 'Please select a venue';
        }
        
        if ($screenId <= 0) {
            $errors[] = 'Please select a screen';
        }
        
        if (empty($showDate)) {
            $errors[] = 'Please select a date';
        }
        
        if (empty($showTime)) {
            $errors[] = 'Please select a time';
        }
        
        if (empty($seats)) {
            $errors[] = 'Please select at least one seat';
        }
        
        if (count($seats) > 5) {
            $errors[] = 'Maximum 5 seats per booking';
        }
        
        if (!empty($errors)) {
            $base = $this->getBasePath();
            header('Location: ' . $base . '/public/index.php?route=/booking/checkout&movie_id=' . $movieId . '&venue_id=' . $venueId . '&screen_id=' . $screenId . '&show_date=' . urlencode($showDate) . '&show_time=' . urlencode($showTime) . '&error=' . urlencode(implode('. ', $errors)));
            exit;
        }
        
        $authService = new AuthService();
        if (!$authService->isUserLoggedIn()) {
            $redirectParams = [];
            if ($movieId > 0) $redirectParams['movie_id'] = $movieId;
            if ($venueId > 0) $redirectParams['venue_id'] = $venueId;
            if ($screenId > 0) $redirectParams['screen_id'] = $screenId;
            if (!empty($showDate)) $redirectParams['show_date'] = $showDate;
            if (!empty($showTime)) $redirectParams['show_time'] = $showTime;
            if (!empty($seats) && is_array($seats)) {
                $redirectParams['seats'] = implode(',', $seats);
            }
            
            $redirectUrl = '/booking/checkout';
            if (!empty($redirectParams)) {
                $redirectUrl .= '?' . http_build_query($redirectParams);
            }
            
            $this->redirect('/login?redirect=' . urlencode($redirectUrl));
            return;
        }
        
        $user = $authService->getCurrentUser();
        if (!$user || !isset($user['id'])) {
            $base = $this->getBasePath();
            header('Location: ' . $base . '/public/index.php?route=/booking&error=' . urlencode('User not found. Please login again.'));
            exit;
        }
        
        $userId = $user['id'];
        
        try {
            $parsedSeats = [];
            foreach ($seats as $seat) {
                $seat = trim($seat);
                if (empty($seat)) continue;
                
                // Match formats like "AL1", "AM1", "AR1" (with L/M/R prefix) or "A1" (numeric only)
                // Pattern: row letters (1-2 chars) + optional L/M/R + number
                // Examples: "AM1" -> row="A", number="M1"; "A1" -> row="A", number="1"; "AAM1" -> row="AA", number="M1"
                // Note: We prioritize matching L/M/R sections, so "AM1" is parsed as row="A", number="M1" not row="AM", number="1"
                if (preg_match('/^([A-Z]{1,2})([LMR]\d+)$/i', $seat, $matches)) {
                    // Format with section prefix (L/M/R): "AM1", "AL1", "AR1", "AAM1", etc.
                    $row = strtoupper($matches[1]);
                    $number = (string)$matches[2];
                } elseif (preg_match('/^([A-Z]{1,2})(\d+)$/i', $seat, $matches)) {
                    // Format without section prefix: "A1", "AA1", etc.
                    $row = strtoupper($matches[1]);
                    $number = (string)$matches[2];
                } else {
                    error_log("BookingController: Invalid seat format: '$seat'");
                    throw new Exception('Invalid seat format: ' . $seat);
                }
                
                if (isset($row) && isset($number)) {
                    $row = strtoupper($matches[1]);
                    $number = (string)$matches[2]; // Store as string to support "L1", "M1", "R1" formats
                    
                    error_log("BookingController: Parsed seat '$seat' -> row='$row', number='$number', full key='$row$number'");
                    
                    $parsedSeats[] = [
                        'row' => $row,
                        'number' => $number,
                        'wheelchair' => false
                    ];
                }
            }
            
            if (empty($parsedSeats)) {
                throw new Exception('Please select at least one valid seat.');
            }
            
            $screen = $this->venueRepository->getScreens($venueId);
            $screenData = null;
            foreach ($screen as $s) {
                if ($s['id'] == $screenId) {
                    $screenData = $s;
                    break;
                }
            }
            
            if (!$screenData) {
                throw new Exception('Screen not found.');
            }
            
            $seatCount = count($parsedSeats);
            $totalPrice = $screenData['base_price'] * $seatCount;
            
            // Ensure show_time is in HH:MM:SS format for database
            $showTimeFormatted = $showTime;
            if (preg_match('/^(\d{1,2}):(\d{2})$/', $showTime, $matches)) {
                // Convert "10:00" to "10:00:00"
                $showTimeFormatted = $matches[1] . ':' . $matches[2] . ':00';
            }
            
            error_log("Creating booking - userId: $userId, movieId: $movieId, seats: " . count($parsedSeats));
            
            $bookingId = $this->bookingService->createBooking([
                'user_id' => $userId,
                'movie_id' => $movieId,
                'venue_id' => $venueId,
                'screen_id' => $screenId,
                'show_date' => $showDate,
                'show_time' => $showTimeFormatted,
                'seats_count' => $seatCount,
                'total_price' => $totalPrice
            ], $parsedSeats);
            
            if ($bookingId && $bookingId > 0) {
                $booking = $this->bookingRepository->findById($bookingId);
                if ($booking) {
                    $this->view('front/booking/confirmation', [
                        'booking' => $booking,
                        'bookingId' => $bookingId,
                        'success' => 'Booking confirmed! You will receive a confirmation email shortly.'
                    ]);
                    return;
                } else {
                    error_log("Booking created with ID $bookingId but could not be retrieved from database");
                }
            } else {
                error_log("Booking creation returned invalid ID: " . var_export($bookingId, true));
            }
            
            $base = $this->getBasePath();
            header('Location: ' . $base . '/public/index.php?route=/booking&movie_id=' . $movieId . '&error=' . urlencode('Failed to create booking. Please try again.'));
            exit;
            
        } catch (Exception $e) {
            error_log("Booking error: " . $e->getMessage());
            error_log("Booking error trace: " . $e->getTraceAsString());
            error_log("Booking error file: " . $e->getFile() . " line " . $e->getLine());
            $base = $this->getBasePath();
            $errorMsg = APP_DEBUG ? $e->getMessage() : 'An error occurred while processing your booking. Please try again.';
            header('Location: ' . $base . '/public/index.php?route=/booking/checkout&movie_id=' . $movieId . '&venue_id=' . $venueId . '&screen_id=' . $screenId . '&show_date=' . urlencode($showDate) . '&show_time=' . urlencode($showTime) . '&error=' . urlencode($errorMsg));
            exit;
        } catch (Error $e) {
            error_log("Booking fatal error: " . $e->getMessage());
            error_log("Booking fatal error trace: " . $e->getTraceAsString());
            $base = $this->getBasePath();
            $errorMsg = APP_DEBUG ? $e->getMessage() : 'A system error occurred. Please contact support.';
            header('Location: ' . $base . '/public/index.php?route=/booking&error=' . urlencode($errorMsg));
            exit;
        }
    }
    
    public function getBookedSeats() {
        header('Content-Type: application/json');
        
        $screenId = (int)($this->get('screen_id') ?? 0);
        $showDate = $this->get('show_date') ?? '';
        $showTime = $this->get('show_time') ?? '';
        
        if ($screenId <= 0 || empty($showDate) || empty($showTime)) {
            echo json_encode(['bookedSeats' => []]);
            exit;
        }
        
        try {
            $bookedSeatsData = $this->bookingService->getBookedSeats($screenId, $showDate, $showTime);
            $bookedSeats = [];
            foreach ($bookedSeatsData as $seat) {
                // Ensure seat key matches the format used in JavaScript (row + number as string)
                // Handle both old format (numeric) and new format (string like "M1")
                $seatNumber = (string)$seat['seat_number'];
                $seatKey = $seat['seat_row'] . $seatNumber;
                $bookedSeats[$seatKey] = true;
            }
            error_log("AJAX: Loaded " . count($bookedSeats) . " booked seats for screen $screenId, date $showDate, time $showTime");
            error_log("AJAX: Booked seat keys: " . implode(', ', array_keys($bookedSeats)));
            echo json_encode(['bookedSeats' => $bookedSeats]);
        } catch (Exception $e) {
            error_log("Error loading booked seats: " . $e->getMessage());
            echo json_encode(['bookedSeats' => [], 'error' => $e->getMessage()]);
        }
        exit;
    }
}

