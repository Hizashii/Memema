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
            $base = $this->getBasePath();
            header('Location: ' . $base . '/public/index.php?route=/admin');
            exit;
        }
        
        $error = $this->get('error');
        $this->view('admin/auth/login', [
            'error' => $error
        ]);
    }
    
    public function login() {
        if (!$this->isPost()) {
            $base = $this->getBasePath();
            header('Location: ' . $base . '/public/index.php?route=/admin/login');
            exit;
        }
        
        Csrf::validate();
        
        $username = trim($this->post('username', ''));
        $password = $this->post('password', '');
        
        if (empty($username) || empty($password)) {
            $base = $this->getBasePath();
            header('Location: ' . $base . '/public/index.php?route=/admin/login&error=' . urlencode('Please fill in all fields'));
            exit;
        }
        
        if ($this->authService->authenticateAdmin($username, $password)) {
            $base = $this->getBasePath();
            header('Location: ' . $base . '/public/index.php?route=/admin');
            exit;
        } else {
            $base = $this->getBasePath();
            header('Location: ' . $base . '/public/index.php?route=/admin/login&error=' . urlencode('Invalid credentials'));
            exit;
        }
    }
    
    /**
     * Handle logout
     */
    public function logout() {
        $this->authService->logoutAdmin();
        $base = $this->getBasePath();
        header('Location: ' . $base . '/public/index.php?route=/admin/login');
        exit;
    }
}

