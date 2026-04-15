<?php
class Experience {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getExperiences() {
        $this->db->query("SELECT experiences.*, users.name as host_name, users.avatar_url FROM experiences INNER JOIN users ON experiences.host_id = users.id ORDER BY experiences.created_at DESC");
        
        return $this->db->resultSet();
    }

    public function getExperienceById($id) {
        $this->db->query("SELECT experiences.*, users.name as host_name, users.bio as host_bio, users.languages as host_languages, users.is_verified, users.avatar_url FROM experiences INNER JOIN users ON experiences.host_id = users.id WHERE experiences.id = :id");
        $this->db->bind(':id', $id);

        return $this->db->single();
    }

    public function addExperience($data) {
        $this->db->query("INSERT INTO experiences (host_id, title, description, price, duration, category, location, image_url) VALUES (:host_id, :title, :description, :price, :duration, :category, :location, :image_url)");

        $this->db->bind(':host_id', $data['host_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':duration', $data['duration']);
        $this->db->bind(':category', $data['category']);
        $this->db->bind(':location', $data['location']);
        $this->db->bind(':image_url', $data['image_url']);

        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
}