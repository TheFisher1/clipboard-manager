// Navigation auth state management
(async function() {
    const navAuth = document.getElementById('navAuth');
    if (!navAuth) return;

    const basePath = window.APP_BASE_PATH || '';

    try {
        const response = await api.getCurrentUser();
        const user = response.user;
        
        // User is logged in
        navAuth.innerHTML = `
            <span class="user-name">Welcome, ${escapeHtml(user.name)}</span>
            <a href="${basePath}/public/dashboard.html" class="btn btn-sm btn-primary">Dashboard</a>
            <a id="navLogoutBtn" class="btn btn-sm btn-secondary">Logout</a>
        `;
        
        document.getElementById('navLogoutBtn')?.addEventListener('click', async () => {
            try {
                await api.logout();
                window.location.href = basePath + '/public/index.html';
            } catch (error) {
                console.error('Logout failed:', error);
            }
        });
    } catch (error) {
        // User is not logged in
        navAuth.innerHTML = `
            <a href="${basePath}/public/login.html" class="btn btn-sm btn-primary">Login</a>
            <a href="${basePath}/public/register.html" class="btn btn-sm btn-secondary">Register</a>
        `;
    }
})();

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
