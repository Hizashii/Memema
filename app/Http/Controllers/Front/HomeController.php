<?php

class HomeController extends Controller {
    private $movieRepository;
    private $newsRepository;
    private $showRepository;
    private $companySettingsRepository;
    
    public function __construct() {
        parent::__construct();
        $this->movieRepository = new MovieRepository();
        $this->newsRepository = new NewsRepository();
        $this->showRepository = new ShowRepository();
        $this->companySettingsRepository = new CompanySettingsRepository();
    }
    
    public function index() {
        try {
            $news = $this->newsRepository->findAll(3);
            $movies = $this->movieRepository->findAll();
            $movies = array_slice($movies, 0, 8);
            
            $shows = $this->showRepository->findAll();
            $shows = array_slice($shows, 0, 6);
            
            $companySettings = $this->companySettingsRepository->find();
            if ($companySettings) {
                $companyPresentation = [
                    'title' => $companySettings['title'],
                    'description' => $companySettings['description'],
                    'features' => $companySettings['features'] ?? [],
                    'opening_hours' => $companySettings['opening_hours'] ?? ''
                ];
            } else {
                $companyPresentation = [
                    'title' => 'Welcome to CinemaBook',
                    'description' => 'Your ultimate destination for movie experiences. Book tickets, discover new films, and enjoy the magic of cinema.',
                    'features' => [
                        'Premium viewing experience',
                        'Comfortable seating',
                        'Latest blockbusters',
                        'Easy online booking'
                    ],
                    'opening_hours' => ''
                ];
            }
            
        } catch (Exception $e) {
            $news = [];
            $movies = [];
            $shows = [];
            $companyPresentation = [];
            $error = "Unable to load content. Please try again later.";
        }
        
        $this->view('front/home/index', [
            'news' => $news,
            'movies' => $movies,
            'shows' => $shows ?? [],
            'companyPresentation' => $companyPresentation,
            'error' => $error ?? null
        ]);
    }
}

