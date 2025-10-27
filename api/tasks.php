<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => '未授权']);
    exit;
}

$taskManager = new TaskManager($auth->getUserId());
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                $task = $taskManager->getTaskById($_GET['id']);
                if ($task) {
                    echo json_encode(['success' => true, 'data' => $task]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => '任务不存在']);
                }
            } else {
                $filters = [
                    'status' => $_GET['status'] ?? '',
                    'priority' => $_GET['priority'] ?? '',
                    'tag' => $_GET['tag'] ?? '',
                    'search' => $_GET['search'] ?? ''
                ];
                $tasks = $taskManager->getTasks($filters);
                echo json_encode(['success' => true, 'data' => $tasks]);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['title'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => '任务标题不能为空']);
                exit;
            }

            $result = $taskManager->createTask(
                $data['title'],
                $data['description'] ?? '',
                $data['status'] ?? 'pending',
                $data['priority'] ?? 'medium',
                $data['tag'] ?? '',
                $data['due_date'] ?? null
            );

            if ($result['success']) {
                http_response_code(201);
                echo json_encode($result);
            } else {
                http_response_code(500);
                echo json_encode($result);
            }
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => '任务ID不能为空']);
                exit;
            }

            $updateData = [];
            if (isset($data['title'])) $updateData['title'] = $data['title'];
            if (isset($data['description'])) $updateData['description'] = $data['description'];
            if (isset($data['status'])) $updateData['status'] = $data['status'];
            if (isset($data['priority'])) $updateData['priority'] = $data['priority'];
            if (isset($data['tag'])) $updateData['tag'] = $data['tag'];
            if (isset($data['due_date'])) $updateData['due_date'] = $data['due_date'];

            $result = $taskManager->updateTask($data['id'], $updateData);
            
            if ($result['success']) {
                echo json_encode($result);
            } else {
                http_response_code(500);
                echo json_encode($result);
            }
            break;

        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => '任务ID不能为空']);
                exit;
            }

            $result = $taskManager->deleteTask($data['id']);
            
            if ($result['success']) {
                echo json_encode($result);
            } else {
                http_response_code(500);
                echo json_encode($result);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => '不支持的请求方法']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '服务器错误: ' . $e->getMessage()]);
}
