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
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($password !== $confirmPassword) {
        $error = '两次输入的密码不一致';
    } else {
        $result = $auth->register($username, $email, $password);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
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
    <title>用户注册 - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1><?php echo APP_NAME; ?></h1>
                <p>创建新账户</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                    <a href="login.php">立即登录</a>
                </div>
            <?php else: ?>
                <form method="POST" action="" class="auth-form">
                    <div class="form-group">
                        <label for="username">用户名</label>
                        <input type="text" id="username" name="username" required 
                               value="<?php echo isset($username) ? $username : ''; ?>"
                               placeholder="请输入用户名">
                    </div>

                    <div class="form-group">
                        <label for="email">邮箱</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo isset($email) ? $email : ''; ?>"
                               placeholder="请输入邮箱地址">
                    </div>

                    <div class="form-group">
                        <label for="password">密码</label>
                        <input type="password" id="password" name="password" required 
                               placeholder="至少6个字符">
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">确认密码</label>
                        <input type="password" id="confirm_password" name="confirm_password" required 
                               placeholder="再次输入密码">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">注册</button>
                </form>

                <div class="auth-footer">
                    <p>已有账户？ <a href="login.php">立即登录</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
