<?php
require_once __DIR__ . '/Database.php';

/**
 * User Class
 * 
 * Handles user CRUD operations with password hashing.
 */
class User {
    private $id;
    private $fullName;
    private $email;
    private $password;
    private $phone;
    private $createdAt;
    private $updatedAt;
    
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->fullName = $data['full_name'] ?? '';
            $this->email = $data['email'] ?? '';
            $this->password = $data['password'] ?? '';
            $this->phone = $data['phone'] ?? null;
            $this->createdAt = $data['created_at'] ?? null;
            $this->updatedAt = $data['updated_at'] ?? null;
        }
    }
    
    /**
     * Get all users
     */
    public static function getAll() {
        return Database::query("SELECT id, full_name, email, phone, created_at FROM users ORDER BY created_at DESC");
    }
    
    /**
     * Get user by ID
     */
    public static function getById($id) {
        $user = Database::queryOne("SELECT * FROM users WHERE id = ?", [$id], 'i');
        if ($user) {
            unset($user['password']);
        }
        return $user;
    }
    
    /**
     * Get user by email (includes password for verification)
     */
    public static function getByEmail($email) {
        return Database::queryOne("SELECT * FROM users WHERE email = ?", [$email], 's');
    }
    
    /**
     * Create a new user
     */
    public function create() {
        if (!empty($this->password)) {
            $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        }
        
        $sql = "INSERT INTO users (full_name, email, password, phone) VALUES (?, ?, ?, ?)";
        $this->id = Database::execute(
            $sql,
            [$this->fullName, $this->email, $this->password, $this->phone],
            'ssss'
        );
        return $this->id;
    }
    
    /**
     * Update user
     */
    public function update() {
        if (!$this->id) {
            throw new Exception('Cannot update user without ID');
        }
        
        $sql = "UPDATE users SET full_name = ?, email = ?, phone = ?";
        $params = [$this->fullName, $this->email, $this->phone];
        $types = 'sss';
        
        if (!empty($this->password)) {
            $this->password = password_hash($this->password, PASSWORD_DEFAULT);
            $sql .= ", password = ?";
            $params[] = $this->password;
            $types .= 's';
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $this->id;
        $types .= 'i';
        
        Database::execute($sql, $params, $types);
        return true;
    }
    
    /**
     * Verify password
     */
    public function verifyPassword($password) {
        return password_verify($password, $this->password);
    }
    
    /**
     * Delete user
     */
    public static function delete($id) {
        return Database::execute("DELETE FROM users WHERE id = ?", [$id], 'i');
    }
    
    // Getters and Setters
    public function getId() { return $this->id; }
    public function getFullName() { return $this->fullName; }
    public function setFullName($fullName) { $this->fullName = $fullName; }
    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }
    public function getPassword() { return $this->password; }
    public function setPassword($password) { $this->password = $password; }
    public function getPhone() { return $this->phone; }
    public function setPhone($phone) { $this->phone = $phone; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }
}
