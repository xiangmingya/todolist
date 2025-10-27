# 项目文件清单

## 📁 项目结构

```
task_manager/
├── 📄 核心页面文件
│   ├── index.php              # 入口文件（自动跳转）
│   ├── login.php              # 用户登录页面
│   ├── register.php           # 用户注册页面
│   ├── logout.php             # 退出登录处理
│   ├── dashboard.php          # 主仪表板页面
│   ├── tasks.php              # 任务管理页面
│   └── stats.php              # 统计图表页面
│
├── 📄 安装和配置文件
│   ├── install.php            # 数据库安装脚本
│   ├── setup.php              # 配置向导（推荐新手使用）
│   ├── test.php               # 环境测试脚本
│   └── database.sql           # SQL 数据库结构文件
│
├── 📁 includes/               # 核心功能模块
│   ├── config.php             # 系统配置文件
│   ├── database.php           # 数据库连接类
│   ├── auth.php               # 用户认证类
│   ├── functions.php          # 任务管理类和工具函数
│   └── header.php             # 公共页面头部
│
├── 📁 api/                    # RESTful API 接口
│   ├── tasks.php              # 任务 CRUD API (GET/POST/PUT/DELETE)
│   └── stats.php              # 统计数据 API
│
├── 📁 css/                    # 样式文件
│   └── style.css              # 主样式表（响应式设计）
│
├── 📁 js/                     # JavaScript 文件
│   └── script.js              # 主脚本文件（AJAX 交互）
│
├── 📄 文档文件
│   ├── README.md              # 项目说明文档
│   ├── QUICKSTART.md          # 快速开始指南
│   ├── DEPLOYMENT.md          # 详细部署指南
│   └── FILES.md               # 本文件（文件清单）
│
└── 📄 配置文件
    ├── .gitignore             # Git 忽略文件配置
    └── .htaccess              # Apache 配置文件
```

---

## 📄 详细文件说明

### 核心页面文件

#### index.php
- **功能**：应用入口，自动判断登录状态
- **跳转逻辑**：
  - 已登录 → dashboard.php
  - 未登录 → login.php
- **大小**：~0.2 KB

#### login.php
- **功能**：用户登录界面
- **特性**：
  - 支持用户名或邮箱登录
  - 密码验证
  - 错误提示
  - 记住登录状态
- **大小**：~2.3 KB

#### register.php
- **功能**：用户注册界面
- **验证**：
  - 用户名唯一性
  - 邮箱格式和唯一性
  - 密码强度（最少6位）
  - 密码确认匹配
- **大小**：~3.5 KB

#### logout.php
- **功能**：退出登录，清除会话
- **操作**：
  - 销毁 session
  - 重定向到登录页
- **大小**：~0.1 KB

#### dashboard.php
- **功能**：用户主仪表板
- **显示内容**：
  - 任务统计卡片（总数、待办、进行中、已完成）
  - 快速添加任务表单
  - 待办任务列表（最近5条）
  - 逾期任务提醒
- **大小**：~7.2 KB

#### tasks.php
- **功能**：完整任务管理页面
- **功能模块**：
  - 任务列表展示（表格视图）
  - 新建/编辑任务（模态框）
  - 删除任务（确认对话框）
  - 任务筛选（状态、优先级、分类、搜索）
  - 任务排序
- **大小**：~12 KB

#### stats.php
- **功能**：统计图表页面
- **图表类型**：
  - 甜甜圈图：任务完成率
  - 柱状图：优先级分布
  - 饼图：分类分布
  - 柱状图：状态分布
- **依赖**：Chart.js 4.4.0 (CDN)
- **大小**：~6.6 KB

---

### 安装和配置文件

#### install.php
- **功能**：数据库自动安装脚本
- **操作**：
  - 创建数据库（如不存在）
  - 创建 users 表
  - 创建 tasks 表
  - 设置索引和外键
- **使用**：首次部署时访问一次
- **安全**：生产环境应删除
- **大小**：~2.0 KB

#### setup.php
- **功能**：可视化配置向导
- **步骤**：
  1. 数据库配置
  2. 初始化数据库
  3. 完成设置
