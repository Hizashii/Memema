<?php
/**
 * Admin Users Controller
 */

class UsersController extends Controller {
    // $db is already defined as protected in parent Controller class
    
    public function __construct() {
        parent::__construct();
        // $this->db is already set by parent::__construct()
    }
    
    public function index() {
        try {
            $users = $this->db->query("SELECT id, full_name, email, phone, created_at FROM users ORDER BY created_at DESC");
        } catch (Exception $e) {
            $users = [];
            $error = "Unable to load users.";
        }
        
        $this->view('admin/users/index', [
            'users' => $users,
            'error' => $error ?? null,
            'success' => $this->get('success'),
            'currentPage' => 'users'
        ]);
    }
    
    public function create() {
        $this->view('admin/users/form', [
            'user' => null,
            'currentPage' => 'users'
        ]);
    }
    
    public function store() {
        if (!$this->isPost()) {
            $this->redirect('/admin/users');
        }
        
        Csrf::validate();
        
        $fullName = trim($this->post('full_name', ''));
        $email = trim($this->post('email', ''));
        $phone = trim($this->post('phone', ''));
        $password = $this->post('password', '');
        
        if (empty($fullName) || empty($email) || empty($password)) {
            $this->redirect('/admin/users/create?error=' . urlencode('Name, email, and password are required'));
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirect('/admin/users/create?error=' . urlencode('Invalid email address'));
        }
        
        // Check if email already exists
        $existing = $this->db->queryOne("SELECT id FROM users WHERE email = ?", [$email]);
        if ($existing) {
            $this->redirect('/admin/users/create?error=' . urlencode('Email already exists'));
        }
        
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $this->db->execute(
                "INSERT INTO users (full_name, email, phone, password) VALUES (?, ?, ?, ?)",
                [$fullName, $email, $phone, $hashedPassword]
            );
            $this->redirect('/admin/users?success=' . urlencode('User created successfully'));
        } catch (Exception $e) {
            $this->redirect('/admin/users/create?error=' . urlencode($e->getMessage()));
        }
    }
}

