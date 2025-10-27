# 部署指南 - 任务管理系统

本文档详细说明如何在不同环境中部署任务管理系统。

## 宝塔面板部署（推荐）

### 1. 环境准备

#### 1.1 宝塔面板要求
- 宝塔 Linux 面板 7.0 及以上版本
- 操作系统：CentOS 7+、Ubuntu 18.04+ 或 Debian 9+

#### 1.2 必要组件
- **Nginx** 1.18+ 或 **Apache** 2.4+
- **PHP** 7.4 或 8.0+
- **MySQL** 5.7+ 或 **MariaDB** 10.3+

### 2. 安装步骤

#### 步骤 1：创建网站
1. 登录宝塔面板
2. 点击 "网站" -> "添加站点"
3. 配置如下：
   - 域名：`yourdomain.com`（或 IP 地址）
   - 根目录：`/www/wwwroot/task_manager`
   - PHP 版本：PHP-74 或 PHP-80
   - 数据库：MySQL
   - FTP：可选

#### 步骤 2：上传代码
```bash
# 方法 1: 使用 Git
cd /www/wwwroot/task_manager
git clone <repository-url> .

# 方法 2: 使用 FTP
# 使用 FileZilla 或宝塔面板的文件管理器上传所有文件
```

#### 步骤 3：设置文件权限
```bash
cd /www/wwwroot/task_manager
chown -R www:www .
chmod -R 755 .
chmod 644 includes/config.php
```

#### 步骤 4：创建数据库
1. 在宝塔面板点击 "数据库" -> "添加数据库"
2. 配置：
   - 数据库名：`task_manager`
   - 用户名：`task_manager`
   - 密码：生成强密码
   - 访问权限：本地服务器
3. 记录数据库信息

#### 步骤 5：配置数据库连接
编辑 `includes/config.php`：
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'task_manager');      // 您的数据库用户名
define('DB_PASS', 'your_password');     // 您的数据库密码
define('DB_NAME', 'task_manager');      // 您的数据库名
```

#### 步骤 6：初始化数据库
1. 访问：`http://yourdomain.com/install.php`
2. 等待页面显示"数据库安装完成"
3. 点击"前往注册"链接

#### 步骤 7：注册管理员账户
1. 访问：`http://yourdomain.com/register.php`
2. 填写注册信息
3. 注册成功后登录系统

#### 步骤 8：安全设置
```bash
# 删除安装文件（重要！）
rm /www/wwwroot/task_manager/install.php

# 修改配置文件权限
chmod 400 includes/config.php
```

### 3. Nginx 配置优化

编辑网站配置（宝塔面板 -> 网站 -> 设置 -> 配置文件）：

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /www/wwwroot/task_manager;
    index index.php index.html;

    # 字符集
    charset utf-8;

    # 日志
    access_log /www/wwwlogs/task_manager_access.log;
    error_log /www/wwwlogs/task_manager_error.log;

    # 安全设置
    location ~ /\. {
        deny all;
    }

    location ~ /(includes|api)/.*\.php$ {
        deny all;
    }

    # PHP 处理
    location ~ \.php$ {
        fastcgi_pass unix:/tmp/php-cgi-74.sock;
        fastcgi_index index.php;
        include fastcgi.conf;
    }

    # 静态文件缓存
    location ~* \.(css|js|jpg|jpeg|png|gif|svg|ico)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

### 4. PHP 配置优化

在宝塔面板 -> 软件商店 -> PHP-7.4 -> 设置 -> 配置修改：

```ini
; 上传限制
upload_max_filesize = 10M
post_max_size = 10M

; 执行时间
max_execution_time = 300
max_input_time = 300

; 内存限制
memory_limit = 256M

; 时区设置
date.timezone = Asia/Shanghai

; 错误报告（生产环境关闭）
display_errors = Off
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
log_errors = On
```

### 5. SSL 证书配置（可选但推荐）

1. 在宝塔面板点击网站 -> SSL
2. 选择 "Let's Encrypt" 或上传证书
3. 开启 "强制 HTTPS"

---

## 传统 LAMP/LNMP 服务器部署

### 1. 安装依赖

#### Ubuntu/Debian
```bash
sudo apt update
sudo apt install nginx php7.4-fpm php7.4-mysql php7.4-mbstring php7.4-xml mysql-server
```

#### CentOS/RHEL
```bash
sudo yum install nginx php php-fpm php-mysqlnd php-mbstring php-xml mysql-server
```

