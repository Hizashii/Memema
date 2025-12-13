<?php
/**
 * Admin Movies Controller
 */

class MoviesController extends Controller {
    private $movieRepository;
    private $imageService;
    
    public function __construct() {
        parent::__construct();
        $this->movieRepository = new MovieRepository();
        $this->imageService = new ImageService();
    }
    
    public function index() {
        try {
            $movies = $this->movieRepository->findAll();
        } catch (Exception $e) {
            $movies = [];
            $error = "Unable to load movies.";
        }
        
        $this->view('admin/movies/index', [
            'movies' => $movies,
            'error' => $error ?? null,
            'currentPage' => 'movies'
        ]);
    }
    
    public function create() {
        $this->view('admin/movies/form', [
            'movie' => null,
            'currentPage' => 'movies'
        ]);
    }
    
    public function store() {
        if (!$this->isPost()) {
            $this->redirect('/admin/movies');
        }
        
        Csrf::validate();
        
        $imagePath = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            try {
                $uploadResult = $this->imageService->uploadImage($_FILES['image']);
                $imagePath = $uploadResult['relative_path'];
            } catch (Exception $e) {
                $this->redirect('/admin/movies/create?error=' . urlencode('Image upload failed: ' . $e->getMessage()));
                return;
            }
        } else {
            $this->redirect('/admin/movies/create?error=' . urlencode('Image is required'));
            return;
        }
        
        $genresInput = trim($this->post('genres', ''));
        $genres = [];
        if (!empty($genresInput)) {
            $genres = array_map('trim', explode(',', $genresInput));
            $genres = array_filter($genres);
        }
        
        $data = [
            'title' => trim($this->post('title', '')),
            'img' => $imagePath,
            'duration_minutes' => (int)$this->post('duration_minutes', 0),
            'rating' => (float)$this->post('rating', 0),
            'genres' => $genres
        ];
        
        if (empty($data['title'])) {
            $this->redirect('/admin/movies/create?error=' . urlencode('Title is required'));
        }
        
        try {
            $id = $this->movieRepository->create($data);
            $this->redirect('/admin/movies?success=' . urlencode('Movie created successfully'));
        } catch (Exception $e) {
            if (!empty($imagePath)) {
                $this->imageService->deleteImage($imagePath);
            }
            $this->redirect('/admin/movies/create?error=' . urlencode($e->getMessage()));
        }
    }
    
    public function edit() {
        $id = (int)$this->get('id', 0);
        if ($id <= 0) {
            $this->redirect('/admin/movies');
        }
        
        $movie = $this->movieRepository->findById($id);
        if (!$movie) {
            $this->redirect('/admin/movies?error=' . urlencode('Movie not found'));
        }
        
        $this->view('admin/movies/form', [
            'movie' => $movie,
            'currentPage' => 'movies'
        ]);
    }
    
    public function update() {
        if (!$this->isPost()) {
            $this->redirect('/admin/movies');
        }
        
        Csrf::validate();
        
        $id = (int)$this->post('id', 0);
        if ($id <= 0) {
            $this->redirect('/admin/movies');
        }
        
        $currentMovie = $this->movieRepository->findById($id);
        $currentImage = $currentMovie['img'] ?? '';
        
        $imagePath = $currentImage;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            try {
                $uploadResult = $this->imageService->uploadImage($_FILES['image']);
                $imagePath = $uploadResult['relative_path'];
                
                if (!empty($currentImage) && $currentImage !== $imagePath) {
                    $this->imageService->deleteImage($currentImage);
                }
            } catch (Exception $e) {
                $this->redirect('/admin/movies/edit?id=' . $id . '&error=' . urlencode('Image upload failed: ' . $e->getMessage()));
                return;
            }
        }
        
        $genresInput = trim($this->post('genres', ''));
        $genres = [];
        if (!empty($genresInput)) {
            $genres = array_map('trim', explode(',', $genresInput));
            $genres = array_filter($genres);
        }
        
        $data = [
            'title' => trim($this->post('title', '')),
            'img' => $imagePath,
            'duration_minutes' => (int)$this->post('duration_minutes', 0),
            'rating' => (float)$this->post('rating', 0),
            'genres' => $genres
        ];
        
        if (empty($data['title'])) {
            $this->redirect('/admin/movies/edit?id=' . $id . '&error=' . urlencode('Title is required'));
        }
        
        try {
            $this->movieRepository->update($id, $data);
            $this->redirect('/admin/movies?success=' . urlencode('Movie updated successfully'));
        } catch (Exception $e) {
            if (!empty($imagePath) && $imagePath !== $currentImage) {
                $this->imageService->deleteImage($imagePath);
            }
            $this->redirect('/admin/movies/edit?id=' . $id . '&error=' . urlencode($e->getMessage()));
        }
    }
    
    public function delete() {
        $id = (int)$this->get('id', 0);
        if ($id <= 0) {
            $this->redirect('/admin/movies');
        }
        
        try {
            $movie = $this->movieRepository->findById($id);
            if ($movie && !empty($movie['img'])) {
                $this->imageService->deleteImage($movie['img']);
            }
            
            $this->movieRepository->delete($id);
            $this->redirect('/admin/movies?success=' . urlencode('Movie deleted successfully'));
        } catch (Exception $e) {
            $this->redirect('/admin/movies?error=' . urlencode($e->getMessage()));
        }
    }
    
}

