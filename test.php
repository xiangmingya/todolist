<?php
// 系统测试脚本
// 用于验证环境配置是否正确

echo "<h1>任务管理系统 - 环境测试</h1>";
echo "<hr>";

// 1. PHP 版本检查
echo "<h2>1. PHP 版本检查</h2>";
$phpVersion = phpversion();
echo "PHP 版本: " . $phpVersion . "<br>";
if (version_compare($phpVersion, '7.4.0', '>=')) {
    echo "<span style='color: green;'>✓ PHP 版本符合要求 (>= 7.4)</span><br>";
} else {
    echo "<span style='color: red;'>✗ PHP 版本过低，需要 7.4 或更高版本</span><br>";
}
echo "<br>";

// 2. 必要扩展检查
echo "<h2>2. PHP 扩展检查</h2>";
$requiredExtensions = ['mysqli', 'pdo_mysql', 'mbstring', 'session'];
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<span style='color: green;'>✓ {$ext} 已安装</span><br>";
    } else {
        echo "<span style='color: red;'>✗ {$ext} 未安装</span><br>";
    }
}
echo "<br>";

// 3. 配置文件检查
echo "<h2>3. 配置文件检查</h2>";
if (file_exists('includes/config.php')) {
    echo "<span style='color: green;'>✓ config.php 存在</span><br>";
    require_once 'includes/config.php';
    echo "数据库主机: " . DB_HOST . "<br>";
    echo "数据库名称: " . DB_NAME . "<br>";
    echo "应用名称: " . APP_NAME . "<br>";
} else {
    echo "<span style='color: red;'>✗ config.php 不存在</span><br>";
}
echo "<br>";

// 4. 数据库连接测试
echo "<h2>4. 数据库连接测试</h2>";
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        echo "<span style='color: red;'>✗ 数据库连接失败: " . $conn->connect_error . "</span><br>";
        echo "<p>请检查 includes/config.php 中的数据库配置</p>";
    } else {
        echo "<span style='color: green;'>✓ 数据库连接成功</span><br>";
        
        // 检查数据表
        $tables = ['users', 'tasks'];
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '{$table}'");
            if ($result && $result->num_rows > 0) {
                echo "<span style='color: green;'>✓ 数据表 {$table} 存在</span><br>";
            } else {
                echo "<span style='color: orange;'>! 数据表 {$table} 不存在，请运行 install.php</span><br>";
            }
        }
        $conn->close();
    }
} catch (Exception $e) {
    echo "<span style='color: red;'>✗ 数据库连接异常: " . $e->getMessage() . "</span><br>";
}
echo "<br>";

// 5. 文件权限检查
echo "<h2>5. 文件权限检查</h2>";
$files = [
    'includes/config.php',
    'includes/auth.php',
    'includes/database.php',
    'includes/functions.php'
];
foreach ($files as $file) {
    if (file_exists($file)) {
        if (is_readable($file)) {
            echo "<span style='color: green;'>✓ {$file} 可读</span><br>";
        } else {
            echo "<span style='color: red;'>✗ {$file} 不可读</span><br>";
        }
    } else {
        echo "<span style='color: red;'>✗ {$file} 不存在</span><br>";
    }
}
echo "<br>";

// 6. 目录结构检查
echo "<h2>6. 目录结构检查</h2>";
$directories = ['includes', 'css', 'js', 'api'];
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        echo "<span style='color: green;'>✓ 目录 {$dir}/ 存在</span><br>";
    } else {
        echo "<span style='color: red;'>✗ 目录 {$dir}/ 不存在</span><br>";
    }
}
echo "<br>";

// 7. 页面文件检查
echo "<h2>7. 页面文件检查</h2>";
$pages = ['index.php', 'login.php', 'register.php', 'dashboard.php', 'tasks.php', 'stats.php'];
foreach ($pages as $page) {
    if (file_exists($page)) {
        echo "<span style='color: green;'>✓ {$page} 存在</span><br>";
    } else {
        echo "<span style='color: red;'>✗ {$page} 不存在</span><br>";
    }
}
echo "<br>";

// 测试结果总结
echo "<h2>测试总结</h2>";
echo "<p>如果所有测试项都显示绿色 ✓，说明系统配置正确。</p>";
echo "<p>如果有橙色 ! 的项目，请运行 <a href='install.php'>install.php</a> 初始化数据库。</p>";
echo "<p>如果有红色 ✗ 的项目，请检查对应的配置或文件。</p>";
echo "<hr>";
echo "<p><strong>下一步：</strong></p>";
echo "<ul>";
echo "<li>如果数据表不存在：<a href='install.php'>运行数据库安装</a></li>";
echo "<li>如果所有测试通过：<a href='register.php'>注册新用户</a> 或 <a href='login.php'>登录</a></li>";
echo "</ul>";

// 安全提示
echo "<hr>";
echo "<p style='color: red;'><strong>安全提示：</strong> 在生产环境中请删除此测试文件 (test.php)！</p>";
?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h1 {
        color: #333;
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
    }
    h2 {
        color: #555;
        margin-top: 20px;
    }
    a {
        color: #007bff;
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }
</style>
