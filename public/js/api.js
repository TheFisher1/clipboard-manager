// Automatically detect the base path from the current location
const getBasePath = () => {
    const path = window.location.pathname;
    console.log("Current pathname:", path);
    // If we're in /some_folder/public/..., extract /some_folder
    // const match = path.match(/^(\/[^\/]+)\/public\//);
    // const match = path.match(/^(\/.+?)\/public\//);
    const split = path.split("/");
    console.log(split);
    const i = split.findIndex((a) => a === 'public');
    const sliced = split.slice(0, i);
    const joined = sliced.join('/');
    console.log(joined);
    return joined;
    // console.log('Regex match:', match);
    // const basePath = match ? match[1] : "";
    // console.log("Base path:", basePath);
    // return basePath;
};

const API_BASE_URL = `${getBasePath()}/api`;
// console.log('API_BASE_URL set to:', API_BASE_URL);

class ClipboardAPI {
    async request(endpoint, options = {}) {
        const url = `${API_BASE_URL}${endpoint}`;

        // Don't set Content-Type for FormData - browser will set it automatically with boundary
        const headers = {};
        if (!(options.body instanceof FormData)) {
            headers["Content-Type"] = "application/json";
        }

        const config = {
            credentials: "same-origin",
            headers: {
                ...headers,
                ...options.headers,
            },
            ...options,
        };

        try {
            const response = await fetch(url, config);

            // Get the response text first to handle non-JSON responses
            const text = await response.text();
            let data;

            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error("Failed to parse JSON response:", text);
                throw new Error(
                    `Server returned invalid JSON: ${text.substring(0, 100)}`,
                );
            }

            if (!response.ok) {
                throw new Error(data.error || "Request failed");
            }

            return data;
        } catch (error) {
            console.error("API Error:", error);
            throw error;
        }
    }

    async requestBlob(endpoint, options = {}) {
        const url = `${API_BASE_URL}${endpoint}`;
        const config = {
            credentials: "include",
            ...options,
        };

        try {
            const response = await fetch(url, config);

            if (!response.ok) {
                throw new Error(`Request failed: ${response.status}`);
            }

            return await response.blob();
        } catch (error) {
            console.error("API Error:", error);
            throw error;
        }
    }

    async login(credentials) {
        return this.request("/auth/login", {
            method: "POST",
            body: JSON.stringify(credentials),
        });
    }

    async register(userData) {
        return this.request("/auth/register", {
            method: "POST",
            body: JSON.stringify(userData),
        });
    }

    async logout() {
        return this.request("/auth/logout", {
            method: "POST",
        });
    }

    async getCurrentUser() {
        return this.request("/auth/me");
    }

    async getUserById(id) {
        return this.request(`/users/${id}`);
    }

    async getClipboards() {
        return this.request("/clipboards");
    }

    async getMyClipboards() {
        return this.request("/clipboards/mine");
    }

    async searchClipboards(keywordString) {
        return this.request(`/clipboards/search/${keywordString}`);
    }

    async getClipboard(id) {
        return this.request(`/clipboards/${id}`);
    }

    async createClipboard(data) {
        return this.request("/clipboards", {
            method: "POST",
            body: JSON.stringify(data),
        });
    }

    async updateClipboard(id, data) {
        return this.request(`/clipboards/${id}`, {
            method: "PUT",
            body: JSON.stringify(data),
        });
    }

    async deleteClipboard(id) {
        return this.request(`/clipboards/${id}`, {
            method: "DELETE",
        });
    }

    async getClipboardItems(clipboardId) {
        return this.request(`/clipboards/${clipboardId}/items`);
    }

    async getItem(clipboardId, itemId) {
        return this.request(`/clipboards/${clipboardId}/items/${itemId}`);
    }

    async createItem(clipboardId, data) {
        return this.request(`/clipboards/${clipboardId}/items`, {
            method: "POST",
            body: JSON.stringify(data),
        });
    }

    async updateItem(clipboardId, itemId, data) {
        return this.request(`/clipboards/${clipboardId}/items/${itemId}`, {
            method: "PUT",
            body: JSON.stringify(data),
        });
    }

    async deleteItem(itemId) {
        return this.request(`/items/${itemId}`, {
            method: "DELETE",
        });
    }

    async createItemFile(clipboardId, data) {
        return this.request(`/clipboards/${clipboardId}/items/file`, {
            method: "POST",
            body: data,
        });
    }

    async getItemBlob(itemId, endpoint = "view") {
        const res = await fetch(`${API_BASE_URL}items/${itemId}/${endpoint}`, {
            credentials: "include",
        });

        if (!res.ok) {
            throw new Error("Failed to fetch file");
        }

        return await res.blob();
    }

    async viewItemFile(itemId) {
        const blob = await this.requestBlob(`/items/${itemId}/view`);
        const url = URL.createObjectURL(blob);

        window.open(url, "_blank");
    }

    async downloadItemFile(itemId) {
        const blob = await this.requestBlob(`/items/${itemId}/download`);
        const url = URL.createObjectURL(blob);

        const a = document.createElement("a");
        a.href = url;
        a.download = filename;
        a.click();

        URL.revokeObjectURL(url);
    }

    getFileViewUrl(itemId) {
        return `${API_BASE_URL}/items/${itemId}/view`;
    }

    getFileDownloadUrl(itemId) {
        return `${API_BASE_URL}/items/${itemId}/download`;
    }
}

const api = new ClipboardAPI();
