<?php
require_once __DIR__ . '/../config/database.php';

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function login($email, $password, $role = 'user') {
        $sql = "SELECT * FROM users WHERE email = ? AND role = ?";
        $user = $this->db->fetch($sql, [$email, $role]);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            return true;
        }
        return false;
    }
    
    public function register($name, $email, $password, $role = 'user') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "SELECT id FROM users WHERE email = ?";
        $existing = $this->db->fetch($sql, [$email]);
        
        if ($existing) {
            return false;
        }
        
        $userId = $this->db->insert('users', [
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role
        ]);
        
        return $userId;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $sql = "SELECT * FROM users WHERE id = ?";
        return $this->db->fetch($sql, [$_SESSION['user_id']]);
    }
    
    public function logout() {
        session_destroy();
        header('Location: ../index.php');
        exit;
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: ../index.php');
            exit;
        }
    }
    
    public function requireRole($role) {
        $this->requireLogin();
        if ($_SESSION['user_role'] !== $role) {
            header('Location: ../index.php');
            exit;
        }
    }
}
?>