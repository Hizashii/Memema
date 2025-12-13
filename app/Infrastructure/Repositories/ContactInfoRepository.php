<?php
/**
 * Contact Info Repository
 */

class ContactInfoRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function find() {
        $sql = "SELECT * FROM contact_info LIMIT 1";
        return $this->db->queryOne($sql);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE contact_info SET phone = ?, email = ?, address = ? WHERE id = ?";
        return $this->db->execute($sql, [
            $data['phone'],
            $data['email'],
            $data['address'],
            $id
        ]);
    }
    
    public function create($data) {
        $sql = "INSERT INTO contact_info (phone, email, address) VALUES (?, ?, ?)";
        return $this->db->execute($sql, [
            $data['phone'],
            $data['email'],
            $data['address']
        ]);
    }
}

