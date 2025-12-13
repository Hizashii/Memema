<?php
/**
 * Movies Controller
 */

class MoviesController extends Controller {
    private $movieRepository;
    
    public function __construct() {
        parent::__construct();
        $this->movieRepository = new MovieRepository();
    }
    
    public function index() {
        try {
            $movies = $this->movieRepository->findAll();
        } catch (Exception $e) {
            $movies = [];
            $error = "Unable to load movies. Please try again later.";
        }
        
        $this->view('front/movies/index', [
            'movies' => $movies,
            'error' => $error ?? null
        ]);
    }
}

