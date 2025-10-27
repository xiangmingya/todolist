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

// 标签管理函数
class TagManager {
    private $db;
    private $userId;

    public function __construct($userId) {
        $this->db = Database::getInstance();
        $this->userId = $userId;
    }

    public function createTag($name, $color = '#808080') {
        $stmt = $this->db->prepare("INSERT INTO tags (user_id, name, color) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $this->userId, $name, $color);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => '标签创建成功', 'tag_id' => $this->db->lastInsertId()];
        } else {
            if ($stmt->errno == 1062) { // Duplicate entry
                return ['success' => false, 'message' => '标签名称已存在'];
            }
            return ['success' => false, 'message' => '标签创建失败'];
        }
    }

    public function getTags() {
        $stmt = $this->db->prepare("SELECT * FROM tags WHERE user_id = ? ORDER BY name ASC");
        $stmt->bind_param("i", $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $tags = [];
        while ($row = $result->fetch_assoc()) {
            $tags[] = $row;
        }

        return $tags;
    }

    public function getTagById($tagId) {
        $stmt = $this->db->prepare("SELECT * FROM tags WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $tagId, $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    public function updateTag($tagId, $data) {
        $fields = [];
        $params = [];
        $types = "";

        if (isset($data['name'])) {
            $fields[] = "name = ?";
            $params[] = $data['name'];
            $types .= "s";
        }

        if (isset($data['color'])) {
            $fields[] = "color = ?";
            $params[] = $data['color'];
            $types .= "s";
        }

        if (empty($fields)) {
            return ['success' => false, 'message' => '没有要更新的字段'];
        }

        $sql = "UPDATE tags SET " . implode(", ", $fields) . " WHERE id = ? AND user_id = ?";
        $params[] = $tagId;
        $params[] = $this->userId;
        $types .= "ii";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => '标签更新成功'];
        } else {
            return ['success' => false, 'message' => '标签更新失败'];
        }
    }

    public function deleteTag($tagId) {
        $stmt = $this->db->prepare("DELETE FROM tags WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $tagId, $this->userId);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => '标签删除成功'];
        } else {
            return ['success' => false, 'message' => '标签删除失败'];
        }
    }

    public function addTagToTask($taskId, $tagId) {
        $stmt = $this->db->prepare("INSERT INTO task_tags (task_id, tag_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $taskId, $tagId);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => '标签已添加'];
        } else {
            if ($stmt->errno == 1062) { // Duplicate entry
                return ['success' => false, 'message' => '标签已存在'];
            }
            return ['success' => false, 'message' => '添加标签失败'];
        }
    }

    public function removeTagFromTask($taskId, $tagId) {
        $stmt = $this->db->prepare("DELETE FROM task_tags WHERE task_id = ? AND tag_id = ?");
        $stmt->bind_param("ii", $taskId, $tagId);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => '标签已移除'];
        } else {
            return ['success' => false, 'message' => '移除标签失败'];
        }
    }

    public function getTasksByTag($tagId) {
        $sql = "SELECT t.* FROM tasks t
                INNER JOIN task_tags tt ON t.id = tt.task_id
                WHERE tt.tag_id = ? AND t.user_id = ?
                ORDER BY t.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $tagId, $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }

        return $tasks;
    }

    public function getTagsForTask($taskId) {
        $sql = "SELECT t.* FROM tags t
                INNER JOIN task_tags tt ON t.id = tt.tag_id
                WHERE tt.task_id = ? AND t.user_id = ?
                ORDER BY t.name ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $taskId, $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $tags = [];
        while ($row = $result->fetch_assoc()) {
            $tags[] = $row;
        }

        return $tags;
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
