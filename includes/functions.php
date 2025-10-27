<?php
require_once 'database.php';

// 任务管理函数
class TaskManager {
    private $db;
    private $userId;

    public function __construct($userId) {
        $this->db = Database::getInstance();
        $this->userId = $userId;
    }

    public function createTask($title, $description, $status, $priority, $category, $dueDate) {
        $stmt = $this->db->prepare("INSERT INTO tasks (user_id, title, description, status, priority, category, due_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $this->userId, $title, $description, $status, $priority, $category, $dueDate);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => '任务创建成功', 'task_id' => $this->db->lastInsertId()];
        } else {
            return ['success' => false, 'message' => '任务创建失败'];
        }
    }

    public function getTasks($filters = []) {
        $sql = "SELECT * FROM tasks WHERE user_id = ?";
        $params = [$this->userId];
        $types = "i";

        if (!empty($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }

        if (!empty($filters['priority'])) {
            $sql .= " AND priority = ?";
            $params[] = $filters['priority'];
            $types .= "s";
        }

        if (!empty($filters['category'])) {
            $sql .= " AND category = ?";
            $params[] = $filters['category'];
            $types .= "s";
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (title LIKE ? OR description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "ss";
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }

        return $tasks;
    }

    public function getTaskById($taskId) {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $taskId, $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    public function updateTask($taskId, $data) {
        $fields = [];
        $params = [];
        $types = "";

        if (isset($data['title'])) {
            $fields[] = "title = ?";
            $params[] = $data['title'];
            $types .= "s";
        }

        if (isset($data['description'])) {
            $fields[] = "description = ?";
            $params[] = $data['description'];
            $types .= "s";
        }

        if (isset($data['status'])) {
            $fields[] = "status = ?";
            $params[] = $data['status'];
            $types .= "s";
        }

        if (isset($data['priority'])) {
            $fields[] = "priority = ?";
            $params[] = $data['priority'];
            $types .= "s";
        }

        if (isset($data['category'])) {
            $fields[] = "category = ?";
            $params[] = $data['category'];
            $types .= "s";
        }

        if (isset($data['due_date'])) {
            $fields[] = "due_date = ?";
            $params[] = $data['due_date'];
            $types .= "s";
        }

        if (empty($fields)) {
            return ['success' => false, 'message' => '没有要更新的字段'];
        }

        $sql = "UPDATE tasks SET " . implode(", ", $fields) . " WHERE id = ? AND user_id = ?";
        $params[] = $taskId;
        $params[] = $this->userId;
        $types .= "ii";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => '任务更新成功'];
        } else {
            return ['success' => false, 'message' => '任务更新失败'];
        }
    }

    public function deleteTask($taskId) {
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $taskId, $this->userId);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => '任务删除成功'];
        } else {
            return ['success' => false, 'message' => '任务删除失败'];
        }
    }

    public function getTaskStats() {
        $stats = [];

        // 总任务数
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM tasks WHERE user_id = ?");
        $stmt->bind_param("i", $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['total'] = $result->fetch_assoc()['total'];

        // 按状态统计
        $stmt = $this->db->prepare("SELECT status, COUNT(*) as count FROM tasks WHERE user_id = ? GROUP BY status");
        $stmt->bind_param("i", $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['by_status'] = [];
        while ($row = $result->fetch_assoc()) {
            $stats['by_status'][$row['status']] = $row['count'];
        }

        // 按优先级统计
        $stmt = $this->db->prepare("SELECT priority, COUNT(*) as count FROM tasks WHERE user_id = ? GROUP BY priority");
        $stmt->bind_param("i", $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['by_priority'] = [];
        while ($row = $result->fetch_assoc()) {
            $stats['by_priority'][$row['priority']] = $row['count'];
        }

        // 按分类统计
        $stmt = $this->db->prepare("SELECT category, COUNT(*) as count FROM tasks WHERE user_id = ? GROUP BY category");
        $stmt->bind_param("i", $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['by_category'] = [];
        while ($row = $result->fetch_assoc()) {
            $stats['by_category'][$row['category']] = $row['count'];
        }

        // 今日到期任务
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM tasks WHERE user_id = ? AND due_date = CURDATE()");
        $stmt->bind_param("i", $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['due_today'] = $result->fetch_assoc()['count'];

        // 逾期任务
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM tasks WHERE user_id = ? AND due_date < CURDATE() AND status != 'completed'");
        $stmt->bind_param("i", $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['overdue'] = $result->fetch_assoc()['count'];

        return $stats;
    }

    public function getCategories() {
        $stmt = $this->db->prepare("SELECT DISTINCT category FROM tasks WHERE user_id = ? AND category IS NOT NULL AND category != ''");
        $stmt->bind_param("i", $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row['category'];
        }

        return $categories;
    }
}

// 工具函数
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function formatDate($date) {
    if (empty($date)) return '';
    return date('Y-m-d', strtotime($date));
}

function formatDateTime($datetime) {
    if (empty($datetime)) return '';
    return date('Y-m-d H:i:s', strtotime($datetime));
}

function getStatusLabel($status) {
    $labels = [
        'pending' => '待办',
        'in_progress' => '进行中',
        'completed' => '已完成'
    ];
    return $labels[$status] ?? $status;
}

function getPriorityLabel($priority) {
    $labels = [
        'low' => '低',
        'medium' => '中',
        'high' => '高'
    ];
    return $labels[$priority] ?? $priority;
}

function getPriorityColor($priority) {
    $colors = [
        'low' => '#28a745',
        'medium' => '#ffc107',
        'high' => '#dc3545'
    ];
    return $colors[$priority] ?? '#6c757d';
}

function getStatusColor($status) {
    $colors = [
        'pending' => '#6c757d',
        'in_progress' => '#007bff',
        'completed' => '#28a745'
    ];
    return $colors[$status] ?? '#6c757d';
}
