<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$auth = new Auth();
$auth->requireLogin();

$taskManager = new TaskManager($auth->getUserId());
$stats = $taskManager->getTaskStats();
$todayTasks = $taskManager->getTasks(['status' => 'pending']);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>仪表板 - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>欢迎回来，<?php echo htmlspecialchars($auth->getUsername()); ?>！</h1>
            <p class="subtitle">这是您的任务概览</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #007bff;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['total']; ?></h3>
                    <p>总任务数</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #ffc107;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['by_status']['pending'] ?? 0; ?></h3>
                    <p>待办任务</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #17a2b8;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20v-6M6 20V10M18 20V4"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['by_status']['in_progress'] ?? 0; ?></h3>
                    <p>进行中</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #28a745;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['by_status']['completed'] ?? 0; ?></h3>
                    <p>已完成</p>
                </div>
            </div>
        </div>

        <?php if ($stats['overdue'] > 0): ?>
        <div class="alert alert-warning">
            <strong>注意：</strong> 您有 <?php echo $stats['overdue']; ?> 个任务已逾期！
        </div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="dashboard-section">
                <div class="section-header">
                    <h2>快速添加任务</h2>
                </div>
                <form id="quickTaskForm" class="quick-task-form">
                    <div class="form-group">
                        <input type="text" id="quickTaskTitle" name="title" placeholder="任务标题" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <select id="quickTaskPriority" name="priority">
                                <option value="low">低优先级</option>
                                <option value="medium" selected>中优先级</option>
                                <option value="high">高优先级</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="date" id="quickTaskDate" name="due_date">
                        </div>
                        <button type="submit" class="btn btn-primary">添加</button>
                    </div>
                </form>
            </div>

            <div class="dashboard-section">
                <div class="section-header">
                    <h2>待办任务</h2>
                    <a href="tasks.php" class="btn btn-secondary btn-sm">查看全部</a>
                </div>
                <div class="task-list">
                    <?php if (empty($todayTasks)): ?>
                        <div class="empty-state">
                            <p>太棒了！您暂时没有待办任务。</p>
                        </div>
                    <?php else: ?>
                        <?php foreach (array_slice($todayTasks, 0, 5) as $task): ?>
                            <div class="task-item" data-task-id="<?php echo $task['id']; ?>">
                                <div class="task-checkbox">
                                    <input type="checkbox" class="task-complete" data-task-id="<?php echo $task['id']; ?>">
                                </div>
                                <div class="task-content">
                                    <h4><?php echo htmlspecialchars($task['title']); ?></h4>
                                    <?php if ($task['due_date']): ?>
                                        <p class="task-meta">
                                            <span class="task-date">截止: <?php echo $task['due_date']; ?></span>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="task-priority" style="background: <?php echo getPriorityColor($task['priority']); ?>">
                                    <?php echo getPriorityLabel($task['priority']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
