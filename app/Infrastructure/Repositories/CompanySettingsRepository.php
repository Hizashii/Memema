<?php

class CompanySettingsRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function find() {
        $sql = "SELECT * FROM company_settings LIMIT 1";
        $settings = $this->db->queryOne($sql);
        
        if ($settings) {
            if (!empty($settings['features'])) {
                $settings['features'] = explode(',', $settings['features']);
            } else {
                $settings['features'] = [];
            }
        }
        
        return $settings;
    }
    
    public function update($id, $data) {
        $features = '';
        if (!empty($data['features']) && is_array($data['features'])) {
            $features = implode(',', $data['features']);
        } elseif (!empty($data['features'])) {
            $features = $data['features'];
        }
        
        $sql = "UPDATE company_settings SET title = ?, description = ?, features = ?, opening_hours = ? WHERE id = ?";
        return $this->db->execute($sql, [
            $data['title'],
            $data['description'],
            $features,
            $data['opening_hours'] ?? '',
            $id
        ]);
    }
    
    public function create($data) {
        $features = '';
        if (!empty($data['features']) && is_array($data['features'])) {
            $features = implode(',', $data['features']);
        } elseif (!empty($data['features'])) {
            $features = $data['features'];
        }
        
        $sql = "INSERT INTO company_settings (title, description, features, opening_hours) VALUES (?, ?, ?, ?)";
        return $this->db->execute($sql, [
            $data['title'],
            $data['description'],
            $features,
            $data['opening_hours'] ?? ''
        ]);
    }
}