- **优势**：适合新手，无需手动编辑文件
- **安全**：配置完成后应删除
- **大小**：~11 KB

#### test.php
- **功能**：环境诊断工具
- **检查项目**：
  - PHP 版本
  - PHP 扩展
  - 数据库连接
  - 文件权限
  - 目录结构
  - 数据表存在性
- **用途**：故障排查
- **安全**：生产环境应删除
- **大小**：~5.0 KB

#### database.sql
- **功能**：完整数据库结构 SQL
- **内容**：
  - 建库语句
  - 建表语句（users, tasks）
  - 索引定义
  - 统计视图
  - 存储过程
  - 示例数据（可选）
- **使用**：可导入或参考
- **大小**：~5.3 KB

---

### 核心功能模块 (includes/)

#### config.php
- **功能**：系统配置文件
- **配置项**：
  - 数据库连接信息
  - 应用基本设置
  - 时区设置
  - 会话配置
  - 错误报告
- **安全**：应设置只读权限（400）
- **大小**：~0.6 KB

#### database.php
- **功能**：数据库连接管理类
- **设计模式**：单例模式
- **方法**：
  - `getInstance()` - 获取实例
  - `getConnection()` - 获取连接
  - `query()` - 执行查询
  - `prepare()` - 预处理语句
  - `escape()` - 转义字符串
  - `lastInsertId()` - 获取最后插入ID
- **大小**：~1.5 KB

#### auth.php
- **功能**：用户认证管理类
- **方法**：
  - `register()` - 用户注册
  - `login()` - 用户登录
  - `logout()` - 退出登录
  - `isLoggedIn()` - 检查登录状态
  - `requireLogin()` - 强制登录
  - `getUserId()` - 获取用户ID
  - `getUsername()` - 获取用户名
  - `getEmail()` - 获取邮箱
- **安全特性**：
  - password_hash 加密
  - 会话超时检查
  - SQL 注入防护
- **大小**：~4.1 KB

#### functions.php
- **功能**：任务管理类和工具函数
- **TaskManager 类方法**：
  - `createTask()` - 创建任务
  - `getTasks()` - 获取任务列表（带筛选）
  - `getTaskById()` - 获取单个任务
  - `updateTask()` - 更新任务
  - `deleteTask()` - 删除任务
  - `getTaskStats()` - 获取统计数据
  - `getCategories()` - 获取分类列表
- **工具函数**：
  - `sanitizeInput()` - 输入清理
  - `formatDate()` - 日期格式化
  - `getStatusLabel()` - 状态标签
  - `getPriorityLabel()` - 优先级标签
  - `getStatusColor()` - 状态颜色
  - `getPriorityColor()` - 优先级颜色
- **大小**：~8.5 KB

#### header.php
- **功能**：公共页面头部
- **包含**：
  - 应用标题
  - 导航菜单
  - 用户信息
  - 退出按钮
- **大小**：~0.6 KB

---

### API 接口 (api/)

#### tasks.php
- **功能**：任务 RESTful API
- **支持方法**：
  - `GET` - 获取任务列表或单个任务
    - 查询参数：id, status, priority, category, search
  - `POST` - 创建新任务
  - `PUT` - 更新任务
  - `DELETE` - 删除任务
- **认证**：需要登录
- **响应格式**：JSON
- **大小**：~4.3 KB

#### stats.php
- **功能**：统计数据 API
- **方法**：GET
- **返回数据**：
  - 总任务数
  - 按状态统计
  - 按优先级统计
  - 按分类统计
  - 今日到期
  - 逾期任务
- **认证**：需要登录
- **响应格式**：JSON
- **大小**：~0.6 KB

---

### 前端资源

#### css/style.css
- **功能**：主样式表
- **特性**：
  - CSS 变量（颜色主题）
  - Flexbox 布局
  - Grid 布局
  - 响应式设计（手机、平板、桌面）
  - 平滑动画
  - 模态框样式
  - 表单样式
  - 通知样式
- **断点**：
  - 桌面：> 768px
  - 平板：481px - 768px
  - 手机：< 480px
- **大小**：~11.5 KB

