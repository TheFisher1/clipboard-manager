// User Management
let currentPage = 1;
let currentFilters = {};
let deleteUserId = null;
let currentSort = { column: 'id', direction: 'asc' };

document.addEventListener('DOMContentLoaded', () => {
    loadUsers();
    updateSortIndicators();
});

let searchTimeout;
function handleSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        currentPage = 1;
        loadUsers();
    }, 500);
}

async function loadUsers() {
    const search = document.getElementById('search').value;
    const isAdmin = document.getElementById('filter-admin').value;
    const emailVerified = document.getElementById('filter-verified').value;

    currentFilters = {
        page: currentPage,
        per_page: 25,
        sort_by: currentSort.column,
        sort_order: currentSort.direction,
        ...(search && { search }),
        ...(isAdmin !== '' && { is_admin: isAdmin }),
        ...(emailVerified !== '' && { email_verified: emailVerified })
    };

    try {
        const response = await adminAPI.getUsers(currentFilters);
        const { users, pagination } = response.data;

        renderUsersTable(users);
        renderPagination(pagination);
    } catch (error) {
        console.error('Failed to load users:', error);
        document.getElementById('users-table-body').innerHTML = 
            '<tr><td colspan="7" class="error text-center">Failed to load users</td></tr>';
    }
}

function renderUsersTable(users) {
    const tbody = document.getElementById('users-table-body');

    if (!users || users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No users found</td></tr>';
        return;
    }

    tbody.innerHTML = users.map(user => `
        <tr>
            <td>${user.id}</td>
            <td>${user.name}</td>
            <td>${user.email}</td>
            <td>${user.is_admin ? '✓' : '✗'}</td>
            <td>${user.email_verified ? '✓' : '✗'}</td>
            <td>${new Date(user.created_at).toLocaleDateString()}</td>
            <td>
                <button class="btn btn-primary" onclick="openDetailModal(${user.id})">View</button>
                <button class="btn btn-primary" onclick="openEditModal(${user.id})">Edit</button>
                <button class="btn btn-danger" onclick="openDeleteModal(${user.id})">Delete</button>
            </td>
        </tr>
    `).join('');
}

