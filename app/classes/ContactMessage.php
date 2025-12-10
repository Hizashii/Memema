<?php
require_once __DIR__ . '/Database.php';

/**
 * ContactMessage Class
 * 
 * Handles contact form messages.
 */
class ContactMessage {
    private $id;
    private $name;
    private $email;
    private $subject;
    private $message;
    private $status;
    private $createdAt;
    
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->name = $data['name'] ?? '';
            $this->email = $data['email'] ?? '';
            $this->subject = $data['subject'] ?? '';
            $this->message = $data['message'] ?? '';
            $this->status = $data['status'] ?? 'new';
            $this->createdAt = $data['created_at'] ?? null;
        }
    }
    
    /**
     * Get all contact messages
     */
    public static function getAll() {
        return Database::query("SELECT * FROM contact_messages ORDER BY created_at DESC");
    }
    
    /**
     * Get contact message by ID
     */
    public static function getById($id) {
        return Database::queryOne("SELECT * FROM contact_messages WHERE id = ?", [$id], 'i');
    }
    
    /**
     * Get contact info (phone, email, address)
     */
    public static function getContactInfo() {
        $info = Database::queryOne("SELECT phone, email, address FROM contact_info LIMIT 1");
        return $info ?: ['phone' => '', 'email' => '', 'address' => ''];
    }
    
    /**
     * Create a new contact message
     */
    public function create() {
        $sql = "INSERT INTO contact_messages (name, email, subject, message, status) VALUES (?, ?, ?, ?, ?)";
        $this->id = Database::execute(
            $sql,
            [$this->name, $this->email, $this->subject, $this->message, $this->status],
            'sssss'
        );
        return $this->id;
    }
    
    /**
     * Update message status
     */
    public static function updateStatus($id, $status) {
        return Database::execute(
            "UPDATE contact_messages SET status = ? WHERE id = ?",
            [$status, $id],
            'si'
        );
    }
    
    /**
     * Delete contact message
     */
    public static function delete($id) {
        return Database::execute("DELETE FROM contact_messages WHERE id = ?", [$id], 'i');
    }
    
    // Getters and Setters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }
    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }
    public function getSubject() { return $this->subject; }
    public function setSubject($subject) { $this->subject = $subject; }
    public function getMessage() { return $this->message; }
    public function setMessage($message) { $this->message = $message; }
    public function getStatus() { return $this->status; }
    public function setStatus($status) { $this->status = $status; }
}

