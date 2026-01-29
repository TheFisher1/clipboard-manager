// API base URL - use APP_BASE_PATH if available (injected by server)
const API_BASE_URL = (window.APP_BASE_PATH || '') + '/api';

class ClipboardAPI {
    async request(endpoint, options = {}) {
        const url = `${API_BASE_URL}${endpoint}`;
        const config = {
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        };

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Request failed');
            }

            return data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    async requestBlob(endpoint, options = {}) {
        const url = `${API_BASE_URL}${endpoint}`;
        const config = {
            credentials: 'include',
            ...options
        };

        try {
            const response = await fetch(url, config);

            if (!response.ok) {
                throw new Error(`Request failed: ${response.status}`);
            }

            return await response.blob();
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    async login(credentials) {
        return this.request('/auth/login', {
            method: 'POST',
            body: JSON.stringify(credentials)
        });
    }

    async register(userData) {
        return this.request('/auth/register', {
            method: 'POST',
            body: JSON.stringify(userData)
        });
    }

    async logout() {
        return this.request('/auth/logout', {
            method: 'POST'
        });
    }

    async getCurrentUser() {
        return this.request('/auth/me');
    }

    async getClipboards() {
        return this.request('/clipboards');
    }

    async getClipboard(id) {
        return this.request(`/clipboards/${id}`);
    }

    async createClipboard(data) {
        return this.request('/clipboards', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    async updateClipboard(id, data) {
        return this.request(`/clipboards/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    async deleteClipboard(id) {
        return this.request(`/clipboards/${id}`, {
            method: 'DELETE'
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
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    async updateItem(clipboardId, itemId, data) {
        return this.request(`/clipboards/${clipboardId}/items/${itemId}`, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    async deleteItem(itemId) {
        return this.request(`/items/${itemId}`, {
            method: 'DELETE'
        });
    }

    async createItemFile(clipboardId, data) {
        return this.request(`/clipboards/${clipboardId}/items/file`, {
            method: 'POST',
            body: data,
        });
    }

    async getItemBlob(itemId, endpoint = 'view') {
        const res = await fetch(
            `${API_BASE_URL}items/${itemId}/${endpoint}`,
            { credentials: 'include' }
        );

        if (!res.ok) {
            throw new Error('Failed to fetch file');
        }

        return await res.blob();
    }

    async viewItemFile(itemId) {
        const blob = await this.requestBlob(`/items/${itemId}/view`);
        const url = URL.createObjectURL(blob);

        window.open(url, '_blank');
    }

    async downloadItemFile(itemId, filename = 'download') {
        const blob = await this.requestBlob(`/items/${itemId}/download`);
        const url = URL.createObjectURL(blob);
        
        const a = document.createElement('a');
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
