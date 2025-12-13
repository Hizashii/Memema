<?php
/**
 * Admin Dashboard Controller
 */

class DashboardController extends Controller {
    private $movieRepository;
    private $newsRepository;
    private $venueRepository;
    
    public function __construct() {
        parent::__construct();
        $this->movieRepository = new MovieRepository();
        $this->newsRepository = new NewsRepository();
        $this->venueRepository = new VenueRepository();
    }
    
    public function index() {
        try {
            $stats = [
                'movies' => count($this->movieRepository->findAll()),
                'news' => count($this->newsRepository->findAll()),
                'venues' => count($this->venueRepository->findAll())
            ];
        } catch (Exception $e) {
            $stats = ['movies' => 0, 'news' => 0, 'venues' => 0];
        }
        
        $this->view('admin/dashboard/index', [
            'stats' => $stats
        ]);
    }
}

