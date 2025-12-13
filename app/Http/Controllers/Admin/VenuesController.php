<?php
/**
 * Admin Venues Controller
 */

class VenuesController extends Controller {
    private $venueRepository;
    private $imageService;
    
    public function __construct() {
        parent::__construct();
        $this->venueRepository = new VenueRepository();
        $this->imageService = new ImageService();
    }
    
    public function index() {
        try {
            $venues = $this->venueRepository->findAll();
        } catch (Exception $e) {
            $venues = [];
            $error = "Unable to load venues.";
        }
        
        $this->view('admin/venues/index', [
            'venues' => $venues,
            'error' => $error ?? null,
            'success' => $this->get('success'),
            'currentPage' => 'venues'
        ]);
    }
    
    public function create() {
        $this->view('admin/venues/form', [
            'venue' => null,
            'currentPage' => 'venues'
        ]);
    }
    
    public function store() {
        if (!$this->isPost()) {
            $this->redirect('/admin/venues');
        }
        
        Csrf::validate();
        
        $imagePath = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            try {
                $uploadResult = $this->imageService->uploadImage($_FILES['image']);
                $imagePath = $uploadResult['relative_path'];
            } catch (Exception $e) {
                $this->redirect('/admin/venues/create?error=' . urlencode('Image upload failed: ' . $e->getMessage()));
                return;
            }
        }
        
        $data = [
            'name' => trim($this->post('name', '')),
            'address' => trim($this->post('address', '')),
            'phone' => trim($this->post('phone', '')),
            'image' => $imagePath
        ];
        
        if (empty($data['name'])) {
            $this->redirect('/admin/venues/create?error=' . urlencode('Name is required'));
        }
        
        try {
            $this->venueRepository->create($data);
            $this->redirect('/admin/venues?success=' . urlencode('Venue created successfully'));
        } catch (Exception $e) {
            // Delete uploaded image if creation failed
            if (!empty($imagePath)) {
                $this->imageService->deleteImage($imagePath);
            }
            $this->redirect('/admin/venues/create?error=' . urlencode($e->getMessage()));
        }
    }
    
    public function edit() {
        $id = (int)$this->get('id', 0);
        if ($id <= 0) {
            $this->redirect('/admin/venues');
        }
        
        $venue = $this->venueRepository->findById($id);
        if (!$venue) {
            $this->redirect('/admin/venues?error=' . urlencode('Venue not found'));
        }
        
        $this->view('admin/venues/form', [
            'venue' => $venue,
            'currentPage' => 'venues'
        ]);
    }
    
    public function update() {
        if (!$this->isPost()) {
            $this->redirect('/admin/venues');
        }
        
        Csrf::validate();
        
        $id = (int)$this->post('id', 0);
        if ($id <= 0) {
            $this->redirect('/admin/venues');
        }
        
        $currentVenue = $this->venueRepository->findById($id);
        $currentImage = $currentVenue['image'] ?? '';
        
        $imagePath = $currentImage;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            try {
                $uploadResult = $this->imageService->uploadImage($_FILES['image']);
                $imagePath = $uploadResult['relative_path'];
                
                if (!empty($currentImage) && $currentImage !== $imagePath) {
                    $this->imageService->deleteImage($currentImage);
                }
            } catch (Exception $e) {
                $this->redirect('/admin/venues/edit?id=' . $id . '&error=' . urlencode('Image upload failed: ' . $e->getMessage()));
                return;
            }
        }
        
        $data = [
            'name' => trim($this->post('name', '')),
            'address' => trim($this->post('address', '')),
            'phone' => trim($this->post('phone', '')),
            'image' => $imagePath
        ];
        
        if (empty($data['name'])) {
            $this->redirect('/admin/venues/edit?id=' . $id . '&error=' . urlencode('Name is required'));
        }
        
        try {
            $this->venueRepository->update($id, $data);
            $this->redirect('/admin/venues?success=' . urlencode('Venue updated successfully'));
        } catch (Exception $e) {
            if (!empty($imagePath) && $imagePath !== $currentImage) {
                $this->imageService->deleteImage($imagePath);
            }
            $this->redirect('/admin/venues/edit?id=' . $id . '&error=' . urlencode($e->getMessage()));
        }
    }
    
    public function delete() {
        $id = (int)$this->get('id', 0);
        if ($id <= 0) {
            $this->redirect('/admin/venues');
        }
        
        try {
            $venue = $this->venueRepository->findById($id);
            if ($venue && !empty($venue['image'])) {
                $this->imageService->deleteImage($venue['image']);
            }
            
            $this->venueRepository->delete($id);
            $this->redirect('/admin/venues?success=' . urlencode('Venue deleted successfully'));
        } catch (Exception $e) {
            $this->redirect('/admin/venues?error=' . urlencode($e->getMessage()));
        }
    }
}

