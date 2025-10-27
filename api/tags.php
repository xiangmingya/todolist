<?php
header('Content-Type: application/json');

require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => '未授权访问']);
    exit;
}

$tagManager = new TagManager($auth->getUserId());
$method = $_SERVER['REQUEST_METHOD'];

// GET - 获取标签列表或单个标签
if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $tag = $tagManager->getTagById($_GET['id']);
        if ($tag) {
            echo json_encode(['success' => true, 'data' => $tag]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => '标签不存在']);
        }
    } else {
        $tags = $tagManager->getTags();
        echo json_encode(['success' => true, 'data' => $tags]);
    }
}

// POST - 创建新标签
elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['name'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => '标签名称不能为空']);
        exit;
    }
    
    $result = $tagManager->createTag(
        $input['name'],
        $input['color'] ?? '#808080'
    );
    
    echo json_encode($result);
}

// PUT - 更新标签
elseif ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => '标签ID不能为空']);
        exit;
    }
    
    $data = [];
    if (isset($input['name'])) $data['name'] = $input['name'];
    if (isset($input['color'])) $data['color'] = $input['color'];
    
    $result = $tagManager->updateTag($input['id'], $data);
    echo json_encode($result);
}

// DELETE - 删除标签
elseif ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => '标签ID不能为空']);
        exit;
    }
    
    $result = $tagManager->deleteTag($input['id']);
    echo json_encode($result);
}

else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => '不支持的请求方法']);
}
