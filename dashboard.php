<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$auth = new Auth();
$auth->requireLogin();

$taskManager = new TaskManager($auth->getUserId());
$stats = $taskManager->getTaskStats();
$todayTasks = $taskManager->getTasks(['status' => 'pending']);

// 获取今天的日期
$today = date('Y年m月d日');
$weekday = ['日', '一', '二', '三', '四', '五', '六'][date('w')];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>今天 - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <!-- 页面标题 - Todoist 风格 -->
        <div class="page-header">
            <div>
                <h1>今天</h1>
                <p class="subtitle"><?php echo $today; ?> · 星期<?php echo $weekday; ?></p>
            </div>
        </div>

        <!-- 统计卡片 - 简化版 -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #dc4c3e;">
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
                <div class="stat-icon" style="background: #058527;">
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

            <div class="stat-card">
                <div class="stat-icon" style="background: #246fe0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20v-6M6 20V10M18 20V4"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['total']; ?></h3>
                    <p>总任务数</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #ff9a14;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['overdue'] ?? 0; ?></h3>
                    <p>逾期任务</p>
                </div>
            </div>
        </div>

        <?php if ($stats['overdue'] > 0): ?>
        <div class="alert alert-warning">
            ⚠️ <strong>注意：</strong> 您有 <?php echo $stats['overdue']; ?> 个任务已逾期！
        </div>
        <?php endif; ?>

        <!-- 快速添加任务 - Todoist 风格 -->
        <div class="quick-add-section">
            <form id="quickTaskForm" class="quick-task-form">
                <div class="form-group">
                    <input type="text" id="quickTaskTitle" name="title" placeholder="添加任务，按下回车即可保存" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <select id="quickTaskPriority" name="priority">
                            <option value="low">🔵 低优先级</option>
                            <option value="medium" selected>🟡 中优先级</option>
                            <option value="high">🔴 高优先级</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="date" id="quickTaskDate" name="due_date" placeholder="截止日期">
                    </div>
                    <button type="submit" class="btn btn-primary">添加任务</button>
                </div>
            </form>
        </div>

        <!-- 任务列表 - Todoist 风格 -->
        <div class="tasks-section">
            <div class="section-header">
                <h2>📋 我的任务</h2>
                <a href="tasks.php" class="btn btn-secondary btn-sm">查看全部</a>
            </div>
            
            <div class="task-list">
                <?php if (empty($todayTasks)): ?>
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <h3>太棒了！</h3>
                        <p>您暂时没有待办任务，享受轻松的一天吧！</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($todayTasks as $task): 
                        $priorityClass = 'priority-' . $task['priority'];
                        $priorityIcon = $task['priority'] == 'high' ? '🚩' : ($task['priority'] == 'medium' ? '⚡' : '📌');
                    ?>
                        <div class="task-item" data-task-id="<?php echo $task['id']; ?>">
                            <div class="task-checkbox">
                                <input type="checkbox" class="task-complete" data-task-id="<?php echo $task['id']; ?>">
                            </div>
                            <div class="task-content">
                                <h4><?php echo htmlspecialchars($task['title']); ?></h4>
                                <?php if ($task['due_date'] || $task['category']): ?>
                                    <div class="task-meta">
                                        <?php if ($task['due_date']): ?>
                                            <span class="task-date">📅 <?php echo date('m月d日', strtotime($task['due_date'])); ?></span>
                                        <?php endif; ?>
                                        <?php if ($task['category']): ?>
                                            <span class="task-category">🏷️ <?php echo htmlspecialchars($task['category']); ?></span>
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

        <!-- 完成的任务（可选显示） -->
        <?php 
        $completedTasks = $taskManager->getTasks(['status' => 'completed']);
        if (!empty($completedTasks) && count($completedTasks) > 0): 
        ?>
        <div class="tasks-section" style="margin-top: 32px;">
            <div class="section-header">
                <h2>✅ 已完成 (<?php echo count($completedTasks); ?>)</h2>
            </div>
            
            <div class="task-list">
                <?php foreach (array_slice($completedTasks, 0, 5) as $task): ?>
                    <div class="task-item" style="opacity: 0.6;">
                        <div class="task-checkbox">
                            <input type="checkbox" class="task-complete" data-task-id="<?php echo $task['id']; ?>" checked>
                        </div>
                        <div class="task-content">
                            <h4 style="text-decoration: line-through;"><?php echo htmlspecialchars($task['title']); ?></h4>
                        </div>
                        <div class="task-actions">
                            <button class="btn-icon delete-task" data-task-id="<?php echo $task['id']; ?>" title="删除">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="js/script.js"></script>
    <script>
        // 支持回车键快速添加任务
        document.getElementById('quickTaskTitle').addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('quickTaskForm').dispatchEvent(new Event('submit'));
            }
        });
    </script>
</body>
</html>
