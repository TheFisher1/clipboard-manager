// Public view for shared clipboards - no authentication required

let currentClipboardId = null;

// Get clipboard ID from URL
const urlParams = new URLSearchParams(window.location.search);
const clipboardId = urlParams.get('id');

if (!clipboardId) {
    document.getElementById('clipboardName').textContent = 'Invalid Link';
    document.getElementById('clipboardDescription').textContent = 'No clipboard ID provided.';
} else {
    loadClipboard(clipboardId);
}

async function loadClipboard(id) {
    currentClipboardId = id;
    
    try {
        const clipboard = await api.getClipboard(id);
        document.getElementById('clipboardName').textContent = clipboard.name;
        document.getElementById('clipboardDescription').textContent = 
            clipboard.description || 'No description';
        
        await loadItems(id);
    } catch (error) {
        document.getElementById('clipboardName').textContent = 'Error';
        document.getElementById('clipboardDescription').textContent = 
            'Failed to load clipboard: ' + error.message;
    }
}

async function loadItems(clipboardId) {
    const container = document.getElementById('itemsList');
    try {
        const items = await api.getClipboardItems(clipboardId);
        if (items.length === 0) {
            container.innerHTML = '<p class="loading">No items in this clipboard.</p>';
            return;
        }

        container.innerHTML = items.map(item => `
            <div class="item-card ${item.content_type}-card" data-id="${item.id}">
                <div class="item-header">
                    <h4>${escapeHtml(item.title || 'Untitled')}</h4>
                    <div class="item-actions">
                        ${(item.content_type === 'text' || item.content_type === 'code') ? 
                            `<button class="btn btn-sm btn-secondary btn-copy-text" data-content="${escapeHtml(item.content_text || '')}" onclick="copyToClipboard(event)" title="Copy to clipboard">Copy</button>` : ''}
                    </div>
                </div>

                <div class="item-subheader">
                    <span class="type-badge">${item.content_type}</span>
                </div>

                ${renderItemPreview(item)}

                <p class="item-description">${escapeHtml(item.description || '')}</p>

                <div class="clipboard-meta">
                    <span>Views: ${item.view_count}</span>
                    <span>Created: ${formatDate(item.created_at)}</span>
                </div>
            </div>
        `).join('');

        document.querySelectorAll('pre code').forEach(block => {
            hljs.highlightElement(block);
        });

    } catch (error) {
        container.innerHTML = `<div class="error">Failed to load items: ${error.message}</div>`;
    }
}

function renderItemPreview(item) {
    switch (item.content_type) {
        case 'image':
            return `
                <div class="item-preview image-preview">
                    <img src="${api.getFileViewUrl(item.id)}" 
                         alt="${escapeHtml(item.title)}" 
                         loading="lazy" 
                         onclick="window.open('${api.getFileViewUrl(item.id)}', '_blank')">
                </div>`;
                
        case 'file':
            return `
                <div class="item-preview file-preview">
                    <div class="file-info">
                        <span class="file-icon">📁</span>
                        <div class="file-details">
                            <span class="file-name">${escapeHtml(item.file_name || 'Download File')}</span>
                            <span class="file-size">${(item.file_size / 1024).toFixed(1)} KB</span>
                        </div>
                    </div>
                    <a href="${api.getFileViewUrl(item.id)}" download="${item.file_name}" class="btn btn-sm btn-secondary">
                        Download
                    </a>
                </div>`;
                
        case 'code':
            return `
                <div class="item-preview code-preview">
                    <pre><code>${escapeHtml(item.content_text)}</code></pre>
                </div>`;
                
        default:
            return item.content_text ? 
                `<p class="item-text-preview">${escapeHtml(item.content_text)}...</p>` : '';
    }
}

async function copyToClipboard(event) {
    try {
        const button = event.target;
        const text = button.getAttribute('data-content');
        
        if (!text) {
            return;
        }

        // Try modern Clipboard API first
        if (navigator.clipboard && navigator.clipboard.writeText) {
            try {
                await navigator.clipboard.writeText(text);
            } catch (clipboardError) {
                fallbackCopyToClipboard(text);
            }
        } else {
            fallbackCopyToClipboard(text);
        }
        
        // Visual feedback
        const originalText = button.textContent;
        const originalBg = button.style.backgroundColor;
        
        button.textContent = 'Copied!';
        button.style.backgroundColor = '#4CAF50';
        
        setTimeout(() => {
            button.textContent = originalText;
            button.style.backgroundColor = originalBg;
        }, 2000);
        
    } catch (error) {
        console.error('Copy failed:', error);
    }
}

function fallbackCopyToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        textArea.remove();
    } catch (err) {
        textArea.remove();
        throw new Error('Fallback copy failed');
    }
}

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
