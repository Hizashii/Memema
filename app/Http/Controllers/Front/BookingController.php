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
                    $bookedSeats[$seat['seat_row'] . $seat['seat_number']] = true;
                }
            } catch (Exception $e) {
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
        $seatsParam = $this->post('seats') ?? $this->get('seats') ?? [];
        
        if (is_string($seatsParam)) {
            $seats = !empty($seatsParam) ? explode(',', $seatsParam) : [];
        } else {
            $seats = is_array($seatsParam) ? $seatsParam : [];
        }
        
        if ($movieId <= 0 || $venueId <= 0 || $screenId <= 0 || empty($showDate) || empty($showTime) || empty($seats)) {
            $base = $this->getBasePath();
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
        $seats = $this->post('seats', []);
        
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
        
        if (empty($seats) || !is_array($seats)) {
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
                
                if (preg_match('/^([A-Z]+)([LMR]?\d+)$/i', $seat, $matches)) {
                    $parsedSeats[] = [
                        'row' => strtoupper($matches[1]),
                        'number' => $matches[2],
                        'wheelchair' => false
                    ];
                } elseif (preg_match('/^([A-Z]+)(\d+)$/i', $seat, $matches)) {
                    $parsedSeats[] = [
                        'row' => strtoupper($matches[1]),
                        'number' => (int)$matches[2],
                        'wheelchair' => false
                    ];
                } else {
                    throw new Exception('Invalid seat format: ' . $seat);
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
            
            $bookingId = $this->bookingService->createBooking([
                'user_id' => $userId,
                'movie_id' => $movieId,
                'venue_id' => $venueId,
                'screen_id' => $screenId,
                'show_date' => $showDate,
                'show_time' => $showTime,
                'seats_count' => $seatCount,
                'total_price' => $totalPrice
            ], $parsedSeats);
            
            if ($bookingId) {
                $booking = $this->bookingRepository->findById($bookingId);
                if ($booking) {
                    $this->view('front/booking/confirmation', [
                        'booking' => $booking,
                        'bookingId' => $bookingId,
                        'success' => 'Booking confirmed! You will receive a confirmation email shortly.'
                    ]);
                    return;
                }
            }
            
            $base = $this->getBasePath();
            header('Location: ' . $base . '/public/index.php?route=/booking&movie_id=' . $movieId . '&error=' . urlencode('Failed to create booking. Please try again.'));
            exit;
            
        } catch (Exception $e) {
            error_log("Booking error: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
            $base = $this->getBasePath();
            header('Location: ' . $base . '/public/index.php?route=/booking/checkout&movie_id=' . $movieId . '&error=' . urlencode($e->getMessage()));
            exit;
        }
    }
}

