<?php
// 配置向导 - 帮助用户快速设置系统

$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step == 1) {
    $dbHost = $_POST['db_host'] ?? 'localhost';
    $dbUser = $_POST['db_user'] ?? '';
    $dbPass = $_POST['db_pass'] ?? '';
    $dbName = $_POST['db_name'] ?? '';

    if (empty($dbUser) || empty($dbName)) {
        $error = '数据库用户名和数据库名不能为空';
    } else {
        // 测试数据库连接
        $testConn = @new mysqli($dbHost, $dbUser, $dbPass);
        if ($testConn->connect_error) {
            $error = '数据库连接失败: ' . $testConn->connect_error;
        } else {
            // 连接成功，写入配置文件
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
                header('Location: setup.php?step=2');
                exit;
            } else {
                $error = '无法写入配置文件，请检查文件权限';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>系统配置向导</title>
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
        
        .setup-container {
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
        }
        
        .btn:hover {
            background: #5568d3;
        }
        
        .btn-block {
            display: block;
            width: 100%;
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
    <div class="setup-container">
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
                    <div class="step-label">初始化数据库</div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-label">完成</div>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="info-box">
                <h3>开始之前</h3>
                <p>请确保您已经：</p>
                <ul>
                    <li>创建了 MySQL 数据库</li>
                    <li>准备好数据库用户名和密码</li>
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
                    <input type="text" id="db_user" name="db_user" required>
                </div>

                <div class="form-group">
                    <label for="db_pass">数据库密码</label>
                    <input type="password" id="db_pass" name="db_pass">
                    <div class="help-text">如果没有密码可以留空</div>
                </div>

                <div class="form-group">
                    <label for="db_name">数据库名称</label>
                    <input type="text" id="db_name" name="db_name" value="task_manager" required>
                    <div class="help-text">请确保此数据库已创建</div>
                </div>

                <button type="submit" class="btn btn-block">下一步</button>
            </form>

        <?php elseif ($step == 2): ?>
            <h1>初始化数据库</h1>
            <p class="subtitle">创建必要的数据表</p>
            
            <div class="steps">
                <div class="step completed">
                    <div class="step-number">✓</div>
                    <div class="step-label">数据库配置</div>
                </div>
                <div class="step active">
                    <div class="step-number">2</div>
                    <div class="step-label">初始化数据库</div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-label">完成</div>
                </div>
            </div>

            <div class="info-box">
                <h3>准备初始化</h3>
                <p>点击下面的按钮将会：</p>
                <ul>
                    <li>创建用户表 (users)</li>
                    <li>创建任务表 (tasks)</li>
                    <li>设置必要的索引和关系</li>
                </ul>
            </div>

            <a href="install.php" class="btn btn-block">开始初始化</a>

        <?php elseif ($step == 3): ?>
            <div class="text-center">
                <div class="success-icon">✓</div>
                <h1>配置完成！</h1>
                <p class="subtitle">您的系统已经准备就绪</p>
            </div>

            <div class="info-box">
                <h3>下一步</h3>
                <p>现在您可以：</p>
                <ul>
                    <li>注册第一个用户账户</li>
                    <li>登录系统开始使用</li>
                </ul>
            </div>

            <a href="register.php" class="btn btn-block">注册新用户</a>
            <br><br>
            <div class="text-center">
                <a href="login.php">已有账户？立即登录</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
