// Clipboard Management
const clipboardManager = {
    currentPage: 1,
    perPage: 25,
    sortField: 'created_at',
    sortOrder: 'desc',
    deleteId: null,

    init() {
        this.loadClipboards();
        this.loadOwners();
        this.setupEventListeners();
    },

    setupEventListeners() {
        document.getElementById('search').addEventListener('input', () => {
            this.currentPage = 1;
            this.loadClipboards();
        });

        document.getElementById('edit-form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveClipboard();
        });

        document.getElementById('transfer-form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.transferOwnership();
        });
    },

    async loadClipboards() {
        const search = document.getElementById('search').value;
        const isPublic = document.getElementById('filter-public').value;
        const ownerId = document.getElementById('filter-owner').value;

        const params = new URLSearchParams({
            page: this.currentPage,
            per_page: this.perPage
        });

        if (search) params.append('search', search);
        if (isPublic) params.append('is_public', isPublic);
        if (ownerId) params.append('owner_id', ownerId);

        try {
            const response = await adminAPI.get(`/api/admin/clipboards?${params}`);
            
            if (response.success) {
                this.renderClipboards(response.data.clipboards);
                this.renderPagination(response.data.pagination);
            }
        } catch (error) {
            console.error('Error loading clipboards:', error);
            alert('Failed to load clipboards');
        }
    },

    renderClipboards(clipboards) {
        const tbody = document.getElementById('clipboards-table-body');
        
        if (clipboards.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="no-data">No clipboards found</td></tr>';
            return;
        }

        tbody.innerHTML = clipboards.map(clipboard => `
            <tr>
                <td>${clipboard.id}</td>
                <td>${this.escapeHtml(clipboard.name)}</td>
                <td>${this.escapeHtml(clipboard.owner_name || 'Unknown')}</td>
                <td><span class="badge ${clipboard.is_public ? 'badge-success' : 'badge-secondary'}">${clipboard.is_public ? 'Public' : 'Private'}</span></td>
                <td>${clipboard.items_count || 0}</td>
                <td>${clipboard.subscribers_count || 0}</td>
                <td>${this.formatDate(clipboard.created_at)}</td>
                <td class="actions">
                    <button onclick="clipboardManager.viewDetails(${clipboard.id})" class="btn-sm btn-info">View</button>
                    <button onclick="clipboardManager.editClipboard(${clipboard.id})" class="btn-sm btn-primary">Edit</button>
                    <button onclick="clipboardManager.showTransferModal(${clipboard.id})" class="btn-sm btn-warning">Transfer</button>
                    <button onclick="clipboardManager.deleteClipboard(${clipboard.id})" class="btn-sm btn-danger">Delete</button>
                </td>
            </tr>
        `).join('');
    },

    renderPagination(pagination) {
        const container = document.getElementById('pagination');
        const { current_page, total_pages } = pagination;

        let html = '<div class="pagination-controls">';
        
        if (current_page > 1) {
            html += `<button onclick="clipboardManager.goToPage(${current_page - 1})">Previous</button>`;
        }

        html += `<span>Page ${current_page} of ${total_pages}</span>`;

        if (current_page < total_pages) {
            html += `<button onclick="clipboardManager.goToPage(${current_page + 1})">Next</button>`;
        }

        html += '</div>';
        container.innerHTML = html;
    },

    goToPage(page) {
        this.currentPage = page;
        this.loadClipboards();
    },

    sortBy(field) {
        if (this.sortField === field) {
            this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortField = field;
            this.sortOrder = 'asc';
        }
        this.loadClipboards();
    },

    async viewDetails(id) {
        try {
            const response = await adminAPI.get(`/api/admin/clipboards/${id}`);
            
            if (response.success) {
                this.showDetailModal(response.data);
            }
        } catch (error) {
            console.error('Error loading clipboard details:', error);
            alert('Failed to load clipboard details');
        }
    },

    showDetailModal(clipboard) {
        const content = document.getElementById('detail-content');
        
        content.innerHTML = `
            <div class="detail-section">
                <h3>Basic Information</h3>
                <p><strong>ID:</strong> ${clipboard.id}</p>
                <p><strong>Name:</strong> ${this.escapeHtml(clipboard.name)}</p>
                <p><strong>Description:</strong> ${this.escapeHtml(clipboard.description || 'N/A')}</p>
                <p><strong>Owner:</strong> ${this.escapeHtml(clipboard.owner_name)} (${this.escapeHtml(clipboard.owner_email)})</p>
                <p><strong>Type:</strong> ${clipboard.is_public ? 'Public' : 'Private'}</p>
                <p><strong>Max Subscribers:</strong> ${clipboard.max_subscribers || 'Unlimited'}</p>
                <p><strong>Max Items:</strong> ${clipboard.max_items || 'Unlimited'}</p>
                <p><strong>Created:</strong> ${this.formatDate(clipboard.created_at)}</p>
            </div>

            <div class="detail-section">
                <h3>Subscribers (${clipboard.subscribers.length})</h3>
                ${clipboard.subscribers.length > 0 ? `
                    <ul>
                        ${clipboard.subscribers.map(sub => `
                            <li>${this.escapeHtml(sub.name)} (${this.escapeHtml(sub.email)}) - Subscribed: ${this.formatDate(sub.subscribed_at)}</li>
                        `).join('')}
                    </ul>
                ` : '<p>No subscribers</p>'}
            </div>

            <div class="detail-section">
                <h3>Recent Items (${clipboard.recent_items.length})</h3>
                ${clipboard.recent_items.length > 0 ? `
                    <ul>
                        ${clipboard.recent_items.map(item => `
                            <li>${this.escapeHtml(item.title || item.content_type)} - ${this.formatDate(item.created_at)}</li>
                        `).join('')}
                    </ul>
                ` : '<p>No items</p>'}
            </div>
        `;

        document.getElementById('detail-modal').style.display = 'block';
    },

    closeDetailModal() {
        document.getElementById('detail-modal').style.display = 'none';
    },

    async editClipboard(id) {
        try {
            const response = await adminAPI.get(`/api/admin/clipboards/${id}`);
            
            if (response.success) {
                const clipboard = response.data;
                document.getElementById('edit-id').value = clipboard.id;
                document.getElementById('edit-name').value = clipboard.name;
                document.getElementById('edit-description').value = clipboard.description || '';
                document.getElementById('edit-public').checked = clipboard.is_public;
                document.getElementById('edit-max-subscribers').value = clipboard.max_subscribers || 0;
                document.getElementById('edit-max-items').value = clipboard.max_items || 0;
                
                document.getElementById('edit-modal').style.display = 'block';
            }
        } catch (error) {
            console.error('Error loading clipboard:', error);
            alert('Failed to load clipboard');
        }
    },

    async saveClipboard() {
        const id = document.getElementById('edit-id').value;
        const data = {
            name: document.getElementById('edit-name').value,
            description: document.getElementById('edit-description').value,
            is_public: document.getElementById('edit-public').checked ? 1 : 0,
            max_subscribers: parseInt(document.getElementById('edit-max-subscribers').value) || null,
            max_items: parseInt(document.getElementById('edit-max-items').value) || null
        };

        try {
            const response = await adminAPI.put(`/api/admin/clipboards/${id}`, data);
            
            if (response.success) {
                alert('Clipboard updated successfully');
                this.closeEditModal();
                this.loadClipboards();
            }
        } catch (error) {
            console.error('Error updating clipboard:', error);
            alert('Failed to update clipboard');
        }
    },

    closeEditModal() {
        document.getElementById('edit-modal').style.display = 'none';
    },

    async showTransferModal(id) {
        this.deleteId = id;
        await this.loadUsersForTransfer();
        document.getElementById('transfer-id').value = id;
        document.getElementById('transfer-modal').style.display = 'block';
    },

    async loadUsersForTransfer() {
        try {
            const response = await adminAPI.get('/api/admin/users?per_page=100');
            
            if (response.success) {
                const select = document.getElementById('transfer-owner');
                select.innerHTML = '<option value="">Select a user...</option>' +
                    response.data.users.map(user => 
                        `<option value="${user.id}">${this.escapeHtml(user.name)} (${this.escapeHtml(user.email)})</option>`
                    ).join('');
            }
        } catch (error) {
            console.error('Error loading users:', error);
        }
    },

    async transferOwnership() {
        const id = document.getElementById('transfer-id').value;
        const newOwnerId = document.getElementById('transfer-owner').value;

        if (!newOwnerId) {
            alert('Please select a new owner');
            return;
        }

        try {
            const response = await adminAPI.post(`/api/admin/clipboards/${id}/transfer`, {
                new_owner_id: parseInt(newOwnerId)
            });
            
            if (response.success) {
                alert('Ownership transferred successfully');
                this.closeTransferModal();
                this.loadClipboards();
            }
        } catch (error) {
            console.error('Error transferring ownership:', error);
            alert('Failed to transfer ownership');
        }
    },

    closeTransferModal() {
        document.getElementById('transfer-modal').style.display = 'none';
    },

    deleteClipboard(id) {
        this.deleteId = id;
        document.getElementById('delete-modal').style.display = 'block';
    },

    async confirmDelete() {
        try {
            const response = await adminAPI.delete(`/api/admin/clipboards/${this.deleteId}`);
            
            if (response.success) {
                alert('Clipboard deleted successfully');
                this.closeDeleteModal();
                this.loadClipboards();
            }
        } catch (error) {
            console.error('Error deleting clipboard:', error);
            alert('Failed to delete clipboard');
        }
    },

    closeDeleteModal() {
        document.getElementById('delete-modal').style.display = 'none';
        this.deleteId = null;
    },

    async loadOwners() {
        try {
            const response = await adminAPI.get('/api/admin/users?per_page=100');
            
            if (response.success) {
                const select = document.getElementById('filter-owner');
                select.innerHTML = '<option value="">All Owners</option>' +
                    response.data.users.map(user => 
                        `<option value="${user.id}">${this.escapeHtml(user.name)}</option>`
                    ).join('');
            }
        } catch (error) {
            console.error('Error loading owners:', error);
        }
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
    clipboardManager.init();
});

// Close modals when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
};
