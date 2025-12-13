<?php

class ContactInfoController extends Controller {
    private $contactInfoRepository;
    
    public function __construct() {
        parent::__construct();
        $this->contactInfoRepository = new ContactInfoRepository();
    }
    
    public function index() {
        try {
            $contactInfo = $this->contactInfoRepository->find();
            if (!$contactInfo) {
                $contactInfo = [
                    'id' => null,
                    'phone' => '',
                    'email' => '',
                    'address' => ''
                ];
            }
        } catch (Exception $e) {
            $contactInfo = [
                'id' => null,
                'phone' => '',
                'email' => '',
                'address' => ''
            ];
            $error = "Unable to load contact information.";
        }
        
        $this->view('admin/contact-info/index', [
            'contactInfo' => $contactInfo,
            'error' => $error ?? null,
            'currentPage' => 'contact-info'
        ]);
    }
    
    public function update() {
        if (!$this->isPost()) {
            $this->redirect('/admin/contact-info');
        }
        
        Csrf::validate();
        
        $id = (int)$this->post('id', 0);
        $phone = trim($this->post('phone', ''));
        $email = trim($this->post('email', ''));
        $address = trim($this->post('address', ''));
        
        if (empty($phone)) {
            $this->redirect('/admin/contact-info?error=' . urlencode('Phone is required'));
            return;
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirect('/admin/contact-info?error=' . urlencode('Valid email is required'));
            return;
        }
        
        if (empty($address)) {
            $this->redirect('/admin/contact-info?error=' . urlencode('Address is required'));
            return;
        }
        
        try {
            $data = [
                'phone' => $phone,
                'email' => $email,
                'address' => $address
            ];
            
            if ($id > 0) {
                $this->contactInfoRepository->update($id, $data);
            } else {
                $this->contactInfoRepository->create($data);
            }
            
            $this->redirect('/admin/contact-info?success=' . urlencode('Contact information updated successfully'));
        } catch (Exception $e) {
            $this->redirect('/admin/contact-info?error=' . urlencode($e->getMessage()));
        }
    }
}

