const getAppBasePath = () => {
    const path = window.location.pathname;

    const split = path.split("/");
    console.log(split);
    const i = split.findIndex((a) => a === "public");
    const sliced = split.slice(0, i);
    const joined = sliced.join("/");
    console.log(joined);
    return joined + "/public";
    // const match = path.match(/^(\/[^\/]+)\/public\//);
    // return match ? match[1] + '/public' : '';
};
// Navigation auth state management
(async function () {
    const navAuth = document.getElementById("navAuth");
    if (!navAuth) return;

    try {
        const response = await api.getCurrentUser();
        const user = response.user;

        // User is logged in
        const adminBtn = user.is_admin
            ? `<a href="../admin/dashboard.php" class="btn btn-sm btn-primary">Admin</a>`
            : '';
        navAuth.innerHTML = `
            <span class="user-name">Welcome, ${escapeHtml(user.name)}</span>
            ${adminBtn}
            <a href="dashboard.html" class="btn btn-sm btn-primary">Dashboard</a>
            <a id="navLogoutBtn" class="btn btn-sm btn-secondary">Logout</a>
        `;

        document
            .getElementById("navLogoutBtn")
            ?.addEventListener("click", async () => {
                try {
                    await api.logout();
                    // current = window.location.href;
                    base_path = getAppBasePath();
                    window.location.href = base_path + "/index.html";
                } catch (error) {
                    console.error("Logout failed:", error);
                }
            });
    } catch (error) {
        // User is not logged in
        navAuth.innerHTML = `
            <a href="login.html" class="btn btn-sm btn-primary">Login</a>
            <a href="register.html" class="btn btn-sm btn-secondary">Register</a>
        `;
    }
})();

function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}
