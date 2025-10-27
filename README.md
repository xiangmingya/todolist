# 任务管理系统

一个功能完整的任务管理系统，采用 PHP + MySQL 开发，支持用户认证、任务管理和数据统计功能。

## 技术栈

- **前端**: HTML5 + CSS3 + JavaScript (原生)
- **图表**: Chart.js 4.4.0
- **后端**: PHP 7.4+
- **数据库**: MySQL 5.7+
- **部署**: 宝塔面板（或任何支持 PHP 的服务器）

## 主要功能

### 1. 用户认证模块
- 用户注册/登录
- 会话管理
- 密码加密存储（使用 PHP password_hash）

### 2. 任务管理模块
- 任务的增删改查（CRUD）
- 任务状态管理（待办/进行中/已完成）
- 任务分类和标签
- 优先级设置（低/中/高）
- 截止日期设置
- 任务搜索和筛选

### 3. 统计图表模块
- 任务完成情况统计（甜甜圈图）
- 分类分布饼图
- 优先级分布柱状图
- 状态分布柱状图

### 4. UI/UX 特性
- **Todoist Today 风格设计** 🎨
  - 采用 Todoist 配色方案（品牌红 #dc4c3e）
  - 简洁清爽的布局
  - 圆形复选框设计
  - 悬停显示操作按钮
- 响应式布局（支持手机、平板、桌面）
- 实时通知提示
- 模态框交互
- 平滑动画效果
- 支持键盘快捷键（回车快速添加任务）

## 安装步骤

### 方法一：宝塔面板部署

1. **创建网站**
   - 登录宝塔面板
   - 创建新网站，设置域名和根目录
   - PHP 版本选择 7.4 或更高

2. **上传代码**
   - 将所有文件上传到网站根目录

3. **创建数据库**
   - 在宝塔面板创建 MySQL 数据库
   - 记录数据库名、用户名和密码

4. **配置数据库连接**
   - 编辑 `includes/config.php`
   - 修改数据库配置信息：
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_db_user');
     define('DB_PASS', 'your_db_password');
     define('DB_NAME', 'task_manager');
     ```

5. **初始化数据库**
   - 访问 `http://your-domain.com/install.php`
   - 等待数据库表创建完成

6. **完成安装**
   - 访问 `http://your-domain.com/register.php` 注册账户
   - 开始使用系统

### 方法二：本地开发环境

1. **环境要求**
   - PHP 7.4 或更高版本
   - MySQL 5.7 或更高版本
   - Apache/Nginx 服务器

2. **克隆项目**
   ```bash
   git clone <repository-url>
   cd task_manager
   ```

3. **配置数据库**
   - 创建 MySQL 数据库
   - 修改 `includes/config.php` 中的数据库配置

4. **初始化数据库**
   - 访问 `http://localhost/task_manager/install.php`

5. **启动项目**
   - 访问 `http://localhost/task_manager`

## 项目结构

```
task_manager/
├── index.php              # 入口文件
├── login.php              # 登录页面
├── register.php           # 注册页面
├── logout.php             # 退出登录
├── dashboard.php          # 仪表板
├── tasks.php              # 任务管理页面
├── stats.php              # 统计图表页面
├── install.php            # 数据库安装脚本
├── README.md              # 项目文档
├── .gitignore             # Git 忽略文件
├── includes/              # 核心功能模块
│   ├── config.php         # 配置文件
│   ├── database.php       # 数据库连接类
│   ├── auth.php           # 用户认证类
│   ├── functions.php      # 任务管理类和工具函数
│   └── header.php         # 公共头部
├── css/                   # 样式文件
│   └── style.css          # 主样式文件
├── js/                    # JavaScript 文件
│   └── script.js          # 主脚本文件
└── api/                   # API 接口
    ├── tasks.php          # 任务 API
    └── stats.php          # 统计 API
```

## 数据库结构

### users 表（用户表）
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### tasks 表（任务表）
```sql
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    category VARCHAR(50),
    due_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## API 接口

### 任务 API (`/api/tasks.php`)

- **GET** - 获取任务列表或单个任务
  - 查询参数：`id`, `status`, `priority`, `category`, `search`
  
- **POST** - 创建新任务
  - 请求体：`title`, `description`, `status`, `priority`, `category`, `due_date`
  
- **PUT** - 更新任务
  - 请求体：`id`, `title`, `description`, `status`, `priority`, `category`, `due_date`
  
- **DELETE** - 删除任务
  - 请求体：`id`

### 统计 API (`/api/stats.php`)

- **GET** - 获取任务统计数据
  - 返回：总任务数、状态分布、优先级分布、分类分布等

## 使用说明

1. **注册账户**：首次访问需要注册新账户
2. **登录系统**：使用用户名/邮箱和密码登录
3. **查看仪表板**：登录后查看任务概览和统计信息
4. **管理任务**：
   - 在仪表板快速添加任务
   - 进入任务管理页面进行详细操作
   - 使用筛选功能查找特定任务
5. **查看统计**：访问统计图表页面查看数据分析

## 安全特性

- 密码使用 PHP `password_hash()` 加密存储
- SQL 查询使用预处理语句防止注入
- 会话管理和超时控制
- 用户输入数据过滤和验证
- XSS 防护（使用 `htmlspecialchars()`）

## 浏览器支持

- Chrome (推荐)
- Firefox
- Safari
- Edge
- 其他现代浏览器

## 常见问题

**Q: 安装时数据库连接失败？**
A: 检查 `includes/config.php` 中的数据库配置是否正确。

**Q: 无法访问页面？**
A: 确保 PHP 版本 >= 7.4，并且 Web 服务器配置正确。

**Q: 图表不显示？**
A: 确保网络连接正常，Chart.js CDN 可以访问。

**Q: 会话过期太快？**
A: 修改 `includes/config.php` 中的 `SESSION_LIFETIME` 常量。

## 设计风格

本项目采用了 **Todoist Today** 页面的设计风格和配色方案，具体包括：

### 🎨 配色方案
- **主色调**: Todoist Red (#dc4c3e) - 品牌标识色
- **成功色**: #058527 - 完成状态
- **警告色**: #ff9a14 - 警告提示
- **信息色**: #246fe0 - 信息展示
- **背景色**: 白色 (#ffffff) 和浅灰 (#fafafa)
- **文字色**: #202020 (主文字), #808080 (次要文字)

### ✨ 设计特点
- 简洁清爽的白色背景
- 圆形复选框（Todoist 标志性设计）
- 细线分隔符
- 悬停显示操作按钮
- Emoji 图标增强视觉效果
- 8px 圆角设计
- 扁平化风格

详细设计文档请查看: [TODOIST_DESIGN.md](TODOIST_DESIGN.md)

## 后续优化建议

- [ ] 添加任务附件上传功能
- [ ] 实现任务提醒通知
- [ ] 支持多人协作和任务分配
- [ ] 添加任务评论功能
- [ ] 实现数据导出（Excel/PDF）
- [ ] 添加深色主题切换
- [ ] 移动端原生应用
- [x] Todoist Today 风格设计（已完成）

## 许可证

MIT License

## 作者

开发时间：2024

---

如有问题或建议，欢迎反馈！
