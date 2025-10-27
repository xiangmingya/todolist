<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$auth = new Auth();
$auth->requireLogin();

$tagManager = new TagManager($auth->getUserId());
$taskManager = new TaskManager($auth->getUserId());

// 获取标签ID
$tagId = $_GET['id'] ?? null;
$currentTag = null;
$tasks = [];

if ($tagId) {
    $currentTag = $tagManager->getTagById($tagId);
    if ($currentTag) {
        $tasks = $tagManager->getTasksByTag($tagId);
    }
}

$allTags = $tagManager->getTags();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $currentTag ? htmlspecialchars($currentTag['name']) : '标签'; ?> - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="main-wrapper">
        <div class="container">
            <?php if ($currentTag): ?>
                <!-- 标签详情页 -->
                <div class="page-header">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span class="tag-color-badge" style="background: <?php echo htmlspecialchars($currentTag['color']); ?>; width: 12px; height: 12px; border-radius: 50%; display: inline-block;"></span>
                        <div>
                            <h1 id="tagNameDisplay"><?php echo htmlspecialchars($currentTag['name']); ?></h1>
                            <p class="subtitle"><?php echo count($tasks); ?> 个任务</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <button class="btn btn-secondary btn-sm" id="editTagBtn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            编辑标签
                        </button>
                        <button class="btn btn-secondary btn-sm" id="deleteTagBtn" data-tag-id="<?php echo $currentTag['id']; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                            删除标签
                        </button>
                    </div>
                </div>

                <!-- 任务列表 -->
                <div class="tasks-section">
                    <div class="task-list">
                        <?php if (empty($tasks)): ?>
                            <div class="empty-state">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                                </svg>
                                <h3>暂无任务</h3>
                                <p>此标签下还没有任务</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($tasks as $task): 
                                $priorityClass = 'priority-' . $task['priority'];
                                $priorityIcon = $task['priority'] == 'high' ? '🚩' : ($task['priority'] == 'medium' ? '⚡' : '📌');
                            ?>
                                <div class="task-item" data-task-id="<?php echo $task['id']; ?>">
                                    <div class="task-checkbox">
                                        <input type="checkbox" class="task-complete" data-task-id="<?php echo $task['id']; ?>" <?php echo $task['status'] === 'completed' ? 'checked' : ''; ?>>
                                    </div>
                                    <div class="task-content">
                                        <h4 <?php echo $task['status'] === 'completed' ? 'style="text-decoration: line-through;"' : ''; ?>><?php echo htmlspecialchars($task['title']); ?></h4>
                                        <?php if ($task['due_date'] || $task['tag']): ?>
                                            <div class="task-meta">
                                                <?php if ($task['due_date']): ?>
                                                    <span class="task-date">📅 <?php echo date('m月d日', strtotime($task['due_date'])); ?></span>
                                                <?php endif; ?>
                                                <?php if ($task['tag']): ?>
                                                    <span class="task-tag">🏷️ <?php echo htmlspecialchars($task['tag']); ?></span>
                                                <?php endif; ?>
                                                <span class="<?php echo $priorityClass; ?>"><?php echo $priorityIcon; ?> <?php echo getPriorityLabel($task['priority']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="task-actions">
                                        <button class="btn-icon edit-task" data-task-id="<?php echo $task['id']; ?>" title="编辑">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                        </button>
                                        <button class="btn-icon delete-task" data-task-id="<?php echo $task['id']; ?>" title="删除">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- 标签列表页 -->
                <div class="page-header">
                    <div>
                        <h1>标签管理</h1>
                        <p class="subtitle">管理您的任务标签</p>
                    </div>
                    <button class="btn btn-primary" id="addTagBtn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        新建标签
                    </button>
                </div>

                <div class="tags-container">
                    <?php if (empty($allTags)): ?>
                        <div class="empty-state">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                <line x1="7" y1="7" x2="7.01" y2="7"></line>
                            </svg>
                            <h3>暂无标签</h3>
                            <p>点击"新建标签"按钮创建第一个标签吧！</p>
                        </div>
                    <?php else: ?>
                        <div class="tags-grid">
                            <?php foreach ($allTags as $tag): 
                                $tagTasks = $tagManager->getTasksByTag($tag['id']);
                            ?>
                                <a href="tags.php?id=<?php echo $tag['id']; ?>" class="tag-card">
                                    <div class="tag-card-header">
                                        <span class="tag-color-badge" style="background: <?php echo htmlspecialchars($tag['color']); ?>"></span>
                                        <h3><?php echo htmlspecialchars($tag['name']); ?></h3>
                                    </div>
                                    <p class="tag-task-count"><?php echo count($tagTasks); ?> 个任务</p>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 任务模态框 -->
    <div id="taskModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">编辑任务</h2>
                <button class="modal-close">&times;</button>
            </div>
            <form id="taskForm" class="modal-form">
                <input type="hidden" id="taskId" name="task_id">
                
                <div class="form-group">
                    <label for="taskTitle">任务标题 *</label>
                    <input type="text" id="taskTitle" name="title" required>
                </div>

                <div class="form-group">
                    <label for="taskDescription">任务描述</label>
                    <textarea id="taskDescription" name="description" rows="3"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="taskStatus">状态</label>
                        <select id="taskStatus" name="status">
                            <option value="pending">待办</option>
                            <option value="in_progress">进行中</option>
                            <option value="completed">已完成</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="taskPriority">优先级</label>
                        <select id="taskPriority" name="priority">
                            <option value="low">低</option>
                            <option value="medium">中</option>
                            <option value="high">高</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="taskTag">标签</label>
                        <input type="text" id="taskTag" name="tag">
                    </div>

                    <div class="form-group">
                        <label for="taskDueDate">截止日期</label>
                        <input type="date" id="taskDueDate" name="due_date">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-cancel">取消</button>
                    <button type="submit" class="btn btn-primary">保存</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 标签编辑模态框 -->
    <div id="tagModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="tagModalTitle">新建标签</h2>
                <button class="modal-close" data-modal="tagModal">&times;</button>
            </div>
            <form id="tagForm" class="modal-form">
                <input type="hidden" id="tagId" name="tag_id">
                
                <div class="form-group">
                    <label for="tagName">标签名称 *</label>
                    <input type="text" id="tagName" name="name" required maxlength="50">
                </div>

                <div class="form-group">
                    <label for="tagColor">标签颜色</label>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <input type="color" id="tagColor" name="color" value="#808080" style="width: 60px; height: 40px; border: none; cursor: pointer;">
                        <input type="text" id="tagColorHex" value="#808080" readonly style="width: 100px; padding: 8px;">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-cancel" data-modal="tagModal">取消</button>
                    <button type="submit" class="btn btn-primary">保存</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/script.js"></script>
    <script src="js/tags.js"></script>
</body>
</html>
