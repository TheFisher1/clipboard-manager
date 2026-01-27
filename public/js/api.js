const API_BASE_URL = '/api';

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

    async deleteItem(clipboardId, itemId) {
        return this.request(`/clipboards/${clipboardId}/items/${itemId}`, {
            method: 'DELETE'
        });
    }
}

const api = new ClipboardAPI();
