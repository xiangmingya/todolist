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
- 任务标签管理
- 优先级设置（低/中/高）
- 截止日期设置
- 任务搜索和筛选

### 3. 统计图表模块
- 任务完成情况统计（甜甜圈图）
- 标签分布饼图
- 优先级分布柱状图
- 状态分布柱状图

### 4. UI/UX 特性
- **Todoist Today 风格设计** 🎨
  - 采用 Todoist 配色方案（品牌红 #dc4c3e）
  - 左侧边栏 + 主内容区水平布局
  - 垂直导航菜单带图标
  - 圆形复选框设计
  - 悬停显示操作按钮
- 响应式布局（支持手机、平板、桌面）
  - 桌面端：280px 固定侧边栏
  - 移动端：滑出式侧边栏 + 遮罩层
- 实时通知提示
- 模态框交互
- 平滑动画效果
- 支持键盘快捷键（回车快速添加任务）

## 安装步骤

### 简单安装（推荐）

1. **上传文件**
   - 将所有文件上传到网站根目录

2. **运行安装向导**
   - 访问 `http://your-domain.com/install.php`
   - 按照界面提示填写数据库信息
   - 系统将自动创建数据库和数据表

3. **开始使用**
   - 访问 `http://your-domain.com/register.php` 注册账户
   - 登录后开始使用系统

### 本地开发环境

1. **环境要求**
   - PHP 7.4 或更高版本
   - MySQL 5.7 或更高版本
   - Apache/Nginx 服务器

2. **克隆项目**
   ```bash
   git clone <repository-url>
   cd task_manager
   ```

3. **运行安装向导**
   - 访问 `http://localhost/task_manager/install.php`
   - 填写数据库信息完成安装

4. **启动项目**
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
├── tags.php               # 标签管理页面
├── stats.php              # 统计图表页面
├── install.php            # 统一安装向导（数据库配置+安装）
├── README.md              # 项目文档
├── .gitignore             # Git 忽略文件
├── .htaccess              # Apache 配置
├── includes/              # 核心功能模块
│   ├── config.php         # 配置文件
│   ├── database.php       # 数据库连接类
│   ├── auth.php           # 用户认证类
│   ├── functions.php      # 任务管理类和工具函数
│   └── header.php         # 公共头部
├── css/                   # 样式文件
│   └── style.css          # 主样式文件
├── js/                    # JavaScript 文件
│   ├── script.js          # 主脚本文件
│   └── tags.js            # 标签管理脚本
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
    tag VARCHAR(50),
    due_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### tags 表（标签表）
```sql
CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    color VARCHAR(7) DEFAULT '#808080',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_tag (user_id, name)
);
```

**注意**: 系统使用单一数据库 `task_manager`，所有数据表统一管理。

## API 接口

### 任务 API (`/api/tasks.php`)

- **GET** - 获取任务列表或单个任务
  - 查询参数：`id`, `status`, `priority`, `tag`, `search`
  
- **POST** - 创建新任务
  - 请求体：`title`, `description`, `status`, `priority`, `tag`, `due_date`
  
- **PUT** - 更新任务
  - 请求体：`id`, `title`, `description`, `status`, `priority`, `tag`, `due_date`
  
- **DELETE** - 删除任务
  - 请求体：`id`

### 统计 API (`/api/stats.php`)

- **GET** - 获取任务统计数据
  - 返回：总任务数、状态分布、优先级分布、标签分布等

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
A: 请确保数据库服务器运行正常，并检查填写的数据库用户名和密码是否正确。

**Q: 无法访问页面？**
A: 确保 PHP 版本 >= 7.4，并且 Web 服务器配置正确。

**Q: 安装向导显示错误？**
A: 检查 includes/ 目录是否有写入权限，系统需要写入配置文件。

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
- 左侧边栏 + 主内容区水平布局（现代化设计）
- 简洁清爽的白色背景
- 垂直导航菜单带 SVG 图标
- 圆形复选框（Todoist 标志性设计）
- 细线分隔符
- 悬停显示操作按钮
- Emoji 图标增强视觉效果
- 8px 圆角设计
- 扁平化风格
- 响应式侧边栏（移动端滑出式）

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
