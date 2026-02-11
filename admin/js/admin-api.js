class AdminAPI {
    constructor() {
        // Use BASE_PATH if defined (from PHP), otherwise detect from current location
        // const basePath = typeof BASE_PATH !== 'undefined' ? BASE_PATH : this.detectBasePath();
        const basePath = this.detectBasePath();
        this.baseURL = `${basePath}/api/admin`;
        
    }

    detectBasePath() {
        const path = window.location.pathname;
        const split = path.split("/");
        console.log(split);
        const i = split.findIndex((a) => a === "admin");
        const sliced = split.slice(0, i);
        const joined = sliced.join("/");
        console.log(joined);
        return joined;
        // If we're in /some_folder/admin/..., extract /some_folder
        //  match = path.match(/^(\/[^\/]+)\/admin\//);
        // return match ? match[1] : '';
    }

    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            ...options,
            headers: {
                "Content-Type": "application/json",
                ...options.headers,
            },
            credentials: "include",
        };

        try {
            const response = await fetch(url, config);
            const text = await response.text();

            // Try to parse as JSON
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error("Invalid JSON response:", text);
                throw new Error(
                    "Server returned invalid JSON: " + text.substring(0, 200),
                );
            }

            if (!response.ok) {
                console.error("API Error Response:", data);
                throw new Error(data.error?.message || "Request failed");
            }

            return data;
        } catch (error) {
            console.error("API Error:", error);
            throw error;
        }
    }

    // Generic HTTP methods
    async get(url) {
        // If URL starts with /api/admin, remove the baseURL prefix
        const endpoint = url.startsWith("/api/admin")
            ? url.replace("/api/admin", "")
            : url;
        return this.request(endpoint, { method: "GET" });
    }

    async post(url, data) {
        const endpoint = url.startsWith("/api/admin")
            ? url.replace("/api/admin", "")
            : url;
        return this.request(endpoint, {
            method: "POST",
            body: JSON.stringify(data),
        });
    }

    async put(url, data) {
        const endpoint = url.startsWith("/api/admin")
            ? url.replace("/api/admin", "")
            : url;
        return this.request(endpoint, {
            method: "PUT",
            body: JSON.stringify(data),
        });
    }

    async delete(url, data = null) {
        const endpoint = url.startsWith("/api/admin")
            ? url.replace("/api/admin", "")
            : url;
        return this.request(endpoint, {
            method: "DELETE",
            body: data ? JSON.stringify(data) : undefined,
        });
    }

    async getUsers(params = {}) {
        const query = new URLSearchParams(params).toString();
        return this.request(`/users?${query}`);
    }

    async getUser(userId) {
        return this.request(`/users/${userId}`);
    }

    async updateUser(userId, data) {
        return this.request(`/users/${userId}`, {
            method: "PUT",
            body: JSON.stringify(data),
        });
    }

    async deleteUser(userId) {
        return this.request(`/users/${userId}`, {
            method: "DELETE",
        });
    }

    async resetPassword(userId, newPassword) {
        return this.request(`/users/${userId}/reset-password`, {
            method: "POST",
            body: JSON.stringify({ new_password: newPassword }),
        });
    }

    async getDashboardStats() {
        return this.request("/dashboard/stats");
    }

    async getRecentActivity(limit = 20) {
        return this.request(`/dashboard/recent-activity?limit=${limit}`);
    }

    async getClipboards(params = {}) {
        const query = new URLSearchParams(params).toString();
        return this.request(`/clipboards?${query}`);
    }

    async getClipboard(clipboardId) {
        return this.request(`/clipboards/${clipboardId}`);
    }

    async updateClipboard(clipboardId, data) {
        return this.request(`/clipboards/${clipboardId}`, {
            method: "PUT",
            body: JSON.stringify(data),
        });
    }

    async deleteClipboard(clipboardId) {
        return this.request(`/clipboards/${clipboardId}`, {
            method: "DELETE",
        });
    }

    async transferClipboard(clipboardId, newOwnerId) {
        return this.request(`/clipboards/${clipboardId}/transfer`, {
            method: "POST",
            body: JSON.stringify({ new_owner_id: newOwnerId }),
        });
    }

    // Content endpoints
    async getContent(params = {}) {
        const query = new URLSearchParams(params).toString();
        return this.request(`/content?${query}`);
    }

    async getContentItem(contentId) {
        return this.request(`/content/${contentId}`);
    }

    async deleteContent(contentId, reason = "") {
        return this.request(`/content/${contentId}`, {
            method: "DELETE",
            body: JSON.stringify({ reason }),
        });
    }

    async bulkDeleteContent(contentIds, reason = "") {
        return this.request("/content/bulk-delete", {
            method: "POST",
            body: JSON.stringify({ content_ids: contentIds, reason }),
        });
    }

    // Activity endpoints
    async getActivityLogs(params = {}) {
        const query = new URLSearchParams(params).toString();
        return this.request(`/activity?${query}`);
    }

    async getAuditLogs(params = {}) {
        const query = new URLSearchParams(params).toString();
        return this.request(`/audit?${query}`);
    }

    async exportActivityLogs(params = {}) {
        const query = new URLSearchParams(params).toString();
        return this.request(`/activity/export?${query}`);
    }

    // Settings endpoints
    async getSettings() {
        return this.request("/settings");
    }

    async getSetting(key) {
        return this.request(`/settings/${key}`);
    }

    async updateSetting(key, value) {
        return this.request(`/settings/${key}`, {
            method: "PUT",
            body: JSON.stringify({ setting_value: value }),
        });
    }
}

// Create global instance
const adminAPI = new AdminAPI();

// Utility functions
function showLoading(element) {
    if (element) {
        element.innerHTML = "<p>Loading...</p>";
    }
}

function showError(element, message) {
    if (element) {
        element.innerHTML = `<p class="error">${message}</p>`;
    }
}

function logout() {
    const basePath = typeof BASE_PATH !== "undefined" ? BASE_PATH : "";
    fetch(`${basePath}/api/auth/logout`, {
        method: "POST",
        credentials: "include",
    }).then(() => {
        window.location.href = `${basePath}/public/login.html`;
    });
}
