// 标签管理 JavaScript

// 标签模态框控制
const tagModal = document.getElementById('tagModal');
const addTagBtn = document.getElementById('addTagBtn');
const sidebarAddTagBtn = document.getElementById('sidebarAddTagBtn');
const editTagBtn = document.getElementById('editTagBtn');
const deleteTagBtn = document.getElementById('deleteTagBtn');

// 打开新建标签模态框
function openTagModal(tagId = null) {
    const modalTitle = document.getElementById('tagModalTitle');
    const tagForm = document.getElementById('tagForm');
    
    if (tagId) {
        modalTitle.textContent = '编辑标签';
        loadTagData(tagId);
    } else {
        modalTitle.textContent = '新建标签';
        tagForm.reset();
        document.getElementById('tagId').value = '';
        document.getElementById('tagColor').value = '#808080';
        document.getElementById('tagColorHex').value = '#808080';
    }
    
    tagModal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

// 关闭标签模态框
function closeTagModal() {
    tagModal.classList.remove('show');
    document.body.style.overflow = '';
}

// 加载标签数据
function loadTagData(tagId) {
    fetch(`/api/tags.php?id=${tagId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tag = data.data;
                document.getElementById('tagId').value = tag.id;
                document.getElementById('tagName').value = tag.name;
                document.getElementById('tagColor').value = tag.color;
                document.getElementById('tagColorHex').value = tag.color;
            } else {
                showNotification('加载标签失败', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('加载标签失败', 'error');
        });
}

// 按钮事件监听
if (addTagBtn) {
    addTagBtn.addEventListener('click', () => {
        openTagModal();
    });
}

if (sidebarAddTagBtn) {
    sidebarAddTagBtn.addEventListener('click', () => {
        openTagModal();
    });
}

if (editTagBtn) {
    editTagBtn.addEventListener('click', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tagId = urlParams.get('id');
        if (tagId) {
            openTagModal(tagId);
        }
    });
}

// 删除标签
if (deleteTagBtn) {
    deleteTagBtn.addEventListener('click', () => {
        if (confirm('确定要删除这个标签吗？此操作不可恢复。')) {
            const tagId = deleteTagBtn.dataset.tagId;
            fetch('/api/tags.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: tagId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = 'tags.php';
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
    });
}

// 模态框关闭按钮
document.querySelectorAll('[data-modal="tagModal"]').forEach(btn => {
    btn.addEventListener('click', () => {
        closeTagModal();
    });
});

if (tagModal) {
    tagModal.addEventListener('click', (e) => {
        if (e.target === tagModal) {
            closeTagModal();
        }
    });
}

// 颜色选择器同步
const tagColor = document.getElementById('tagColor');
const tagColorHex = document.getElementById('tagColorHex');

if (tagColor && tagColorHex) {
    tagColor.addEventListener('input', (e) => {
        tagColorHex.value = e.target.value;
    });
}

// 标签表单提交
const tagForm = document.getElementById('tagForm');
if (tagForm) {
    tagForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const tagId = document.getElementById('tagId').value;
        const formData = {
            id: tagId,
            name: document.getElementById('tagName').value,
            color: document.getElementById('tagColor').value
        };
        
        const url = '/api/tags.php';
        const method = tagId ? 'PUT' : 'POST';
        
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
                closeTagModal();
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
