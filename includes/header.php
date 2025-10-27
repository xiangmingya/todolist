<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!-- 侧边栏遮罩层（移动端） -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- 移动端菜单按钮 -->
<button class="mobile-menu-toggle" id="mobileMenuToggle">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="3" y1="12" x2="21" y2="12"></line>
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <line x1="3" y1="18" x2="21" y2="18"></line>
    </svg>
</button>

<!-- 左侧边栏 -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <a href="dashboard.php"><?php echo APP_NAME; ?></a>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-link <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            今天
        </a>
        <a href="tasks.php" class="nav-link <?php echo $currentPage === 'tasks.php' ? 'active' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
            任务
        </a>
        
        <!-- 标签部分 -->
        <div class="nav-section">
            <div class="nav-section-header">
                <span class="nav-section-title">标签</span>
                <button class="btn-icon btn-add-tag" id="sidebarAddTagBtn" title="添加标签">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </button>
            </div>
            <div id="tagsList">
                <?php
                if (isset($auth) && $auth->isLoggedIn()) {
                    require_once __DIR__ . '/functions.php';
                    $tagManager = new TagManager($auth->getUserId());
                    $userTags = $tagManager->getTags();
                    
                    if (!empty($userTags)) {
                        foreach ($userTags as $tag) {
                            $isActive = ($currentPage === 'tags.php' && isset($_GET['id']) && $_GET['id'] == $tag['id']) ? 'active' : '';
                            echo '<a href="tags.php?id=' . $tag['id'] . '" class="nav-link nav-tag ' . $isActive . '">';
                            echo '<span class="tag-color-dot" style="background: ' . htmlspecialchars($tag['color']) . '; width: 8px; height: 8px; border-radius: 50%; display: inline-block;"></span>';
                            echo htmlspecialchars($tag['name']);
                            echo '</a>';
                        }
                    } else {
                        echo '<div class="nav-empty" style="padding: 8px 24px; color: var(--text-secondary); font-size: 12px;">暂无标签</div>';
                    }
                }
                ?>
            </div>
        </div>
        
        <a href="stats.php" class="nav-link <?php echo $currentPage === 'stats.php' ? 'active' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="20" x2="12" y2="10"></line>
                <line x1="18" y1="20" x2="18" y2="4"></line>
                <line x1="6" y1="20" x2="6" y2="16"></line>
            </svg>
            统计
        </a>
    </nav>
    
    <div class="sidebar-footer">
        <div class="user-menu">
            <span class="username"><?php echo htmlspecialchars($auth->getUsername()); ?></span>
            <a href="logout.php" class="btn btn-secondary btn-sm btn-block">退出登录</a>
        </div>
    </div>
</aside>

<script>
// 移动端菜单切换
const sidebar = document.getElementById('sidebar');
const sidebarOverlay = document.getElementById('sidebarOverlay');
const mobileMenuToggle = document.getElementById('mobileMenuToggle');

function toggleSidebar() {
    sidebar.classList.toggle('open');
    sidebarOverlay.classList.toggle('show');
}

function closeSidebar() {
    sidebar.classList.remove('open');
    sidebarOverlay.classList.remove('show');
}

mobileMenuToggle?.addEventListener('click', toggleSidebar);
sidebarOverlay?.addEventListener('click', closeSidebar);

// 点击导航链接后关闭侧边栏（移动端）
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function() {
        if (window.innerWidth <= 768) {
            closeSidebar();
        }
    });
});
</script>
