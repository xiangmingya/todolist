<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$auth = new Auth();

// 如果已登录，重定向到仪表板
if ($auth->isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $result = $auth->login($username, $password);
    if ($result['success']) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = $result['message'];
    }
}

function sanitizeInput($data) {
    return htmlspecialchars(trim($data));
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户登录 - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1><?php echo APP_NAME; ?></h1>
                <p>登录您的账户</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="auth-form">
                <div class="form-group">
                    <label for="username">用户名或邮箱</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo isset($username) ? $username : ''; ?>"
                           placeholder="请输入用户名或邮箱">
                </div>

                <div class="form-group">
                    <label for="password">密码</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="请输入密码">
                </div>

                <button type="submit" class="btn btn-primary btn-block">登录</button>
            </form>

            <div class="auth-footer">
                <p>还没有账户？ <a href="register.php">立即注册</a></p>
            </div>
        </div>
    </div>
</body>
</html>
