// Auth functionality
let currentUser = null;

// Get base path for the application
const getAppBasePath = () => {
    const path = window.location.pathname;

    const split = path.split("/");
    console.log(split);
    const i = split.findIndex((a) => a === 'public');
    const sliced = split.slice(0, i);
    const joined = sliced.join('/');
    console.log(joined);
    return joined + '/public';
    // const match = path.match(/^(\/[^\/]+)\/public\//);
    // return match ? match[1] + '/public' : '';
};

// Check if user is logged in
async function checkAuth() {
    try {
        const response = await api.getCurrentUser();
        currentUser = response.user;
        return true;
    } catch (error) {
        currentUser = null;
        return false;
    }
}

// Redirect to login if not authenticated
async function requireAuth() {
    const isAuthenticated = await checkAuth();
    if (!isAuthenticated) {
        window.location.href = `${getAppBasePath()}/login.html`;
        return false;
    }
    return true;
}

// Update UI with user info
function updateAuthUI() {
    const authLinks = document.querySelector('.auth-links-nav');
    if (!authLinks) return;
    
    const basePath = getAppBasePath();
    console.log(basePath); 
    if (currentUser) {
        authLinks.innerHTML = `
            <span>Welcome, ${escapeHtml(currentUser.name)}</span>
            <button id="logoutBtn" class="btn btn-sm btn-secondary">Logout</button>
        `;
        
        document.getElementById('logoutBtn')?.addEventListener('click', handleLogout);
    } else {
        authLinks.innerHTML = `
            <a href="${basePath}/login.html" class="btn btn-sm btn-primary">Login</a>
            <a href="${basePath}/register.html" class="btn btn-sm btn-secondary">Register</a>
        `;
    }
}

// Handle logout
async function handleLogout() {
    try {
        await api.logout();
        window.location.href = `${getAppBasePath()}/index.html`;
    } catch (error) {
        console.error('Logout failed:', error);
        alert('Logout failed. Please try again.');
    }
}

// Handle login form
if (document.getElementById('loginForm')) {
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const errorDiv = document.getElementById('errorMessage');
        errorDiv.style.display = 'none';
        
        const formData = new FormData(e.target);
        const credentials = {
            email: formData.get('email'),
            password: formData.get('password')
        };
        
        try {
            const response = await api.login(credentials);
            currentUser = response.user;
            
            // Redirect to dashboard
            window.location.href = `${getAppBasePath()}/dashboard.html`;
        } catch (error) {
            errorDiv.textContent = error.message;
            errorDiv.style.display = 'block';
        }
    });
}

// Utility function
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
