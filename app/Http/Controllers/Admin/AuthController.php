<?php
/**
 * Admin Auth Controller
 */

class AuthController extends Controller {
    private $authService;
    
    public function __construct() {
        parent::__construct();
        $this->authService = new AuthService();
    }
    
    public function loginForm() {
        if ($this->authService->isAdminLoggedIn()) {
            $this->redirect('/admin');
            return;
        }
        
        $error = $this->get('error');
        $this->view('admin/auth/login', [
            'error' => $error
        ]);
    }
    
    public function login() {
        if (!$this->isPost()) {
            $this->redirect('/admin/login');
            return;
        }
        
        Csrf::validate();
        
        $username = trim($this->post('username', ''));
        $password = $this->post('password', '');
        
        if (empty($username) || empty($password)) {
            $this->redirect('/admin/login?error=' . urlencode('Please fill in all fields'));
            return;
        }
        
        if ($this->authService->authenticateAdmin($username, $password)) {
            $this->redirect('/admin');
        } else {
            $this->redirect('/admin/login?error=' . urlencode('Invalid credentials'));
        }
    }
    
    public function logout() {
        $this->authService->logoutAdmin();
        $this->redirect('/admin/login');
    }
}

