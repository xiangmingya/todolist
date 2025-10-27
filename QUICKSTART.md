# 快速开始指南

## 5 分钟快速部署

### 方法一：使用配置向导（推荐新手）

1. **上传所有文件到服务器**
   ```
   将所有文件上传到网站根目录
   ```

2. **访问配置向导**
   ```
   http://yourdomain.com/setup.php
   ```

3. **填写数据库信息**
   - 数据库主机：localhost
   - 数据库用户名：您的数据库用户名
   - 数据库密码：您的数据库密码
   - 数据库名称：task_manager

4. **初始化数据库**
   - 点击"开始初始化"按钮

5. **注册账户**
   - 创建第一个管理员账户

6. **开始使用**
   - 登录系统，开始管理任务

---

### 方法二：手动配置（推荐有经验的用户）

#### 第 1 步：创建数据库

**宝塔面板：**
```
数据库 -> 添加数据库
- 数据库名：task_manager
- 用户名：task_manager
- 密码：（生成强密码）
```

**命令行：**
```bash
mysql -u root -p
CREATE DATABASE task_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'task_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON task_manager.* TO 'task_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### 第 2 步：导入数据库（可选）

如果您喜欢使用 SQL 文件导入：

**宝塔面板：**
```
数据库 -> task_manager -> 导入 -> 选择 database.sql
```

**命令行：**
```bash
mysql -u task_user -p task_manager < database.sql
```

或者使用内置安装脚本：
```
访问 http://yourdomain.com/install.php
```

#### 第 3 步：配置数据库连接

编辑 `includes/config.php`：

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'task_manager');  // 您的数据库用户名
define('DB_PASS', 'your_password'); // 您的数据库密码
define('DB_NAME', 'task_manager');  // 您的数据库名
```

#### 第 4 步：测试环境

访问测试页面：
```
http://yourdomain.com/test.php
```

检查所有项目是否显示绿色 ✓

#### 第 5 步：注册使用

```
http://yourdomain.com/register.php
```

---

## 宝塔面板快速部署

### 1. 创建网站
```
网站 -> 添加站点
- 域名：yourdomain.com
- 根目录：/www/wwwroot/task_manager
- PHP 版本：PHP-7.4
- 数据库：MySQL
```

### 2. 上传代码
```
文件 -> /www/wwwroot/task_manager -> 上传
或使用 Git：
cd /www/wwwroot/task_manager
git clone <repository-url> .
```

### 3. 设置权限
```bash
cd /www/wwwroot/task_manager
chown -R www:www .
chmod -R 755 .
```

### 4. 配置数据库
```
访问 http://yourdomain.com/setup.php
按照向导完成配置
```

### 5. 完成
```
删除 test.php 和 setup.php（生产环境）
访问系统开始使用
```

---

## Docker 快速部署

### 1. 创建 docker-compose.yml

```yaml
version: '3.8'
services:
  web:
    image: php:7.4-apache
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
    depends_on:
      - db
    environment:
      - DB_HOST=db
      - DB_USER=root
      - DB_PASS=rootpass
      - DB_NAME=task_manager
    command: >
      bash -c "docker-php-ext-install mysqli pdo pdo_mysql 
      && apache2-foreground"
      
  db:
    image: mysql:5.7
    environment:
      MYSQL_ROOT_PASSWORD: rootpass
      MYSQL_DATABASE: task_manager
    volumes:
      - db_data:/var/lib/mysql
      - ./database.sql:/docker-entrypoint-initdb.d/init.sql

volumes:
  db_data:
```

### 2. 启动服务

```bash
docker-compose up -d
```

### 3. 访问系统

```
http://localhost:8080
```

---

## 本地开发环境

### XAMPP (Windows/Mac/Linux)

1. **安装 XAMPP**
   - 下载：https://www.apachefriends.org/

2. **部署项目**
   ```
   将项目文件夹复制到：
   C:\xampp\htdocs\task_manager (Windows)
   /Applications/XAMPP/htdocs/task_manager (Mac)
   ```

3. **启动服务**
   - 打开 XAMPP Control Panel
   - 启动 Apache 和 MySQL

4. **创建数据库**
   - 访问 http://localhost/phpmyadmin
   - 新建数据库：task_manager

5. **访问系统**
   ```
   http://localhost/task_manager/setup.php
   ```

---

## 常见问题快速解决

### ❓ 数据库连接失败

**解决方案：**
1. 检查 `includes/config.php` 配置是否正确
2. 确认 MySQL 服务已启动
3. 验证数据库用户名和密码
4. 检查数据库是否已创建

**测试连接：**
```bash
mysql -u task_user -p task_manager
```

### ❓ 页面显示 500 错误

**解决方案：**
1. 检查 PHP 错误日志
2. 确认 PHP 版本 >= 7.4
3. 验证文件权限（755 for folders, 644 for files）
4. 检查所有文件是否上传完整

**查看错误日志：**
```bash
# 宝塔面板
/www/wwwlogs/error.log

# 传统服务器
/var/log/apache2/error.log
/var/log/nginx/error.log
```

### ❓ 无法登录/注册

**解决方案：**
1. 确认数据库表已创建（运行 install.php）
2. 检查 session 目录权限
3. 清除浏览器缓存和 Cookie
4. 检查 PHP session 配置

**验证数据表：**
```sql
USE task_manager;
SHOW TABLES;
-- 应该显示：users, tasks
```

### ❓ 图表不显示

**解决方案：**
1. 检查网络连接（Chart.js 使用 CDN）
2. 查看浏览器控制台错误
3. 确认 JavaScript 未被阻止
4. 检查防火墙/安全组设置

**替代方案：**
下载 Chart.js 到本地，修改 `stats.php` 中的引用

---

## 安全建议

### ✅ 部署后必做

1. **删除测试文件**
   ```bash
   rm test.php setup.php install.php
   ```

2. **修改配置文件权限**
   ```bash
   chmod 400 includes/config.php
   ```

3. **关闭错误显示**
   ```php
   // includes/config.php
   error_reporting(0);
   ini_set('display_errors', 0);
   ```

4. **启用 HTTPS**
   - 在宝塔面板配置 SSL 证书
   - 强制 HTTPS 访问

5. **定期备份**
   - 设置自动备份数据库
   - 备份网站文件

---

## 下一步

### 🎯 开始使用

1. **创建第一个任务**
   - 登录后在仪表板快速添加

2. **探索功能**
   - 任务管理：增删改查任务
   - 筛选搜索：按状态、优先级筛选
   - 统计图表：查看数据分析

3. **自定义设置**
   - 修改应用名称（`includes/config.php`）
   - 调整会话超时时间
   - 自定义样式（`css/style.css`）

### 📚 深入了解

- 阅读完整文档：`README.md`
- 部署指南：`DEPLOYMENT.md`
- 数据库结构：`database.sql`

---

## 获取帮助

遇到问题？

1. 查看错误日志
2. 运行 `test.php` 诊断
3. 阅读 FAQ 部分
4. 查看项目文档

---

**祝您使用愉快！** 🎉
