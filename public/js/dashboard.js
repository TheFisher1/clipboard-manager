// Dashboard functionality
let currentUser = null;
let currentClipboardId = null;

// Check authentication on page load
(async function() {
    try {
        const response = await api.getCurrentUser();
        currentUser = response.user;
        loadClipboards();
    } catch (error) {
        // Not authenticated, redirect to login
        window.location.href = '/login.html';
    }
})();

// Modal management
const createModal = document.getElementById('createModal');
const detailsModal = document.getElementById('detailsModal');
const editModal = document.getElementById('editModal');

const stepperTrack = document.getElementById('stepperTrack');
const collapsibleContent = document.getElementById('collapsibleContent');
const collapsibleButton = document.getElementById('collapsibleButton');
const createBtn = document.getElementById('createClipboardBtn');
const cancelBtns = document.querySelectorAll('.cancel-btn');
const closeBtns = document.querySelectorAll('.close');

const createClipboardForm = document.getElementById('createClipboardForm');
const createItemForm = document.getElementById('createItemForm');

const itemTypeSelect = document.getElementById('itemTypeSelect');
const itemTypeFields = document.getElementById('itemTypeFields');

collapsibleButton.addEventListener('click', (e) => {
    const collapsed = e.currentTarget.classList.toggle('collapsed');
    e.currentTarget.textContent = collapsed ? 'Hide details' : 'Show details';
    collapsibleContent.style.display = collapsed ? 'block' : 'none';
});

createBtn.addEventListener('click', () => {
    createModal.style.display = 'block';
});

cancelBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        createModal.style.display = 'none';
        editModal.style.display = 'none';
    });
});

closeBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        createModal.style.display = 'none';
        detailsModal.style.display = 'none';
        editModal.style.display = 'none';
        resetCollapsible();
    });
});

window.addEventListener('click', (e) => {
    if (e.target === createModal) {
        createModal.style.display = 'none';
    }

    if (e.target === detailsModal) {
        detailsModal.style.display = 'none';
        stepperTrack.classList.remove('step-2');
        resetCollapsible();
        resetItemForm();
    }

    if (e.target === editModal) {
        editModal.style.display = 'none';
    }
});

document.getElementById('addItemStepBtn').addEventListener('click', () => {
    stepperTrack.classList.add('step-2');
});

document.getElementById('backToDetails').addEventListener('click', () => {
    stepperTrack.classList.remove('step-2');
});

// Load clipboards
async function loadClipboards() {
    const container = document.getElementById('clipboardsList');
    
    try {
        const clipboards = await api.getClipboards();
        
        if (clipboards.length === 0) {
            container.innerHTML = '<p class="loading">No clipboards yet. Create one to get started!</p>';
            return;
        }

            container.innerHTML = clipboards.map(clipboard => `
    <div class="clipboard-card" data-id="${clipboard.id}">
        <div class="card-header">
            <h3>${escapeHtml(clipboard.name)}</h3>
            <div class="card-actions">
                <button onclick="event.stopPropagation(); openEditModal(${JSON.stringify(clipboard).replace(/"/g, '&quot;')})" class="btn-icon btn-edit">️</button>
                <button onclick="event.stopPropagation(); confirmDelete(${clipboard.id})" class="btn-icon btn-delete">️</button>
            </div>
        </div>

        <p>${escapeHtml(clipboard.description || 'No description')}</p>

        <div class="clipboard-meta">
            <span class="badge ${clipboard.is_public ? 'badge-public' : 'badge-private'}">
                ${clipboard.is_public ? 'Public' : 'Private'}
            </span>
            <span>Created: ${formatDate(clipboard.created_at)}</span>
        </div>
    </div>
`).join('');


        // Add click handlers
        document.querySelectorAll('.clipboard-card').forEach(card => {
            card.addEventListener('click', () => {
                const id = card.dataset.id;
                showClipboardDetails(id);
            });
        });
    } catch (error) {
        container.innerHTML = `<div class="error">Failed to load clipboards: ${error.message}</div>`;
    }
}

// Create clipboard
createClipboardForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const allowed_types = formData.getAll('allowed_types');
    const data = {
        name: formData.get('name'),
        description: formData.get('description'),
        is_public: formData.get('is_public') === 'on',
        owner_id: currentUser.id,
        max_subscribers: formData.get('max-subscribers'),
        max_items: formData.get('max-items'),
        default_expiration_minutes: getExpirationMinutes(formData.get('expiration')),
        allowed_content_types: allowed_types.length === 0 ? null : allowed_types 
    };

    try {
        await api.createClipboard(data);
        createModal.style.display = 'none';
        e.target.reset();
        loadClipboards();
    } catch (error) {
        alert('Failed to create clipboard: ' + error.message);
    }
});

