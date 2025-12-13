<?php

class CompanySettingsController extends Controller {
    private $companySettingsRepository;
    
    public function __construct() {
        parent::__construct();
        $this->companySettingsRepository = new CompanySettingsRepository();
    }
    
    public function index() {
        try {
            $settings = $this->companySettingsRepository->find();
            if (!$settings) {
                $settings = [
                    'id' => null,
                    'title' => 'Welcome to CinemaBook',
                    'description' => '',
                    'features' => [],
                    'opening_hours' => ''
                ];
            }
        } catch (Exception $e) {
            $settings = [
                'id' => null,
                'title' => 'Welcome to CinemaBook',
                'description' => '',
                'features' => [],
                'opening_hours' => ''
            ];
            $error = "Unable to load settings.";
        }
        
        $this->view('admin/company-settings/index', [
            'settings' => $settings,
            'error' => $error ?? null,
            'currentPage' => 'settings'
        ]);
    }
    
    public function update() {
        if (!$this->isPost()) {
            $this->redirect('/admin/settings');
        }
        
        Csrf::validate();
        
        $id = (int)$this->post('id', 0);
        $title = trim($this->post('title', ''));
        $description = trim($this->post('description', ''));
        $featuresInput = trim($this->post('features', ''));
        $openingHours = trim($this->post('opening_hours', ''));
        
        $features = [];
        if (!empty($featuresInput)) {
            $features = array_map('trim', explode(',', $featuresInput));
            $features = array_filter($features);
        }
        
        if (empty($title)) {
            $this->redirect('/admin/settings?error=' . urlencode('Title is required'));
            return;
        }
        
        if (empty($description)) {
            $this->redirect('/admin/settings?error=' . urlencode('Description is required'));
            return;
        }
        
        try {
            $data = [
                'title' => $title,
                'description' => $description,
                'features' => $features,
                'opening_hours' => $openingHours
            ];
            
            if ($id > 0) {
                $this->companySettingsRepository->update($id, $data);
            } else {
                $this->companySettingsRepository->create($data);
            }
            
            $this->redirect('/admin/settings?success=' . urlencode('Settings updated successfully'));
        } catch (Exception $e) {
            $this->redirect('/admin/settings?error=' . urlencode($e->getMessage()));
        }
    }
}

