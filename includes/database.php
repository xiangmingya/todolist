<?php
require_once 'config.php';

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($this->conn->connect_error) {
                throw new Exception("数据库连接失败: " . $this->conn->connect_error);
            }
            
            $this->conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            die("数据库错误: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function query($sql) {
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("查询错误: " . $this->conn->error);
        }
        return $result;
    }

    public function prepare($sql) {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("预处理语句错误: " . $this->conn->error);
        }
        return $stmt;
    }

    public function escape($value) {
        return $this->conn->real_escape_string($value);
    }

    public function lastInsertId() {
        return $this->conn->insert_id;
    }
}

function getDB() {
    return Database::getInstance()->getConnection();
}
