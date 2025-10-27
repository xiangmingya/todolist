<?php
require_once 'database.php';

class Auth {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function register($username, $email, $password) {
        // 验证输入
        if (empty($username) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => '所有字段都是必填的'];
        }

        // 验证邮箱格式
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => '邮箱格式不正确'];
        }

        // 验证密码长度
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => '密码至少需要6个字符'];
        }

        // 检查用户名是否已存在
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return ['success' => false, 'message' => '用户名已存在'];
        }

        // 检查邮箱是否已存在
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return ['success' => false, 'message' => '邮箱已被注册'];
        }

        // 加密密码
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // 插入新用户
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashedPassword);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => '注册成功'];
        } else {
            return ['success' => false, 'message' => '注册失败，请重试'];
        }
    }

    public function login($username, $password) {
        if (empty($username) || empty($password)) {
            return ['success' => false, 'message' => '用户名和密码不能为空'];
        }

        $stmt = $this->db->prepare("SELECT id, username, email, password FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => '用户名或密码错误'];
        }

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['last_activity'] = time();
            
            return ['success' => true, 'message' => '登录成功'];
        } else {
            return ['success' => false, 'message' => '用户名或密码错误'];
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        return ['success' => true, 'message' => '已退出登录'];
    }

    public function isLoggedIn() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        // 检查会话是否过期
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_LIFETIME)) {
            $this->logout();
            return false;
        }

        $_SESSION['last_activity'] = time();
        return true;
    }

    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }

    public function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    public function getUsername() {
        return $_SESSION['username'] ?? null;
    }

    public function getEmail() {
        return $_SESSION['email'] ?? null;
    }
}
