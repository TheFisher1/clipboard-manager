// Activity Log Management
const activityManager = {
    async init() {
        await this.loadActivity();
    },

    async loadActivity() {
        const dateFrom = document.getElementById('filter-date-from').value;
        const dateTo = document.getElementById('filter-date-to').value;
        const actionType = document.getElementById('filter-action-type').value;

        const params = new URLSearchParams({ limit: 100 });
        if (dateFrom) params.append('date_from', dateFrom);
        if (dateTo) params.append('date_to', dateTo);
        if (actionType) params.append('action_type', actionType);

        try {
            const response = await adminAPI.get(`/api/admin/activity?${params}`);
            
            if (response.success) {
                this.renderActivity(response.data);
            }
        } catch (error) {
            console.error('Error loading activity:', error);
            alert('Failed to load activity logs');
        }
    },

    renderActivity(activities) {
        const tbody = document.getElementById('activity-table-body');
        
        if (activities.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="no-data">No activity found</td></tr>';
            return;
        }

        tbody.innerHTML = activities.map(activity => `
            <tr>
                <td>${activity.id}</td>
                <td>${this.escapeHtml(activity.user_name || 'Unknown')}</td>
                <td><span class="badge">${this.escapeHtml(activity.action_type)}</span></td>
                <td>${this.escapeHtml(activity.clipboard_name || 'N/A')}</td>
                <td>${this.formatDate(activity.created_at)}</td>
                <td class="actions">
                    <button onclick="activityManager.viewDetails(${activity.id})" class="btn-sm btn-info">View</button>
                </td>
            </tr>
        `).join('');
    },

    async viewDetails(id) {
        // For simplicity, find the activity in the current data
        const params = new URLSearchParams({ limit: 100 });
        const response = await adminAPI.get(`/api/admin/activity?${params}`);
        const activity = response.data.find(a => a.id === id);
        
        if (activity) {
            this.showDetailModal(activity);
        }
    },

    showDetailModal(activity) {
        const content = document.getElementById('detail-content');
        
        content.innerHTML = `
            <div class="detail-section">
                <p><strong>ID:</strong> ${activity.id}</p>
                <p><strong>User:</strong> ${this.escapeHtml(activity.user_name)} (${this.escapeHtml(activity.user_email || '')})</p>
                <p><strong>Action:</strong> ${this.escapeHtml(activity.action_type)}</p>
                <p><strong>Clipboard:</strong> ${this.escapeHtml(activity.clipboard_name || 'N/A')}</p>
                <p><strong>Date:</strong> ${this.formatDate(activity.created_at)}</p>
                <p><strong>IP Address:</strong> ${this.escapeHtml(activity.ip_address || 'N/A')}</p>
                ${activity.details ? `<p><strong>Details:</strong> <pre>${JSON.stringify(activity.details, null, 2)}</pre></p>` : ''}
            </div>
        `;

        document.getElementById('detail-modal').style.display = 'block';
    },

    closeDetailModal() {
        document.getElementById('detail-modal').style.display = 'none';
    },

    async exportActivity() {
        const dateFrom = document.getElementById('filter-date-from').value;
        const dateTo = document.getElementById('filter-date-to').value;

        const params = new URLSearchParams({ format: 'csv' });
        if (dateFrom) params.append('date_from', dateFrom);
        if (dateTo) params.append('date_to', dateTo);

        try {
            window.location.href = `/api/admin/activity/export?${params}`;
        } catch (error) {
            console.error('Error exporting activity:', error);
            alert('Failed to export activity logs');
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
    activityManager.init();
});

// Close modals when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
};
