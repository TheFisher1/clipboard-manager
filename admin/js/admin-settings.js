// Settings Management
const settingsManager = {
    settings: [],

    async init() {
        await this.loadSettings();
    },

    async loadSettings() {
        try {
            const response = await adminAPI.get('/api/admin/settings');
            
            if (response.success) {
                this.settings = response.data;
                this.renderSettings();
            }
        } catch (error) {
            console.error('Error loading settings:', error);
            document.getElementById('settings-container').innerHTML = '<p class="error">Failed to load settings</p>';
        }
    },

    renderSettings() {
        const container = document.getElementById('settings-container');
        
        // Group settings by category
        const grouped = {};
        this.settings.forEach(setting => {
            const category = setting.category || 'General';
            if (!grouped[category]) {
                grouped[category] = [];
            }
            grouped[category].push(setting);
        });

        let html = '<form id="settings-form">';
        
        for (const [category, settings] of Object.entries(grouped)) {
            html += `
                <div class="settings-category">
                    <h3>${this.escapeHtml(category)}</h3>
                    <div class="settings-group">
            `;
            
            settings.forEach(setting => {
                html += `
                    <div class="form-group">
                        <label for="setting-${setting.setting_key}">
                            ${this.escapeHtml(setting.setting_key)}
                            ${setting.description ? `<span class="help-text">${this.escapeHtml(setting.description)}</span>` : ''}
                        </label>
                        <input 
                            type="text" 
                            id="setting-${setting.setting_key}" 
                            name="${setting.setting_key}"
                            value="${this.escapeHtml(setting.setting_value || '')}"
                            data-key="${setting.setting_key}"
                        >
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        }
        
        html += `
            <div class="form-actions">
                <button type="submit" class="btn-primary">Save Settings</button>
            </div>
        </form>
        `;
        
        container.innerHTML = html;
        
        document.getElementById('settings-form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveSettings();
        });
    },

    async saveSettings() {
        const form = document.getElementById('settings-form');
        const formData = new FormData(form);
        const updates = [];

        for (const [key, value] of formData.entries()) {
            updates.push({ key, value });
        }

        try {
            for (const update of updates) {
                await adminAPI.put(`/api/admin/settings/${update.key}`, {
                    setting_value: update.value
                });
            }
            
            alert('Settings saved successfully');
            this.loadSettings();
        } catch (error) {
            console.error('Error saving settings:', error);
            alert('Failed to save settings');
        }
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
    settingsManager.init();
});
