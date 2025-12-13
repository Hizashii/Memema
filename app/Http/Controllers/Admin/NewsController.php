<?php
/**
 * Admin News Controller
 */

class NewsController extends Controller {
    private $newsRepository;
    private $imageService;
    
    public function __construct() {
        parent::__construct();
        $this->newsRepository = new NewsRepository();
        $this->imageService = new ImageService();
    }
    
    public function index() {
        try {
            $news = $this->newsRepository->findAll();
        } catch (Exception $e) {
            $news = [];
            $error = "Unable to load news.";
        }
        
        $this->view('admin/news/index', [
            'news' => $news,
            'error' => $error ?? null,
            'success' => $this->get('success'),
            'currentPage' => 'news'
        ]);
    }
    
    public function create() {
        $this->view('admin/news/form', [
            'news' => null,
            'currentPage' => 'news'
        ]);
    }
    
    public function store() {
        if (!$this->isPost()) {
            $this->redirect('/admin/news');
        }
        
        Csrf::validate();
        
        $imagePath = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            try {
                $uploadResult = $this->imageService->uploadImage($_FILES['image']);
                $imagePath = $uploadResult['relative_path'];
            } catch (Exception $e) {
                $this->redirect('/admin/news/create?error=' . urlencode('Image upload failed: ' . $e->getMessage()));
                return;
            }
        } else {
            $this->redirect('/admin/news/create?error=' . urlencode('Image is required'));
            return;
        }
        
        $data = [
            'title' => trim($this->post('title', '')),
            'img' => $imagePath,
            'excerpt' => trim($this->post('excerpt', '')),
            'url' => trim($this->post('url', ''))
        ];
        
        if (empty($data['title'])) {
            $this->redirect('/admin/news/create?error=' . urlencode('Title is required'));
        }
        
        try {
            $this->newsRepository->create($data);
            $this->redirect('/admin/news?success=' . urlencode('News created successfully'));
        } catch (Exception $e) {
            // Delete uploaded image if creation failed
            if (!empty($imagePath)) {
                $this->imageService->deleteImage($imagePath);
            }
            $this->redirect('/admin/news/create?error=' . urlencode($e->getMessage()));
        }
    }
    
    public function edit() {
        $id = (int)$this->get('id', 0);
        if ($id <= 0) {
            $this->redirect('/admin/news');
        }
        
        $news = $this->newsRepository->findById($id);
        if (!$news) {
            $this->redirect('/admin/news?error=' . urlencode('News not found'));
        }
        
        $this->view('admin/news/form', [
            'news' => $news,
            'currentPage' => 'news'
        ]);
    }
    
    public function update() {
        if (!$this->isPost()) {
            $this->redirect('/admin/news');
        }
        
        Csrf::validate();
        
        $id = (int)$this->post('id', 0);
        if ($id <= 0) {
            $this->redirect('/admin/news');
        }
        
        $currentNews = $this->newsRepository->findById($id);
        $currentImage = $currentNews['img'] ?? '';
        
        $imagePath = $currentImage;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            try {
                $uploadResult = $this->imageService->uploadImage($_FILES['image']);
                $imagePath = $uploadResult['relative_path'];
                
                if (!empty($currentImage) && $currentImage !== $imagePath) {
                    $this->imageService->deleteImage($currentImage);
                }
            } catch (Exception $e) {
                $this->redirect('/admin/news/edit?id=' . $id . '&error=' . urlencode('Image upload failed: ' . $e->getMessage()));
                return;
            }
        }
        
        $data = [
            'title' => trim($this->post('title', '')),
            'img' => $imagePath,
            'excerpt' => trim($this->post('excerpt', '')),
            'url' => trim($this->post('url', ''))
        ];
        
        if (empty($data['title'])) {
            $this->redirect('/admin/news/edit?id=' . $id . '&error=' . urlencode('Title is required'));
        }
        
        try {
            $this->newsRepository->update($id, $data);
            $this->redirect('/admin/news?success=' . urlencode('News updated successfully'));
        } catch (Exception $e) {
            if (!empty($imagePath) && $imagePath !== $currentImage) {
                $this->imageService->deleteImage($imagePath);
            }
            $this->redirect('/admin/news/edit?id=' . $id . '&error=' . urlencode($e->getMessage()));
        }
    }
    
    public function delete() {
        $id = (int)$this->get('id', 0);
        if ($id <= 0) {
            $this->redirect('/admin/news');
        }
        
        try {
            $news = $this->newsRepository->findById($id);
            if ($news && !empty($news['img'])) {
                $this->imageService->deleteImage($news['img']);
            }
            
            $this->newsRepository->delete($id);
            $this->redirect('/admin/news?success=' . urlencode('News deleted successfully'));
        } catch (Exception $e) {
            $this->redirect('/admin/news?error=' . urlencode($e->getMessage()));
        }
    }
}

