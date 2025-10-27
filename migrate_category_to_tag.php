<?php
require_once 'includes/config.php';

// 创建数据库连接
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

echo "开始迁移：将 category 字段重命名为 tag...<br><br>";

// 检查 category 字段是否存在
$checkSql = "SHOW COLUMNS FROM tasks LIKE 'category'";
$result = $conn->query($checkSql);

if ($result->num_rows > 0) {
    // category 字段存在，进行重命名
    $sql = "ALTER TABLE tasks CHANGE COLUMN category tag VARCHAR(50)";
    
    if ($conn->query($sql) === TRUE) {
        echo "✓ 成功将 category 字段重命名为 tag<br>";
    } else {
        echo "✗ 重命名失败: " . $conn->error . "<br>";
    }
} else {
    // 检查 tag 字段是否已存在
    $checkTagSql = "SHOW COLUMNS FROM tasks LIKE 'tag'";
    $tagResult = $conn->query($checkTagSql);
    
    if ($tagResult->num_rows > 0) {
        echo "✓ tag 字段已存在，无需迁移<br>";
    } else {
        echo "✗ category 和 tag 字段都不存在，请检查数据库结构<br>";
    }
}

echo "<br>迁移完成！<br>";
echo "<a href='dashboard.php'>返回首页</a>";

$conn->close();
