<?php
// 数据库安装脚本 - 统一安装配置
session_start();

$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

// 处理配置提交
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step == 1) {
    $dbHost = $_POST['db_host'] ?? 'localhost';
    $dbUser = $_POST['db_user'] ?? '';
    $dbPass = $_POST['db_pass'] ?? '';
    $dbName = $_POST['db_name'] ?? 'task_manager';

    if (empty($dbUser) || empty($dbName)) {
        $error = '数据库用户名和数据库名不能为空';
    } else {
        // 测试数据库连接
        $testConn = @new mysqli($dbHost, $dbUser, $dbPass);
        if ($testConn->connect_error) {
            $error = '数据库连接失败: ' . $testConn->connect_error;
        } else {
            // 保存配置到session
            $_SESSION['db_config'] = [
                'host' => $dbHost,
                'user' => $dbUser,
                'pass' => $dbPass,
                'name' => $dbName
            ];
            
            // 写入配置文件
            $configContent = <<<PHP
<?php
// 数据库配置
define('DB_HOST', '{$dbHost}');
define('DB_USER', '{$dbUser}');
define('DB_PASS', '{$dbPass}');
define('DB_NAME', '{$dbName}');

// 应用配置
define('APP_NAME', '任务管理系统');
define('APP_URL', 'http://localhost');
define('TIMEZONE', 'Asia/Shanghai');

// 会话配置
define('SESSION_LIFETIME', 7200); // 2小时

// 设置时区
date_default_timezone_set(TIMEZONE);

// 错误报告设置（生产环境请关闭）
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 会话配置
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
session_set_cookie_params(SESSION_LIFETIME);

PHP;

            if (file_put_contents('includes/config.php', $configContent)) {
                header('Location: install.php?step=2');
                exit;
            } else {
                $error = '无法写入配置文件，请检查文件权限';
            }
        }
    }
}

// 处理数据库安装
if ($step == 2 && isset($_SESSION['db_config'])) {
    $config = $_SESSION['db_config'];
    
    try {
        $conn = new mysqli($config['host'], $config['user'], $config['pass']);
        
        if ($conn->connect_error) {
            throw new Exception("连接失败: " . $conn->connect_error);
        }
        
        // 创建数据库
        $sql = "CREATE DATABASE IF NOT EXISTS " . $config['name'] . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if (!$conn->query($sql)) {
            throw new Exception("数据库创建失败: " . $conn->error);
        }
        
        $conn->select_db($config['name']);
        
        // 创建用户表
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        if (!$conn->query($sql)) {
            throw new Exception("用户表创建失败: " . $conn->error);
        }
        
        // 创建任务表
        $sql = "CREATE TABLE IF NOT EXISTS tasks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
            priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
            tag VARCHAR(50),
            due_date DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_status (status),
            INDEX idx_priority (priority),
            INDEX idx_due_date (due_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        if (!$conn->query($sql)) {
            throw new Exception("任务表创建失败: " . $conn->error);
        }
        
        // 创建标签表
        $sql = "CREATE TABLE IF NOT EXISTS tags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            name VARCHAR(50) NOT NULL,
            color VARCHAR(7) DEFAULT '#808080',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_tag (user_id, name),
            INDEX idx_user_id (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        if (!$conn->query($sql)) {
            throw new Exception("标签表创建失败: " . $conn->error);
        }
        
        $conn->close();
        $success = true;
        unset($_SESSION['db_config']);
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>系统安装向导</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            max-width: 600px;
            width: 100%;
            padding: 2rem;
        }
        
        h1 {
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 1.75rem;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 2rem;
        }
        
        .steps {
            display: flex;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .step {
            flex: 1;
            text-align: center;
            position: relative;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            background: #e0e0e0;
            color: #999;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-weight: bold;
        }
        
        .step.active .step-number {
            background: #667eea;
            color: white;
        }
        
        .step.completed .step-number {
            background: #28a745;
            color: white;
        }
        
        .step-label {
            font-size: 0.875rem;
            color: #666;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }
        
        input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
        }
        
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .help-text {
            font-size: 0.875rem;
            color: #666;
            margin-top: 0.25rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s;
            width: 100%;
            text-align: center;
        }
        
        .btn:hover {
            background: #5568d3;
        }
        
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196f3;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }
        
        .info-box h3 {
            margin-bottom: 0.5rem;
            color: #1976d2;
        }
        
        .info-box ul {
            margin-left: 1.5rem;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: #28a745;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 1rem;
        }
        
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($step == 1): ?>
            <h1>欢迎使用任务管理系统</h1>
            <p class="subtitle">让我们开始配置您的系统</p>
            
            <div class="steps">
                <div class="step active">
                    <div class="step-number">1</div>
                    <div class="step-label">数据库配置</div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-label">安装数据库</div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-label">完成</div>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="info-box">
                <h3>开始之前</h3>
                <p>请确保您已经：</p>
                <ul>
                    <li>准备好 MySQL 数据库服务器</li>
                    <li>知道数据库用户名和密码</li>
                    <li>知道数据库主机地址（通常是 localhost）</li>
                </ul>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label for="db_host">数据库主机</label>
                    <input type="text" id="db_host" name="db_host" value="localhost" required>
                    <div class="help-text">通常是 localhost 或 127.0.0.1</div>
                </div>

                <div class="form-group">
                    <label for="db_user">数据库用户名</label>
                    <input type="text" id="db_user" name="db_user" value="root" required>
                </div>

                <div class="form-group">
                    <label for="db_pass">数据库密码</label>
                    <input type="password" id="db_pass" name="db_pass">
                    <div class="help-text">如果没有密码可以留空</div>
                </div>

                <div class="form-group">
                    <label for="db_name">数据库名称</label>
                    <input type="text" id="db_name" name="db_name" value="task_manager" required>
                    <div class="help-text">系统将自动创建此数据库</div>
                </div>

                <button type="submit" class="btn">下一步</button>
            </form>

        <?php elseif ($step == 2 && !$success): ?>
            <h1>正在安装数据库...</h1>
            <p class="subtitle">请稍候，正在创建数据表</p>
            
            <div class="steps">
                <div class="step completed">
                    <div class="step-number">✓</div>
                    <div class="step-label">数据库配置</div>
                </div>
                <div class="step active">
                    <div class="step-number">2</div>
                    <div class="step-label">安装数据库</div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-label">完成</div>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <a href="install.php?step=1" class="btn">返回重试</a>
            <?php endif; ?>

        <?php elseif ($step == 2 && $success): ?>
            <div class="text-center">
                <div class="success-icon">✓</div>
                <h1>安装完成！</h1>
                <p class="subtitle">数据库已成功创建</p>
            </div>

            <div class="info-box">
                <h3>安装成功</h3>
                <p>已成功创建以下数据表：</p>
                <ul>
                    <li>用户表 (users)</li>
                    <li>任务表 (tasks)</li>
                    <li>标签表 (tags)</li>
                </ul>
            </div>

            <div class="info-box">
                <h3>下一步</h3>
                <p>现在您可以：</p>
                <ul>
                    <li>注册第一个用户账户</li>
                    <li>登录系统开始使用</li>
                </ul>
            </div>

            <a href="register.php" class="btn">注册新用户</a>
            <br><br>
            <div class="text-center">
                <a href="login.php">已有账户？立即登录</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
