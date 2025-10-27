<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$auth = new Auth();
$auth->requireLogin();

$taskManager = new TaskManager($auth->getUserId());

// 获取筛选参数
$filters = [
    'status' => $_GET['status'] ?? '',
    'priority' => $_GET['priority'] ?? '',
    'category' => $_GET['category'] ?? '',
    'search' => $_GET['search'] ?? ''
];

$tasks = $taskManager->getTasks($filters);
$categories = $taskManager->getCategories();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>任务管理 - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="main-wrapper">
        <div class="container">
        <div class="page-header">
            <div>
                <h1>任务管理</h1>
                <p class="subtitle">查看和管理所有任务</p>
            </div>
            <button class="btn btn-primary" id="addTaskBtn">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                新建任务
            </button>
        </div>

        <div class="filters-bar">
            <form method="GET" class="filters-form">
                <div class="filter-group">
                    <input type="text" name="search" placeholder="搜索任务..." 
                           value="<?php echo htmlspecialchars($filters['search']); ?>">
                </div>

                <div class="filter-group">
                    <select name="status">
                        <option value="">所有状态</option>
                        <option value="pending" <?php echo $filters['status'] === 'pending' ? 'selected' : ''; ?>>待办</option>
                        <option value="in_progress" <?php echo $filters['status'] === 'in_progress' ? 'selected' : ''; ?>>进行中</option>
                        <option value="completed" <?php echo $filters['status'] === 'completed' ? 'selected' : ''; ?>>已完成</option>
                    </select>
                </div>

                <div class="filter-group">
                    <select name="priority">
                        <option value="">所有优先级</option>
                        <option value="low" <?php echo $filters['priority'] === 'low' ? 'selected' : ''; ?>>低</option>
                        <option value="medium" <?php echo $filters['priority'] === 'medium' ? 'selected' : ''; ?>>中</option>
                        <option value="high" <?php echo $filters['priority'] === 'high' ? 'selected' : ''; ?>>高</option>
                    </select>
                </div>

                <div class="filter-group">
                    <select name="category">
                        <option value="">所有标签</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category); ?>" 
                                    <?php echo $filters['category'] === $category ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-secondary">筛选</button>
                <a href="tasks.php" class="btn btn-secondary">重置</a>
            </form>
        </div>

        <div class="tasks-container">
            <?php if (empty($tasks)): ?>
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    <h3>暂无任务</h3>
                    <p>点击"新建任务"按钮创建第一个任务吧！</p>
                </div>
            <?php else: ?>
                <div class="tasks-table">
                    <table>
                        <thead>
                            <tr>
                                <th>任务</th>
                                <th>状态</th>
                                <th>优先级</th>
                                <th>标签</th>
                                <th>截止日期</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tasks as $task): ?>
                                <tr data-task-id="<?php echo $task['id']; ?>">
                                    <td>
                                        <div class="task-title">
                                            <strong><?php echo htmlspecialchars($task['title']); ?></strong>
                                            <?php if ($task['description']): ?>
                                                <p class="task-description"><?php echo htmlspecialchars($task['description']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-status" style="background: <?php echo getStatusColor($task['status']); ?>">
                                            <?php echo getStatusLabel($task['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-priority" style="background: <?php echo getPriorityColor($task['priority']); ?>">
                                            <?php echo getPriorityLabel($task['priority']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($task['category'] ?: '-'); ?></td>
                                    <td>
                                        <?php if ($task['due_date']): ?>
                                            <span class="<?php echo (strtotime($task['due_date']) < time() && $task['status'] !== 'completed') ? 'text-danger' : ''; ?>">
                                                <?php echo $task['due_date']; ?>
                                            </span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
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
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        </div>
    </div>

    <!-- 任务模态框 -->
    <div id="taskModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">新建任务</h2>
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
                        <label for="taskCategory">标签</label>
                        <input type="text" id="taskCategory" name="category" list="categoryList">
                        <datalist id="categoryList">
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category); ?>">
                            <?php endforeach; ?>
                        </datalist>
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