### 2. 配置 MySQL
```bash
sudo mysql_secure_installation
sudo mysql -u root -p

CREATE DATABASE task_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'task_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON task_manager.* TO 'task_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. 部署代码
```bash
sudo mkdir -p /var/www/task_manager
cd /var/www/task_manager
sudo git clone <repository-url> .
sudo chown -R www-data:www-data .
```

### 4. 配置 Nginx
```bash
sudo nano /etc/nginx/sites-available/task_manager
```

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/task_manager;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    }

    location ~ /\. {
        deny all;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/task_manager /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## Docker 部署

### 1. 创建 Dockerfile
```dockerfile
FROM php:7.4-apache
RUN docker-php-ext-install mysqli pdo pdo_mysql
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html
```

### 2. 创建 docker-compose.yml
```yaml
version: '3.8'
services:
  web:
    build: .
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
    depends_on:
      - db
  db:
    image: mysql:5.7
    environment:
      MYSQL_ROOT_PASSWORD: rootpass
      MYSQL_DATABASE: task_manager
      MYSQL_USER: task_user
      MYSQL_PASSWORD: taskpass
    volumes:
      - db_data:/var/lib/mysql

volumes:
  db_data:
```

### 3. 启动容器
```bash
docker-compose up -d
```

---

## 本地开发环境

### 1. XAMPP (Windows/Mac/Linux)
1. 下载安装 XAMPP
2. 将项目复制到 `htdocs/task_manager`
3. 启动 Apache 和 MySQL
4. 访问 `http://localhost/task_manager`

### 2. MAMP (Mac/Windows)
1. 下载安装 MAMP
2. 将项目复制到 `htdocs/task_manager`
3. 启动服务器
4. 访问 `http://localhost:8888/task_manager`

### 3. PHP 内置服务器
```bash
cd task_manager
php -S localhost:8000
# 注意：需要单独配置 MySQL
```

---

## 性能优化建议

### 1. 数据库优化
```sql
-- 添加索引
ALTER TABLE tasks ADD INDEX idx_user_status (user_id, status);
ALTER TABLE tasks ADD INDEX idx_due_date (due_date);

-- 定期清理旧数据
DELETE FROM tasks WHERE status = 'completed' AND updated_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);
```

### 2. PHP OPcache
在 php.ini 中启用：
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
```

### 3. 启用浏览器缓存
已在 `.htaccess` 中配置

### 4. CDN 加速
将 Chart.js 等静态资源使用 CDN

---

## 备份策略

### 1. 数据库备份
```bash
# 手动备份
mysqldump -u task_user -p task_manager > backup_$(date +%Y%m%d).sql

# 宝塔面板：数据库 -> 选择数据库 -> 备份
```

### 2. 文件备份
```bash
tar -czf task_manager_$(date +%Y%m%d).tar.gz /www/wwwroot/task_manager
```

### 3. 自动备份脚本
```bash
#!/bin/bash
# /root/backup_task_manager.sh
DATE=$(date +%Y%m%d)
BACKUP_DIR="/root/backups"
DB_NAME="task_manager"
DB_USER="task_user"
DB_PASS="your_password"

# 创建备份目录
mkdir -p $BACKUP_DIR

# 备份数据库
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# 备份文件
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /www/wwwroot/task_manager

# 删除 30 天前的备份
find $BACKUP_DIR -mtime +30 -delete
```

设置定时任务：
```bash
crontab -e
# 每天凌晨 2 点执行备份
0 2 * * * /root/backup_task_manager.sh
```

---

## 故障排除

### 问题 1：数据库连接失败
```
解决方案：
1. 检查 includes/config.php 配置
2. 确认 MySQL 服务运行：systemctl status mysql
3. 检查防火墙设置
4. 验证数据库用户权限
```

### 问题 2：500 内部服务器错误
```
解决方案：
1. 检查 PHP 错误日志
2. 验证文件权限（755 for directories, 644 for files）
3. 检查 PHP 版本兼容性
4. 查看 Nginx/Apache 错误日志
```

### 问题 3：图表不显示
```
解决方案：
1. 检查网络连接（Chart.js CDN）
2. 查看浏览器控制台错误
3. 确认 JavaScript 未被阻止
4. 检查 Content-Security-Policy 设置
```

### 问题 4：会话丢失/频繁退出
```
解决方案：
1. 检查 PHP session 配置
2. 确认 session 目录权限
3. 增加 SESSION_LIFETIME 值
4. 检查服务器时间设置
```

---

## 安全检查清单

- [ ] 更改默认数据库用户名和密码
- [ ] 删除 install.php 文件
- [ ] 配置文件权限设置为 400 或 600
- [ ] 启用 HTTPS（SSL证书）
- [ ] 关闭 PHP 错误显示（生产环境）
- [ ] 启用防火墙
- [ ] 定期更新 PHP 和 MySQL
- [ ] 设置强密码策略
- [ ] 启用日志监控
- [ ] 配置定期备份

---

## 技术支持

如遇到问题，请检查：
1. README.md - 基本使用说明
2. 错误日志 - /www/wwwlogs/ 或 /var/log/nginx/
3. PHP 日志 - php-fpm 错误日志

---

祝部署顺利！🚀
