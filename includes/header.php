<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<header class="header">
    <div class="header-container">
        <div class="logo">
            <a href="dashboard.php"><?php echo APP_NAME; ?></a>
        </div>
        <nav class="nav">
            <a href="dashboard.php" class="nav-link <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">今天</a>
            <a href="tasks.php" class="nav-link <?php echo $currentPage === 'tasks.php' ? 'active' : ''; ?>">任务</a>
            <a href="stats.php" class="nav-link <?php echo $currentPage === 'stats.php' ? 'active' : ''; ?>">统计</a>
        </nav>
        <div class="user-menu">
            <span class="username"><?php echo htmlspecialchars($auth->getUsername()); ?></span>
            <a href="logout.php" class="btn btn-secondary btn-sm">退出</a>
        </div>
    </div>
</header>
