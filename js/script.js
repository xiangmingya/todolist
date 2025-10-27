// 工具函数
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// 模态框控制
const modal = document.getElementById('taskModal');
const addTaskBtn = document.getElementById('addTaskBtn');
const modalClose = document.querySelector('.modal-close');
const modalCancel = document.querySelector('.modal-cancel');

if (addTaskBtn) {
    addTaskBtn.addEventListener('click', () => {
        openTaskModal();
    });
}

if (modalClose) {
    modalClose.addEventListener('click', () => {
        closeTaskModal();
    });
}

if (modalCancel) {
    modalCancel.addEventListener('click', () => {
        closeTaskModal();
    });
}

if (modal) {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeTaskModal();
        }
    });
}

function openTaskModal(taskId = null) {
    const modalTitle = document.getElementById('modalTitle');
    const taskForm = document.getElementById('taskForm');
    
    if (taskId) {
        modalTitle.textContent = '编辑任务';
        loadTaskData(taskId);
    } else {
        modalTitle.textContent = '新建任务';
        taskForm.reset();
        document.getElementById('taskId').value = '';
    }
    
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeTaskModal() {
    modal.classList.remove('show');
    document.body.style.overflow = '';
}

function loadTaskData(taskId) {
    fetch(`/api/tasks.php?id=${taskId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const task = data.data;
                document.getElementById('taskId').value = task.id;
                document.getElementById('taskTitle').value = task.title;
                document.getElementById('taskDescription').value = task.description || '';
                document.getElementById('taskStatus').value = task.status;
                document.getElementById('taskPriority').value = task.priority;
                document.getElementById('taskCategory').value = task.category || '';
                document.getElementById('taskDueDate').value = task.due_date || '';
            } else {
                showNotification('加载任务失败', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('加载任务失败', 'error');
        });
}

// 任务表单提交
const taskForm = document.getElementById('taskForm');
if (taskForm) {
    taskForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const taskId = document.getElementById('taskId').value;
        const formData = {
            id: taskId,
            title: document.getElementById('taskTitle').value,
            description: document.getElementById('taskDescription').value,
            status: document.getElementById('taskStatus').value,
            priority: document.getElementById('taskPriority').value,
            category: document.getElementById('taskCategory').value,
            due_date: document.getElementById('taskDueDate').value || null
        };
        
        const url = '/api/tasks.php';
        const method = taskId ? 'PUT' : 'POST';
        
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                closeTaskModal();
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('操作失败，请重试', 'error');
        });
    });
}

// 编辑任务
document.addEventListener('click', (e) => {
    if (e.target.closest('.edit-task')) {
        const btn = e.target.closest('.edit-task');
        const taskId = btn.dataset.taskId;
        openTaskModal(taskId);
    }
});

// 删除任务
document.addEventListener('click', (e) => {
    if (e.target.closest('.delete-task')) {
        const btn = e.target.closest('.delete-task');
        const taskId = btn.dataset.taskId;
        
        if (confirm('确定要删除这个任务吗？')) {
            fetch('/api/tasks.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: taskId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('删除失败，请重试', 'error');
            });
        }
    }
});

// 快速添加任务
const quickTaskForm = document.getElementById('quickTaskForm');
if (quickTaskForm) {
    quickTaskForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const formData = {
            title: document.getElementById('quickTaskTitle').value,
            priority: document.getElementById('quickTaskPriority').value,
            category: document.getElementById('quickTaskCategory')?.value || null,
            due_date: document.getElementById('quickTaskDate').value || null,
            status: 'pending'
        };
        
        fetch('/api/tasks.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('任务添加成功', 'success');
                quickTaskForm.reset();
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('添加失败，请重试', 'error');
        });
    });
}

// 任务完成复选框
document.addEventListener('change', (e) => {
    if (e.target.classList.contains('task-complete')) {
        const taskId = e.target.dataset.taskId;
        const completed = e.target.checked;
        
        fetch('/api/tasks.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: taskId,
                status: completed ? 'completed' : 'pending'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('状态已更新', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification(data.message, 'error');
                e.target.checked = !completed;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('更新失败，请重试', 'error');
            e.target.checked = !completed;
        });
    }
});

// 设置今天的日期为默认最小日期
const dateInputs = document.querySelectorAll('input[type="date"]');
const today = new Date().toISOString().split('T')[0];
dateInputs.forEach(input => {
    if (!input.value) {
        input.min = today;
    }
});