function renderPagination(pagination) {
    const container = document.getElementById('pagination');
    const { current_page, total_pages } = pagination;

    if (total_pages <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = '';
    
    // Previous button
    html += `<button ${current_page === 1 ? 'disabled' : ''} onclick="changePage(${current_page - 1})">Previous</button>`;
    
    // Page numbers
    for (let i = 1; i <= total_pages; i++) {
        if (i === 1 || i === total_pages || (i >= current_page - 2 && i <= current_page + 2)) {
            html += `<button class="${i === current_page ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
        } else if (i === current_page - 3 || i === current_page + 3) {
            html += '<span>...</span>';
        }
    }
    
    // Next button
    html += `<button ${current_page === total_pages ? 'disabled' : ''} onclick="changePage(${current_page + 1})">Next</button>`;
    
    container.innerHTML = html;
}

function changePage(page) {
    currentPage = page;
    loadUsers();
}

async function openDetailModal(userId) {
    try {
        const response = await adminAPI.getUser(userId);
        const user = response.data;

        const content = `
            <div class="user-detail">
                <div class="detail-row">
                    <strong>ID:</strong> ${user.id}
                </div>
                <div class="detail-row">
                    <strong>Name:</strong> ${user.name}
                </div>
                <div class="detail-row">
                    <strong>Email:</strong> ${user.email}
                </div>
                <div class="detail-row">
                    <strong>Admin:</strong> ${user.is_admin ? 'Yes' : 'No'}
                </div>
                <div class="detail-row">
                    <strong>Email Verified:</strong> ${user.email_verified ? 'Yes' : 'No'}
                </div>
                <div class="detail-row">
                    <strong>Created:</strong> ${new Date(user.created_at).toLocaleString()}
                </div>
                <div class="detail-row">
                    <strong>Last Updated:</strong> ${new Date(user.updated_at).toLocaleString()}
                </div>
                ${user.clipboards_count !== undefined ? `
                <div class="detail-row">
                    <strong>Clipboards Owned:</strong> ${user.clipboards_count}
                </div>
                ` : ''}
                ${user.items_count !== undefined ? `
                <div class="detail-row">
                    <strong>Items Submitted:</strong> ${user.items_count}
                </div>
                ` : ''}
            </div>
            <div class="mt-20">
                <button class="btn btn-primary" onclick="closeDetailModal(); openEditModal(${user.id})">Edit User</button>
                <button class="btn btn-success" onclick="closeDetailModal(); openPasswordResetModal(${user.id})">Reset Password</button>
            </div>
        `;

        document.getElementById('user-detail-content').innerHTML = content;
        document.getElementById('detail-modal').classList.add('active');
    } catch (error) {
        alert('Failed to load user details');
    }
}

function closeDetailModal() {
    document.getElementById('detail-modal').classList.remove('active');
}

function sortBy(column) {
    if (currentSort.column === column) {
        // Toggle direction if same column
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        // New column, default to ascending
        currentSort.column = column;
        currentSort.direction = 'asc';
    }
    
    currentPage = 1; // Reset to first page when sorting
    updateSortIndicators();
    loadUsers();
}

function updateSortIndicators() {
    // Clear all indicators
    document.querySelectorAll('.sort-indicator').forEach(el => {
        el.textContent = '';
    });
    
    // Set current sort indicator
    const indicator = document.getElementById(`sort-${currentSort.column}`);
    if (indicator) {
        indicator.textContent = currentSort.direction === 'asc' ? '▲' : '▼';
    }
}

async function openEditModal(userId) {
    try {
        const response = await adminAPI.getUser(userId);
        const user = response.data;

        document.getElementById('edit-user-id').value = user.id;
        document.getElementById('edit-name').value = user.name;
        document.getElementById('edit-email').value = user.email;
        document.getElementById('edit-admin').checked = user.is_admin;
        document.getElementById('edit-verified').checked = user.email_verified;

        document.getElementById('edit-modal').classList.add('active');
    } catch (error) {
        alert('Failed to load user details');
    }
}

function closeEditModal() {
    document.getElementById('edit-modal').classList.remove('active');
}

async function handleEditSubmit(event) {
    event.preventDefault();

    const userId = document.getElementById('edit-user-id').value;
    const data = {
        name: document.getElementById('edit-name').value,
        email: document.getElementById('edit-email').value,
        is_admin: document.getElementById('edit-admin').checked ? 1 : 0,
        email_verified: document.getElementById('edit-verified').checked ? 1 : 0
    };

    try {
        await adminAPI.updateUser(userId, data);
        closeEditModal();
        loadUsers();
        alert('User updated successfully');
    } catch (error) {
        alert('Failed to update user: ' + error.message);
    }
}

function openDeleteModal(userId) {
    deleteUserId = userId;
    document.getElementById('delete-modal').classList.add('active');
}

function closeDeleteModal() {
    deleteUserId = null;
    document.getElementById('delete-modal').classList.remove('active');
}

async function confirmDelete() {
    if (!deleteUserId) return;

    try {
        await adminAPI.deleteUser(deleteUserId);
        closeDeleteModal();
        loadUsers();
        alert('User deleted successfully');
    } catch (error) {
        alert('Failed to delete user: ' + error.message);
    }
}

function openPasswordResetModal(userId) {
    document.getElementById('reset-user-id').value = userId;
    document.getElementById('new-password').value = '';
    document.getElementById('confirm-password').value = '';
    document.getElementById('password-reset-modal').classList.add('active');
}

function closePasswordResetModal() {
    document.getElementById('password-reset-modal').classList.remove('active');
}

async function handlePasswordReset(event) {
    event.preventDefault();

    const userId = document.getElementById('reset-user-id').value;
    const newPassword = document.getElementById('new-password').value;
    const confirmPassword = document.getElementById('confirm-password').value;

    if (newPassword !== confirmPassword) {
        alert('Passwords do not match');
        return;
    }

    if (newPassword.length < 8) {
        alert('Password must be at least 8 characters');
        return;
    }

    try {
        await adminAPI.resetPassword(userId, newPassword);
        closePasswordResetModal();
        alert('Password reset successfully');
    } catch (error) {
        alert('Failed to reset password: ' + error.message);
    }
}