// Show clipboard details
async function showClipboardDetails(id) {
    currentClipboardId = id;
    
    try {
        const clipboard = await api.getClipboard(id);
        document.getElementById('clipboardName').textContent = clipboard.name;
        document.getElementById('clipboardDescription').textContent = 
            clipboard.description || 'No description';
        createCollapsible(clipboard);
        
        await loadItems(id);
        
        detailsModal.style.display = 'block';
    } catch (error) {
        alert('Failed to load clipboard: ' + error.message);
    }
}

function createCollapsible(clipboard) {
    const allowedContentString = clipboard['allowed_content_types']?.join(', ') ?? null;
    const expiration = clipboard['default_expiration_minutes'] ?? null;

    collapsibleContent.textContent = 
        `Created by: ${clipboard['owner_id']} (TODO: change to username)\n` +
        `Allowed content: ${allowedContentString === null ? 'All content types' : allowedContentString}\n` +
        `Created: ${clipboard['created_at']}\n` +
        `Expires: ${expiration === null ? 'Never' : expiration}\n` +
        `Max subscribers: ${clipboard['max_subscribers']}\n` +
        `Max items: ${clipboard['max_items']}\n`;
}

function resetCollapsible() {
    const btn = document.getElementById('collapsibleButton');
    const content = document.getElementById('collapsibleContent');

    btn.classList.remove('collapsed');
    btn.textContent = 'Show details';
    content.style.display = 'none';
}

function resetClipboardForm() {
    createClipboardForm.reset();
}

function resetItemForm() {
    createItemForm.reset();
    itemTypeSelect.value = 'text';
    itemTypeFields.innerHTML = getTypeFields(itemTypeSelect.value);
}

function renderItemPreview(item) {
    // Construct the dynamic URL for your viewFile endpoint
    switch (item.content_type) {
        case 'image':
            return `
                <div class="item-preview image-preview">
                    <img src="${api.getFileViewUrl(item.id)}" 
                         alt="${escapeHtml(item.title)}" 
                         loading="lazy" 
                         onclick="window.open('${api.getFileViewUrl(item.id)}', '_blank')">
                </div>`;
                
        case 'file':
            return `
                <div class="item-preview file-preview">
                    <div class="file-info">
                        <span class="file-icon">📁</span>
                        <div class="file-details">
                            <span class="file-name">${escapeHtml(item.file_name || 'Download File')}</span>
                            <span class="file-size">${(item.file_size / 1024).toFixed(1)} KB</span>
                        </div>
                    </div>
                    <a href="${api.getFileViewUrl(item.id)}" download="${item.file_name}" class="btn btn-sm btn-secondary">
                        Download
                    </a>
                </div>`;
                
        case 'code':
            return `
                <div class="item-preview code-preview">
                    <pre><code>${escapeHtml(item.content_text.substring(0, 300))}</code></pre>
                </div>`;
                
        default:
            return item.content_text ? 
                `<p class="item-text-preview">${escapeHtml(item.content_text.substring(0, 150))}...</p>` : '';
    }
}

// Update the loadItems mapping logic
async function loadItems(clipboardId) {
    const container = document.getElementById('itemsList');
    try {
        const items = await api.getClipboardItems(clipboardId);
        if (items.length === 0) {
            container.innerHTML = '<p class="loading">No items yet.</p>';
            return;
        }

        container.innerHTML = items.map(item => `
            <div class="item-card ${item.content_type}-card" data-id="${item.id}">
                <div class="item-header">
                    <h4>${escapeHtml(item.title || 'Untitled')}</h4>
                    <button class="btn-icon btn-delete" onclick="deleteItemHandler(${clipboardId}, ${item.id})">️</button>
                </div>

                <div class="item-subheader">
                    <span class="type-badge">${item.content_type}</span>
                </div>

                ${renderItemPreview(item)}

                <p class="item-description">${escapeHtml(item.description || '')}</p>

                <div class="clipboard-meta">
                    <span>Views: ${item.view_count}</span>
                    <span>Created: ${formatDate(item.created_at)}</span>
                </div>
            </div>
        `).join('');

    } catch (error) {
        container.innerHTML = `<div class="error">Failed to load items: ${error.message}</div>`;
    }
}

createItemForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const rawFormData = new FormData(e.target);
    const contentType = itemTypeSelect.value;

    try {
        if (contentType === 'image' || contentType === 'file') {

            const filePayload = new FormData();
            
            filePayload.append('title', rawFormData.get('name'));
            filePayload.append('description', rawFormData.get('description'));
            filePayload.append('content_type', contentType);
            filePayload.append('submitted_by', currentUser.id);
            filePayload.append('file', rawFormData.get('file'));

            await api.createItemFile(currentClipboardId, filePayload);

        } else {

            const jsonPayload = {
                title: rawFormData.get('name'),
                description: rawFormData.get('description'),
                content_type: contentType,
                submitted_by: currentUser.id,
                content_text: rawFormData.get('content-text')
            };

            await api.createItem(currentClipboardId, jsonPayload);
        }

        e.target.reset();
        await loadItems(currentClipboardId);
        stepperTrack.classList.remove('step-2');

    } catch (error) {
        alert('Failed to add item: ' + error.message);
    }
});

itemTypeSelect.addEventListener('change', () => {
    const type = itemTypeSelect.value;
    itemTypeFields.innerHTML = getTypeFields(type);
});

function getTypeFields(type) {

    switch (type) {
        case 'text':
            return `
                <div class="form-group">
                    <label for="content-text">Text</label>
                    <textarea type="text" id="content-text" name="content-text" rows="4" required></textarea>
                </div>
            `;
        case 'code':
            return `
                <div class="form-group">
                    <label for="content-text">Code</label>
                    <textarea type="text" id="content-text" name="content-text" rows="4" required></textarea>
                </div>
            `;
        case 'image':
            return `
                <div class="form-group">
                    <label for="file">Image</label>
                    <input type="file" id="file" name="file" accept="image/*" required>
                </div>
            `;
        case 'file':
            return `
                <div class="form-group">
                    <label for="file">File</label>
                    <input type="file" id="file" name="file"  required>
                </div>
            `;
        default:
            return '';
    }
}

// Utility functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString();
}

// Hardcoded zashtoto me myrzi sorry
function getExpirationMinutes(expiration) {
    switch (expiration) {
        case 'never':
            return '';
        case '1h':
            return 60;
        case '24h':
            return 24 * 60;
        case '7d':
            return 7 * 24 * 60;
        case '30d':
            return 30 * 24 * 60;
    }
}

const allowAll = document.getElementById('allowAll');
const typeCheckboxes = document.querySelectorAll(
  'input[name="allowed_types"]'
);

allowAll.addEventListener('change', () => {
    typeCheckboxes.forEach(cb => cb.checked = allowAll.checked);
});

typeCheckboxes.forEach(cb => {
    cb.addEventListener('change', () => {
        allowAll.checked = [...typeCheckboxes].every(c => c.checked);
    });
});

// Initialize
loadClipboards();

// EDITING AND DELETING CLIPBOARDS
async function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this clipboard and all its items?')) {
        try {
            await api.deleteClipboard(id);
            loadClipboards(); // Refresh the list
        } catch (error) {
            alert('Error deleting: ' + error.message);
        }
    }
}

function openEditModal(clipboard) {
    document.getElementById('editClipboardId').value = clipboard.id;
    document.getElementById('editName').value = clipboard.name;
    document.getElementById('editDescription').value = clipboard.description;
    document.getElementById('editIsPublic').checked = clipboard.is_public;
    
    document.getElementById('editModal').style.display = 'block';
}

async function handleEditSubmit(event) {
    event.preventDefault();
    const id = document.getElementById('editClipboardId').value;
    const data = {
        name: document.getElementById('editName').value,
        description: document.getElementById('editDescription').value,
        is_public: document.getElementById('editIsPublic').checked
    };

    try {
        await api.updateClipboard(id, data);
        editModal.style.display = 'none';
        loadClipboards(); // Refresh
    } catch (error) {
        alert('Update failed: ' + error.message);
    }
}

async function deleteItemHandler(clipboardId, itemId) {
    if (!confirm("Are you sure you want to delete this item?")) return;

    try {
        await api.deleteItem(itemId);

        // reload items in clipboard
        await loadItems(clipboardId);

    } catch (error) {
        alert('Failed to delete item: ' + error.message);
    }
}
