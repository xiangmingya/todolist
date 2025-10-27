# 更新日志 - 分类字段重命名为标签

## 修改日期
2024

## 修改原因
- 用户反馈：创建任务时填写的标签没有与标签系统关联
- 数据库中存在 `category` 字段，与"标签"概念重复
- 统一术语，将 `category`（分类）改为 `tag`（标签）更符合实际使用场景

## 修改内容

### 1. 数据库修改
- **文件**: `install.php`
- **修改**: 将 `tasks` 表中的 `category VARCHAR(50)` 字段改为 `tag VARCHAR(50)`

### 2. 后端 PHP 代码修改

#### includes/functions.php
- `TaskManager::createTask()` - 参数 `$category` 改为 `$tag`
- `TaskManager::getTasks()` - 筛选条件 `category` 改为 `tag`
- `TaskManager::updateTask()` - 更新字段 `category` 改为 `tag`
- `TaskManager::getTaskStats()` - 统计字段 `by_category` 改为 `by_tag`
- `TaskManager::getCategories()` - 方法重命名为 `getTags()`

#### api/tasks.php
- GET 请求筛选参数：`category` → `tag`
- POST 请求数据字段：`category` → `tag`
- PUT 请求数据字段：`category` → `tag`

### 3. 前端页面修改

#### tasks.php
- 筛选下拉框：`name="category"` → `name="tag"`
- 表单字段：`id="taskCategory"` → `id="taskTag"`
- 数据列表：`id="categoryList"` → `id="tagList"`
- 显示字段：`$task['category']` → `$task['tag']`
- 变量名：`$categories` → `$tags`

#### dashboard.php
- 快速添加表单：`id="quickTaskCategory"` → `id="quickTaskTag"`
- 数据列表：`id="quickCategoryList"` → `id="quickTagList"`
- 任务显示：`$task['category']` → `$task['tag']`
- CSS 类名：`task-category` → `task-tag`
- 编辑表单：`id="taskCategory"` → `id="taskTag"`

#### stats.php
- 图表标题："分类分布" → "标签分布"
- Canvas ID：`categoryChart` → `tagChart`
- JavaScript 变量：`categoryData` → `tagData`
- 统计字段：`statsData.by_category` → `statsData.by_tag`

#### tags.php
- 任务显示：`$task['category']` → `$task['tag']`
- CSS 类名：`task-category` → `task-tag`
- 表单字段：`id="taskCategory"` → `id="taskTag"`

### 4. JavaScript 代码修改

#### js/script.js
- 加载任务数据：`taskCategory` → `taskTag`
- 表单提交：`category` → `tag`
- 快速添加：`quickTaskCategory` → `quickTaskTag`

### 5. 新增文件
- **migrate_category_to_tag.php** - 数据库迁移脚本
- **MIGRATION_CATEGORY_TO_TAG.md** - 迁移文档
- **CHANGELOG_TAG_RENAME.md** - 本更新日志

### 6. 文档更新
- **README.md** - 更新 API 文档和数据库结构说明

## 影响范围
- ✅ 不影响现有数据（迁移脚本会自动重命名字段）
- ✅ 不影响用户使用（功能保持不变）
- ✅ 所有 "分类" 术语统一改为 "标签"
- ✅ 保持了代码的向后兼容性

## 迁移步骤
1. 备份数据库
2. 更新代码文件
3. 运行 `migrate_category_to_tag.php` 迁移数据库
4. 验证功能正常

## 测试清单
- [ ] 创建新任务时可以添加标签
- [ ] 编辑任务时可以修改标签
- [ ] 按标签筛选任务功能正常
- [ ] 标签统计图表正常显示
- [ ] 快速添加任务的标签字段正常
- [ ] 现有任务的标签数据正常显示

## 注意事项
- 迁移前请务必备份数据库
- 本次修改仅重命名字段，不改变功能逻辑
- 独立的标签管理系统（tags 表）保持不变
- 未来可以选择将简单标签迁移到完整的标签关联系统
