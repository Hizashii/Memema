<?php

class ShowsController extends Controller {
    private $showRepository;
    private $imageService;
    
    public function __construct() {
        parent::__construct();
        $this->showRepository = new ShowRepository();
        $this->imageService = new ImageService();
    }
    
    public function index() {
        try {
            $shows = $this->showRepository->findAll();
        } catch (Exception $e) {
            $shows = [];
            $error = "Unable to load shows.";
        }
        
        $this->view('admin/shows/index', [
            'shows' => $shows,
            'error' => $error ?? null,
            'currentPage' => 'shows'
        ]);
    }
    
    public function create() {
        $this->view('admin/shows/form', [
            'show' => null,
            'currentPage' => 'shows'
        ]);
    }
    
    public function store() {
        if (!$this->isPost()) {
            $this->redirect('/admin/shows');
        }
        
        Csrf::validate();
        
        $imagePath = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            try {
                $uploadResult = $this->imageService->uploadImage($_FILES['image']);
                $imagePath = $uploadResult['relative_path'];
            } catch (Exception $e) {
                $this->redirect('/admin/shows/create?error=' . urlencode('Image upload failed: ' . $e->getMessage()));
                return;
            }
        } else {
            $this->redirect('/admin/shows/create?error=' . urlencode('Image is required'));
            return;
        }
        
        $showtimesInput = trim($this->post('showtimes', ''));
        $showtimes = [];
        if (!empty($showtimesInput)) {
            $showtimes = array_map('trim', explode(',', $showtimesInput));
            $showtimes = array_filter($showtimes);
        }
        
        $data = [
            'title' => trim($this->post('title', '')),
            'img' => $imagePath,
            'tag_text' => trim($this->post('tag_text', '')),
            'tag_color' => trim($this->post('tag_color', '#FF0000')),
            'showtimes' => $showtimes
        ];
        
        if (empty($data['title'])) {
            $this->redirect('/admin/shows/create?error=' . urlencode('Title is required'));
            return;
        }
        
        try {
            $id = $this->showRepository->create($data);
            $this->redirect('/admin/shows?success=' . urlencode('Show created successfully'));
        } catch (Exception $e) {
            if (!empty($imagePath)) {
                $this->imageService->deleteImage($imagePath);
            }
            $this->redirect('/admin/shows/create?error=' . urlencode($e->getMessage()));
        }
    }
    
    public function edit() {
        $id = (int)$this->get('id', 0);
        if ($id <= 0) {
            $this->redirect('/admin/shows');
        }
        
        $show = $this->showRepository->findById($id);
        if (!$show) {
            $this->redirect('/admin/shows?error=' . urlencode('Show not found'));
        }
        
        $this->view('admin/shows/form', [
            'show' => $show,
            'currentPage' => 'shows'
        ]);
    }
    
    public function update() {
        if (!$this->isPost()) {
            $this->redirect('/admin/shows');
        }
        
        Csrf::validate();
        
        $id = (int)$this->post('id', 0);
        if ($id <= 0) {
            $this->redirect('/admin/shows');
        }
        
        $currentShow = $this->showRepository->findById($id);
        $currentImage = $currentShow['img'] ?? '';
        
        $imagePath = $currentImage;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            try {
                $uploadResult = $this->imageService->uploadImage($_FILES['image']);
                $imagePath = $uploadResult['relative_path'];
                
                if (!empty($currentImage) && $currentImage !== $imagePath) {
                    $this->imageService->deleteImage($currentImage);
                }
            } catch (Exception $e) {
                $this->redirect('/admin/shows/edit?id=' . $id . '&error=' . urlencode('Image upload failed: ' . $e->getMessage()));
                return;
            }
        }
        
        $showtimesInput = trim($this->post('showtimes', ''));
        $showtimes = [];
        if (!empty($showtimesInput)) {
            $showtimes = array_map('trim', explode(',', $showtimesInput));
            $showtimes = array_filter($showtimes);
        }
        
        $data = [
            'title' => trim($this->post('title', '')),
            'img' => $imagePath,
            'tag_text' => trim($this->post('tag_text', '')),
            'tag_color' => trim($this->post('tag_color', '#FF0000')),
            'showtimes' => $showtimes
        ];
        
        if (empty($data['title'])) {
            $this->redirect('/admin/shows/edit?id=' . $id . '&error=' . urlencode('Title is required'));
            return;
        }
        
        try {
            $this->showRepository->update($id, $data);
            $this->redirect('/admin/shows?success=' . urlencode('Show updated successfully'));
        } catch (Exception $e) {
            if (!empty($imagePath) && $imagePath !== $currentImage) {
                $this->imageService->deleteImage($imagePath);
            }
            $this->redirect('/admin/shows/edit?id=' . $id . '&error=' . urlencode($e->getMessage()));
        }
    }
    
    public function delete() {
        $id = (int)$this->get('id', 0);
        if ($id <= 0) {
            $this->redirect('/admin/shows');
        }
        
        try {
            $show = $this->showRepository->findById($id);
            if ($show && !empty($show['img'])) {
                $this->imageService->deleteImage($show['img']);
            }
            
            $this->showRepository->delete($id);
            $this->redirect('/admin/shows?success=' . urlencode('Show deleted successfully'));
        } catch (Exception $e) {
            $this->redirect('/admin/shows?error=' . urlencode($e->getMessage()));
        }
    }
}

