<?php
/**
 * News Controller
 */

class NewsController extends Controller {
    private $newsRepository;
    
    public function __construct() {
        parent::__construct();
        $this->newsRepository = new NewsRepository();
    }
    
    public function index() {
        try {
            $news = $this->newsRepository->findAll();
        } catch (Exception $e) {
            $news = [];
            $error = "Unable to load news. Please try again later.";
        }
        
        $this->view('front/news/index', [
            'news' => $news,
            'error' => $error ?? null
        ]);
    }
}

