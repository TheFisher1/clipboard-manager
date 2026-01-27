// Check authentication on page load
(async function() {
    try {
        const response = await api.getCurrentUser();
        currentUser = response.user;
        loadClipboards();
    } catch (error) {
        console.log(error);
        // Not authenticated, redirect to login
        window.location.href = '/login.html';
    }
})();

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