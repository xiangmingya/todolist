# 分类字段重命名为标签的迁移说明

## 修改概述

本次更新将数据库中的 `category` 字段重命名为 `tag`（标签），使语义更加清晰明确。

## 修改内容

### 数据库修改
- 将 `tasks` 表中的 `category` 字段重命名为 `tag`

### 代码修改
以下文件已更新，将所有 `category` 引用改为 `tag`：

1. **install.php** - 数据库安装脚本
2. **includes/functions.php** - TaskManager 类
   - `createTask()` 方法参数
   - `getTasks()` 筛选逻辑
   - `updateTask()` 更新逻辑
   - `getTaskStats()` 统计逻辑
   - `getCategories()` 重命名为 `getTags()`
3. **api/tasks.php** - API 接口
4. **tasks.php** - 任务管理页面
5. **dashboard.php** - 首页
6. **stats.php** - 统计页面
7. **tags.php** - 标签页面
8. **js/script.js** - 前端 JavaScript

## 迁移步骤

### 对于新安装
直接使用 `install.php` 安装即可，数据库将自动创建包含 `tag` 字段的表结构。

### 对于已有数据库
1. 访问 `migrate_category_to_tag.php` 执行数据库迁移
2. 迁移脚本会自动将 `category` 字段重命名为 `tag`
3. 迁移完成后，所有现有的分类数据会保留并作为标签使用

## 注意事项

- 所有现有的分类数据会被保留，自动转换为标签
- 前端界面的"标签"标签会继续使用文本输入方式
- 独立的标签管理系统（tags 表）仍然保留，未来可以选择迁移到完整的标签关联系统

## 验证

迁移完成后，请验证以下功能：
- ✓ 创建新任务时可以添加标签
- ✓ 筛选任务时可以按标签筛选
- ✓ 统计页面正确显示标签分布
- ✓ 现有任务的标签数据正常显示
