// Content Moderation Management
const contentManager = {
    selectedItems: new Set(),
    deleteId: null,

    async init() {
        await this.loadClipboards();
        await this.loadContent();
        this.setupEventListeners();
    },

    setupEventListeners() {
        document.getElementById('delete-form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.confirmDelete();
        });
    },

    async loadClipboards() {
        try {
            const response = await adminAPI.get('/api/admin/clipboards?per_page=100');
            
            if (response.success) {
                const select = document.getElementById('filter-clipboard');
                select.innerHTML = '<option value="">All Clipboards</option>' +
                    response.data.clipboards.map(clipboard => 
                        `<option value="${clipboard.id}">${this.escapeHtml(clipboard.name)}</option>`
                    ).join('');
            }
        } catch (error) {
            console.error('Error loading clipboards:', error);
        }
    },

    async loadContent() {
        const contentType = document.getElementById('filter-content-type').value;
        const clipboardId = document.getElementById('filter-clipboard').value;
        const dateFrom = document.getElementById('filter-date-from').value;
        const dateTo = document.getElementById('filter-date-to').value;

        const params = new URLSearchParams({ per_page: 50 });
        if (contentType) params.append('content_type', contentType);
        if (clipboardId) params.append('clipboard_id', clipboardId);
        if (dateFrom) params.append('date_from', dateFrom);
        if (dateTo) params.append('date_to', dateTo);

        try {
            const response = await adminAPI.get(`/api/admin/content?${params}`);
            
            if (response.success) {
                this.renderContent(response.data.content);
            }
        } catch (error) {
            console.error('Error loading content:', error);
            alert('Failed to load content');
        }
    },

    renderContent(content) {
        const tbody = document.getElementById('content-table-body');
        
        if (content.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="no-data">No content found</td></tr>';
            return;
        }

        tbody.innerHTML = content.map(item => `
            <tr>
                <td><input type="checkbox" class="content-checkbox" data-id="${item.id}" onchange="contentManager.toggleSelect(${item.id})"></td>
                <td>${item.id}</td>
                <td><span class="badge">${this.escapeHtml(item.content_type)}</span></td>
                <td>${this.escapeHtml(item.clipboard_name || 'N/A')}</td>
                <td>${this.escapeHtml(item.submitted_by_name || 'Unknown')}</td>
                <td>${this.formatDate(item.created_at)}</td>
                <td class="actions">
                    <button onclick="contentManager.viewDetails(${item.id})" class="btn-sm btn-info">View</button>
                    <button onclick="contentManager.deleteContent(${item.id})" class="btn-sm btn-danger">Delete</button>
                </td>
            </tr>
        `).join('');
    },

    toggleSelect(id) {
        if (this.selectedItems.has(id)) {
            this.selectedItems.delete(id);
        } else {
            this.selectedItems.add(id);
        }
    },

    toggleSelectAll() {
        const checkboxes = document.querySelectorAll('.content-checkbox');
        const selectAll = document.getElementById('select-all').checked;
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll;
            const id = parseInt(checkbox.dataset.id);
            if (selectAll) {
                this.selectedItems.add(id);
            } else {
                this.selectedItems.delete(id);
            }
        });
    },

    async viewDetails(id) {
        try {
            const response = await adminAPI.get(`/api/admin/content/${id}`);
            
            if (response.success) {
                this.showDetailModal(response.data);
            }
        } catch (error) {
            console.error('Error loading content details:', error);
            alert('Failed to load content details');
        }
    },

    showDetailModal(content) {
        const detailContent = document.getElementById('detail-content');
        
        let preview = '';
        if (content.content_text) {
            preview = `<p><strong>Content:</strong></p><pre>${this.escapeHtml(content.content_text.substring(0, 500))}</pre>`;
        } else if (content.url) {
            preview = `<p><strong>URL:</strong> <a href="${this.escapeHtml(content.url)}" target="_blank">${this.escapeHtml(content.url)}</a></p>`;
        } else if (content.file_path) {
            preview = `<p><strong>File:</strong> ${this.escapeHtml(content.original_filename || 'Unknown')}</p>`;
        }

        detailContent.innerHTML = `
            <div class="detail-section">
                <p><strong>ID:</strong> ${content.id}</p>
                <p><strong>Type:</strong> ${this.escapeHtml(content.content_type)}</p>
                <p><strong>Clipboard:</strong> ${this.escapeHtml(content.clipboard_name || 'N/A')}</p>
                <p><strong>Submitted By:</strong> ${this.escapeHtml(content.submitted_by_name || 'Unknown')}</p>
                <p><strong>Created:</strong> ${this.formatDate(content.created_at)}</p>
                <p><strong>Views:</strong> ${content.view_count || 0}</p>
                <p><strong>Downloads:</strong> ${content.download_count || 0}</p>
                ${preview}
            </div>
        `;

        document.getElementById('detail-modal').style.display = 'block';
    },

    closeDetailModal() {
        document.getElementById('detail-modal').style.display = 'none';
    },

    deleteContent(id) {
        this.deleteId = id;
        this.selectedItems.clear();
        this.selectedItems.add(id);
        document.getElementById('delete-modal').style.display = 'block';
    },

    async bulkDelete() {
        if (this.selectedItems.size === 0) {
            alert('Please select items to delete');
            return;
        }
        
        this.deleteId = null;
        document.getElementById('delete-modal').style.display = 'block';
    },

    async confirmDelete() {
        const reason = document.getElementById('delete-reason').value;
        
        try {
            if (this.deleteId) {
                // Single delete
                await adminAPI.delete(`/api/admin/content/${this.deleteId}`, {
                    body: JSON.stringify({ reason })
                });
            } else {
                // Bulk delete
                await adminAPI.post('/api/admin/content/bulk-delete', {
                    content_ids: Array.from(this.selectedItems),
                    reason
                });
            }
            
            alert('Content deleted successfully');
            this.closeDeleteModal();
            this.selectedItems.clear();
            this.loadContent();
        } catch (error) {
            console.error('Error deleting content:', error);
            alert('Failed to delete content');
        }
    },

    closeDeleteModal() {
        document.getElementById('delete-modal').style.display = 'none';
        document.getElementById('delete-reason').value = '';
        this.deleteId = null;
    },

    formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleString();
    },

    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    contentManager.init();
});

// Close modals when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
};
