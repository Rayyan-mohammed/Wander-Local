<?php
class User {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function login($email, $password) {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        if ($row) {
            if (password_verify($password, $row->password_hash)) {
                return $row;
            }
        }
        return false;
    }

    public function register($data) {
        $this->db->query("INSERT INTO users (name, email, password_hash, role) VALUES(:name, :email, :password, :role)");
        
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':role', $data['role']);

        return $this->db->execute();
    }

    public function findUserByEmail($email) {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        return $this->db->rowCount() > 0;
    }
}