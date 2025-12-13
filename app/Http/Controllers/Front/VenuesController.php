<?php
/**
 * Venues Controller
 */

class VenuesController extends Controller {
    private $venueRepository;
    
    public function __construct() {
        parent::__construct();
        $this->venueRepository = new VenueRepository();
    }
    
    public function index() {
        try {
            $venues = $this->venueRepository->findAll();
        } catch (Exception $e) {
            $venues = [];
            $error = "Unable to load venues. Please try again later.";
        }
        
        $this->view('front/venues/index', [
            'venues' => $venues,
            'error' => $error ?? null
        ]);
    }
}