#### js/script.js
- **功能**：前端交互脚本
- **功能模块**：
  - 通知提示
  - 模态框控制
  - AJAX 请求（Fetch API）
  - 任务表单提交
  - 任务编辑/删除
  - 快速添加任务
  - 任务状态切换
  - 日期验证
- **无依赖**：纯原生 JavaScript
- **大小**：~8.5 KB

---

### 文档文件

#### README.md
- **内容**：
  - 项目介绍
  - 技术栈说明
  - 功能特性
  - 安装步骤
  - 项目结构
  - 数据库设计
  - API 文档
  - 使用说明
  - 安全特性
  - 常见问题
- **大小**：~6.4 KB

#### QUICKSTART.md
- **内容**：
  - 5分钟快速部署
  - 配置向导使用
  - 手动配置步骤
  - 宝塔面板部署
  - Docker 部署
  - 本地开发环境
  - 常见问题解决
  - 安全建议
- **大小**：~7.8 KB

#### DEPLOYMENT.md
- **内容**：
  - 详细部署指南
  - 宝塔面板配置
  - LAMP/LNMP 配置
  - Nginx/Apache 配置
  - PHP 优化
  - SSL 配置
  - Docker 部署
  - 性能优化
  - 备份策略
  - 故障排除
- **大小**：~8.9 KB

#### FILES.md
- **内容**：本文件
- **功能**：项目文件清单和说明
- **大小**：~10+ KB

---

### 配置文件

#### .gitignore
- **功能**：Git 版本控制忽略文件
- **排除内容**：
  - 环境配置文件
  - 编辑器配置
  - 日志文件
  - 缓存文件
  - 上传文件
  - 依赖包
  - 备份文件
- **大小**：~0.5 KB

#### .htaccess
- **功能**：Apache 服务器配置
- **配置项**：
  - URL 重写
  - 安全设置
  - 字符集
  - Gzip 压缩
  - 浏览器缓存
  - 安全头部
  - PHP 设置
- **大小**：~1.3 KB

---

## 📊 文件统计

### 总计
- **PHP 文件**：17 个
- **JavaScript 文件**：1 个
- **CSS 文件**：1 个
- **SQL 文件**：1 个
- **Markdown 文档**：4 个
- **配置文件**：2 个

### 代码量估算
- **PHP 代码**：~1,500 行
- **JavaScript 代码**：~250 行
- **CSS 代码**：~750 行
- **SQL 代码**：~150 行
- **文档内容**：~1,200 行

### 总大小
- **代码文件**：~65 KB
- **文档文件**：~30 KB
- **配置文件**：~2 KB
- **总计**：~97 KB（不含 .git）

---

## 🔧 生产环境应删除的文件

部署到生产环境后，为了安全考虑，应删除以下文件：

```bash
rm install.php
rm setup.php
rm test.php
rm database.sql
rm README.md
rm QUICKSTART.md
rm DEPLOYMENT.md
rm FILES.md
```

或者通过 `.htaccess` 禁止访问：

```apache
<FilesMatch "(install|setup|test)\.php$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

---

## 📋 文件依赖关系

```
index.php
├── includes/config.php
├── includes/auth.php
└── includes/database.php

login.php / register.php
├── includes/config.php
└── includes/auth.php

dashboard.php / tasks.php / stats.php
├── includes/config.php
├── includes/auth.php
├── includes/functions.php
├── includes/database.php
├── includes/header.php
├── css/style.css
└── js/script.js

api/tasks.php / api/stats.php
├── includes/config.php
├── includes/auth.php
├── includes/functions.php
└── includes/database.php
```

---

## 🔒 文件权限建议

```bash
# 目录权限
find . -type d -exec chmod 755 {} \;

# 一般文件权限
find . -type f -exec chmod 644 {} \;

# 配置文件（只读）
chmod 400 includes/config.php

# 可执行文件（如果有）
chmod 755 *.sh
```

---

## 📝 文件版本信息

- **项目版本**：1.0.0
- **创建日期**：2024
- **PHP 版本要求**：7.4+
- **MySQL 版本要求**：5.7+
- **字符编码**：UTF-8

---

**文档更新日期**：2024-10-27
