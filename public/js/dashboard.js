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
const collapsibleContent = document.getElementById('collapsibleContent');
const collapsibleButton = document.getElementById('collapsibleButton');
const createBtn = document.getElementById('createClipboardBtn');
const cancelBtn = document.getElementById('cancelBtn');
const closeBtns = document.querySelectorAll('.close');

collapsibleButton.addEventListener('click', (e) => {
    const collapsed = e.currentTarget.classList.toggle('collapsed');
    e.currentTarget.textContent = collapsed ? 'Hide details' : 'Show details';
    collapsibleContent.style.display = collapsed ? 'block' : 'none';
});

createBtn.addEventListener('click', () => {
    createModal.style.display = 'block';
});

cancelBtn.addEventListener('click', () => {
    createModal.style.display = 'none';
});

closeBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        createModal.style.display = 'none';
        detailsModal.style.display = 'none';
        resetCollapsible();
    });
});

window.addEventListener('click', (e) => {
    if (e.target === createModal) {
        createModal.style.display = 'none';
    }

    if (e.target === detailsModal) {
        detailsModal.style.display = 'none';
        resetCollapsible();
    }
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
                <h3>${escapeHtml(clipboard.name)}</h3>
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
document.getElementById('createClipboardForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = {
        name: formData.get('name'),
        description: formData.get('description'),
        is_public: formData.get('is_public') === 'on',
        owner_id: currentUser.id
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
    detailsModal.style.display = 'block';
    
    try {
        const clipboard = await api.getClipboard(id);
        document.getElementById('clipboardName').textContent = clipboard.name;
        document.getElementById('clipboardDescription').textContent = 
            clipboard.description || 'No description';
        createCollapsible(clipboard);
        
        await loadItems(id);
    } catch (error) {
        alert('Failed to load clipboard: ' + error.message);
    }
}

function createCollapsible(clipboard) {
    collapsibleContent.textContent = 
        `Created by: ${clipboard['owner_id']} (TODO: change to username)\n` +
        `Allowed content: ${clipboard['allowed_content_types']}\n` +
        `Created: ${clipboard['created_at']}\n` +
        `Expires: ${clipboard['default_expiration_minutes']}\n` +
        `Max items: ${clipboard['max_items']}\n` +
        `Max subscribers: ${clipboard['max_subscribers']}\n`;
}

function resetCollapsible() {
    const btn = document.getElementById('collapsibleButton');
    const content = document.getElementById('collapsibleContent');

    btn.classList.remove('collapsed');
    btn.textContent = 'Show details';
    content.style.display = 'none';
}

// Load items
async function loadItems(clipboardId) {
    const container = document.getElementById('itemsList');
    
    try {
        const items = await api.getClipboardItems(clipboardId);
        
        if (items.length === 0) {
            container.innerHTML = '<p class="loading">No items yet.</p>';
            return;
        }

        container.innerHTML = items.map(item => `
            <div class="item-card">
                <h4>${escapeHtml(item.title || 'Untitled')}</h4>
                <p><strong>Type:</strong> ${item.content_type}</p>
                ${item.content_text ? `<p>${escapeHtml(item.content_text.substring(0, 100))}...</p>` : ''}
                ${item.url ? `<p><a href="${escapeHtml(item.url)}" target="_blank">Open Link</a></p>` : ''}
                <p class="clipboard-meta">
                    <span>Views: ${item.view_count}</span>
                    <span>Created: ${formatDate(item.created_at)}</span>
                </p>
            </div>
        `).join('');
    } catch (error) {
        container.innerHTML = `<div class="error">Failed to load items: ${error.message}</div>`;
    }
}

// Add item button
document.getElementById('addItemBtn').addEventListener('click', async () => {
    const text = prompt('Enter text content:');
    if (!text) return;

    try {
        await api.createItem(currentClipboardId, {
            content_type: 'text',
            content_text: text,
            submitted_by: currentUser.id
        });
        loadItems(currentClipboardId);
    } catch (error) {
        alert('Failed to add item: ' + error.message);
    }
});

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

// Initialize
loadClipboards();
