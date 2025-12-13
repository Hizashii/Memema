<?php
/**
 * Contact Message Repository
 */

class ContactMessageRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function findAll() {
        $sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
        return $this->db->query($sql);
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM contact_messages WHERE id = ?";
        return $this->db->queryOne($sql, [$id]);
    }
    
    public function findByStatus($status) {
        $sql = "SELECT * FROM contact_messages WHERE status = ? ORDER BY created_at DESC";
        return $this->db->query($sql, [$status]);
    }
    
    public function create($data) {
        $sql = "INSERT INTO contact_messages (name, email, subject, message, status) VALUES (?, ?, ?, ?, ?)";
        return $this->db->execute($sql, [
            $data['name'],
            $data['email'],
            $data['subject'],
            $data['message'],
            $data['status'] ?? 'new'
        ]);
    }
    
    public function updateStatus($id, $status) {
        $sql = "UPDATE contact_messages SET status = ? WHERE id = ?";
        return $this->db->execute($sql, [$status, $id]);
    }
    
    public function delete($id) {
        return $this->db->execute("DELETE FROM contact_messages WHERE id = ?", [$id]);
    }
}

