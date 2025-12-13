<?php
/**
 * Frontend Authentication Controller
 */

class AuthController extends Controller {
    private $authService;
    
    public function __construct() {
        parent::__construct();
        $this->authService = new AuthService();
    }
    
    public function loginForm() {
        if ($this->authService->isUserLoggedIn()) {
            $this->redirect('/');
        }
        
        $redirect = $this->get('redirect', '/');
        $error = $this->get('error', '');
        
        $this->view('front/auth/login', [
            'redirect' => $redirect,
            'error' => $error
        ]);
    }
    
    public function login() {
        Csrf::validate();
        
        $email = $this->post('email', '');
        $password = $this->post('password', '');
        $redirect = $this->post('redirect', '/');
        
        if (empty($email) || empty($password)) {
            $this->redirect('/login?error=' . urlencode('Please enter both email and password.') . '&redirect=' . urlencode($redirect));
            return;
        }
        
        if ($this->authService->authenticateUser($email, $password)) {
            $this->redirect($redirect);
        } else {
            $this->redirect('/login?error=' . urlencode('Invalid email or password.') . '&redirect=' . urlencode($redirect));
        }
    }
    
    /**
     * Show registration form
     */
    public function registerForm() {
        // If already logged in, redirect to home
        if ($this->authService->isUserLoggedIn()) {
            $this->redirect('/');
        }
        
        $redirect = $this->get('redirect', '/');
        $error = $this->get('error', '');
        
        $this->view('front/auth/register', [
            'redirect' => $redirect,
            'error' => $error
        ]);
    }
    
    public function register() {
        Csrf::validate();
        
        $fullName = trim($this->post('full_name', ''));
        $email = trim($this->post('email', ''));
        $password = $this->post('password', '');
        $confirmPassword = $this->post('confirm_password', '');
        $phone = trim($this->post('phone', ''));
        $redirect = $this->post('redirect', '/');
        
        $errors = [];
        
        if (empty($fullName)) {
            $errors[] = 'Full name is required.';
        }
        
        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        }
        
        if (empty($password)) {
            $errors[] = 'Password is required.';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters long.';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }
        
        if (!empty($errors)) {
            $this->redirect('/register?error=' . urlencode(implode(' ', $errors)) . '&redirect=' . urlencode($redirect));
            return;
        }
        
        try {
            if ($this->authService->registerUser($fullName, $email, $password, $phone)) {
                $this->redirect($redirect);
            } else {
                $this->redirect('/register?error=' . urlencode('Registration failed. Please try again.') . '&redirect=' . urlencode($redirect));
            }
        } catch (Exception $e) {
            $this->redirect('/register?error=' . urlencode($e->getMessage()) . '&redirect=' . urlencode($redirect));
        }
    }
    
    public function logout() {
        $this->authService->logoutUser();
        $this->redirect('/');
    }
}

