<?php
/**
 * Venue Entity
 */

class Venue {
    private $id;
    private $name;
    private $address;
    private $phone;
    private $image;
    private $screens;
    
    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->address = $data['address'] ?? '';
        $this->phone = $data['phone'] ?? '';
        $this->image = $data['image'] ?? '';
        $this->screens = $data['screens'] ?? [];
    }
    
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getAddress() { return $this->address; }
    public function getPhone() { return $this->phone; }
    public function getImage() { return $this->image; }
    public function getScreens() { return $this->screens; }
    
    public function setId($id) { $this->id = $id; }
    public function setName($name) { $this->name = $name; }
    public function setAddress($address) { $this->address = $address; }
    public function setPhone($phone) { $this->phone = $phone; }
    public function setImage($image) { $this->image = $image; }
    public function setScreens($screens) { $this->screens = $screens; }
    
    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'image' => $this->image,
            'screens' => $this->screens
        ];
    }
}

