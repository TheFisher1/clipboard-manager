// Dashboard state
let dashboardState = {
    stats: null,
    activities: []
};

// Initialize dashboard on page load
document.addEventListener('DOMContentLoaded', () => {
    loadDashboard();
});

/**
 * Load all dashboard data
 */
async function loadDashboard() {
    await Promise.all([
        loadStatistics(),
        loadRecentActivity()
    ]);
}

/**
 * Load dashboard statistics
 */
async function loadStatistics() {
    const container = document.getElementById('stats-container');
    
    try {
        showLoading(container);
        
        const response = await adminAPI.getDashboardStats();
        dashboardState.stats = response.data;
        
        renderStatistics(dashboardState.stats);
    } catch (error) {
        console.error('Failed to load statistics:', error);
        showError(container, 'Failed to load statistics. Please try again.');
    }
}

/**
 * Load recent activity feed
 */
async function loadRecentActivity() {
    const container = document.getElementById('activity-feed');
    
    try {
        showLoading(container);
        
        const response = await adminAPI.getRecentActivity(20);
        dashboardState.activities = response.data.activities;
        
        renderActivityFeed(dashboardState.activities);
    } catch (error) {
        console.error('Failed to load activity:', error);
        showError(container, 'Failed to load recent activity. Please try again.');
    }
}

/**
 * Render statistics cards
 */
function renderStatistics(stats) {
    const container = document.getElementById('stats-container');
    
    const cards = [
        {
            title: 'Total Users',
            value: stats.total_users,
            subtitle: `${stats.new_users_week} new this week`,
            colorClass: 'primary'
        },
        {
            title: 'Total Clipboards',
            value: stats.total_clipboards,
            subtitle: `${stats.public_clipboards} public, ${stats.total_clipboards - stats.public_clipboards} private`,
            colorClass: 'success'
        },
        {
            title: 'Total Items',
            value: stats.total_items,
            subtitle: `${stats.active_items} active items`,
            colorClass: 'info'
        },
        {
            title: 'Subscriptions',
            value: stats.total_subscriptions,
            subtitle: 'Active subscriptions',
            colorClass: 'warning'
        },
        {
            title: 'Today\'s Activity',
            value: stats.today_activities,
            subtitle: 'Actions today',
            colorClass: 'primary'
        },
        {
            title: 'Storage Used',
            value: formatBytes(stats.total_storage_bytes),
            subtitle: `${stats.total_files} files`,
            colorClass: 'danger'
        }
    ];
    
    container.innerHTML = cards.map(card => createStatCard(card)).join('');
}

/**
 * Create a single stat card HTML
 */
function createStatCard(card) {
    const colorClass = card.colorClass || '';
    return `
        <div class="stat-card ${colorClass}">
            <h3>${card.title}</h3>
            <div class="stat-value">${card.value}</div>
            ${card.subtitle ? `<p class="stat-subtitle">${card.subtitle}</p>` : ''}
        </div>
    `;
}

/**
 * Render activity feed
 */
function renderActivityFeed(activities) {
    const container = document.getElementById('activity-feed');
    
    if (!activities || activities.length === 0) {
        container.innerHTML = '<p class="text-center">No recent activity</p>';
        return;
    }
    
    container.innerHTML = activities.map(activity => createActivityItem(activity)).join('');
}

/**
 * Create a single activity item HTML
 */
function createActivityItem(activity) {
    const icon = getActivityIcon(activity.action_type);
    const description = getActivityDescription(activity);
    const timeAgo = formatTimeAgo(activity.created_at);
    
    return `
        <div class="activity-item" data-action="${activity.action_type}">
            <div class="activity-icon">${icon}</div>
            <div class="activity-details">
                <p><strong>${activity.user_name}</strong> ${description}</p>
                <p class="activity-time">${timeAgo}</p>
            </div>
        </div>
    `;
}

/**
 * Get icon for activity type
 */
function getActivityIcon(actionType) {
    const icons = {
        'create': '➕',
        'view': '👁️',
        'download': '⬇️',
        'delete': '🗑️',
        'expire': '⏰',
        'share': '📤'
    };
    
    return icons[actionType] || '📋';
}

/**
 * Get human-readable description for activity
 */
function getActivityDescription(activity) {
    const clipboardName = activity.clipboard_name || 'a clipboard';
    const itemTitle = activity.item_title || 'an item';
    
    switch (activity.action_type) {
        case 'create':
            if (activity.item_title) {
                return `created item "${itemTitle}" in ${clipboardName}`;
            }
            return `created clipboard "${clipboardName}"`;
        case 'view':
            return `viewed ${itemTitle} in ${clipboardName}`;
        case 'download':
            return `downloaded ${itemTitle} from ${clipboardName}`;
        case 'delete':
            return `deleted ${itemTitle} from ${clipboardName}`;
        case 'expire':
            return `content expired in ${clipboardName}`;
        case 'share':
            return `shared ${itemTitle} in ${clipboardName}`;
        default:
            return `performed action on ${clipboardName}`;
    }
}

/**
 * Format bytes to human-readable size
 */
function formatBytes(bytes) {
    if (bytes === 0) return '0 B';
    
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

/**
 * Format timestamp to relative time
 */
function formatTimeAgo(timestamp) {
    const now = new Date();
    const past = new Date(timestamp);
    const diffMs = now - past;
    const diffSec = Math.floor(diffMs / 1000);
    const diffMin = Math.floor(diffSec / 60);
    const diffHour = Math.floor(diffMin / 60);
    const diffDay = Math.floor(diffHour / 24);
    
    if (diffSec < 60) {
        return 'just now';
    } else if (diffMin < 60) {
        return `${diffMin} minute${diffMin !== 1 ? 's' : ''} ago`;
    } else if (diffHour < 24) {
        return `${diffHour} hour${diffHour !== 1 ? 's' : ''} ago`;
    } else if (diffDay < 7) {
        return `${diffDay} day${diffDay !== 1 ? 's' : ''} ago`;
    } else {
        return past.toLocaleDateString();
    }
}

/**
 * Refresh dashboard data
 */
function refreshDashboard() {
    loadDashboard();
}
