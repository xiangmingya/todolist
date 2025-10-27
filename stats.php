<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$auth = new Auth();
$auth->requireLogin();

$taskManager = new TaskManager($auth->getUserId());
$stats = $taskManager->getTaskStats();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>统计图表 - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="page-header">
            <div>
                <h1>统计图表</h1>
                <p class="subtitle">任务数据统计与分析</p>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <h3>任务完成情况</h3>
                <canvas id="completionChart"></canvas>
            </div>

            <div class="chart-card">
                <h3>优先级分布</h3>
                <canvas id="priorityChart"></canvas>
            </div>

            <div class="chart-card">
                <h3>分类分布</h3>
                <canvas id="categoryChart"></canvas>
            </div>

            <div class="chart-card">
                <h3>状态分布</h3>
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        const statsData = <?php echo json_encode($stats); ?>;

        // 任务完成情况 - 甜甜圈图
        const completionCtx = document.getElementById('completionChart').getContext('2d');
        const completedCount = statsData.by_status.completed || 0;
        const totalCount = statsData.total || 1;
        const completionRate = Math.round((completedCount / totalCount) * 100);
        
        new Chart(completionCtx, {
            type: 'doughnut',
            data: {
                labels: ['已完成', '未完成'],
                datasets: [{
                    data: [completedCount, totalCount - completedCount],
                    backgroundColor: ['#058527', '#f3f3f3'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const percentage = Math.round((value / totalCount) * 100);
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        // 优先级分布 - 柱状图
        const priorityCtx = document.getElementById('priorityChart').getContext('2d');
        const priorityData = statsData.by_priority || {};
        
        new Chart(priorityCtx, {
            type: 'bar',
            data: {
                labels: ['低优先级', '中优先级', '高优先级'],
                datasets: [{
                    label: '任务数量',
                    data: [
                        priorityData.low || 0,
                        priorityData.medium || 0,
                        priorityData.high || 0
                    ],
                    backgroundColor: ['#246fe0', '#ff9a14', '#dc4c3e'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // 分类分布 - 饼图
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryData = statsData.by_category || {};
        const categoryLabels = Object.keys(categoryData);
        const categoryValues = Object.values(categoryData);
        
        const categoryColors = [
            '#dc4c3e', '#246fe0', '#ff9a14', '#058527', 
            '#8b5cf6', '#ec4899', '#f59e0b', '#10b981'
        ];
        
        new Chart(categoryCtx, {
            type: 'pie',
            data: {
                labels: categoryLabels.length > 0 ? categoryLabels : ['未分类'],
                datasets: [{
                    data: categoryValues.length > 0 ? categoryValues : [totalCount],
                    backgroundColor: categoryColors.slice(0, categoryLabels.length || 1),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // 状态分布 - 柱状图
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusData = statsData.by_status || {};
        
        new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: ['待办', '进行中', '已完成'],
                datasets: [{
                    label: '任务数量',
                    data: [
                        statusData.pending || 0,
                        statusData.in_progress || 0,
                        statusData.completed || 0
                    ],
                    backgroundColor: ['#808080', '#246fe0', '#058527'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>
</html>
