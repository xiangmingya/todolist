<?php
// 数据库配置
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'task_manager');

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
