<?php

class ProfileController extends Controller {
    private $authService;
    private $bookingRepository;
    
    public function __construct() {
        parent::__construct();
        $this->authService = new AuthService();
        $this->bookingRepository = new BookingRepository();
    }
    
    public function index() {
        if (!$this->authService->isUserLoggedIn()) {
            $this->redirect('/login?redirect=' . urlencode('/profile'));
            return;
        }
        
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            $this->redirect('/login');
            return;
        }
        
        try {
            $bookings = $this->bookingRepository->findByUserId($user['id']);
        } catch (Exception $e) {
            $bookings = [];
            $error = "Unable to load bookings.";
        }
        
        $this->view('front/profile/index', [
            'user' => $user,
            'bookings' => $bookings,
            'error' => $error ?? null
        ]);
    }
}

