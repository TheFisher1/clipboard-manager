<?php 
require_once __DIR__ . '/config.php';
require_once __DIR__ . "/auth_check.php"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Panel</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include __DIR__ . '/nav.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>User Management</h1>
                <div class="user-info">
                    <span id="admin-name">Admin</span>
                    <button onclick="logout()">Logout</button>
                </div>
            </header>

            <div class="admin-content">
                <div class="filters">
                    <div class="filters-row">
                        <div class="filter-item">
                            <label for="search">Search</label>
                            <input type="text" id="search" placeholder="Search by name or email..." onkeyup="handleSearch()">
                        </div>
                        <div class="filter-item">
                            <label for="filter-admin">User Type</label>
                            <select id="filter-admin" onchange="loadUsers()">
                                <option value="">All Users</option>
                                <option value="1">Administrators</option>
                                <option value="0">Regular Users</option>
                            </select>
                        </div>
                        <div class="filter-item">
                            <label for="filter-verified">Email Status</label>
                            <select id="filter-verified" onchange="loadUsers()">
                                <option value="">All Statuses</option>
                                <option value="1">Verified ✓</option>
                                <option value="0">Not Verified</option>
                            </select>
                        </div>
                        <div class="filter-item">
                            <label>&nbsp;</label>
                            <button class="btn btn-secondary" onclick="clearFilters()">Clear Filters</button>
                        </div>
                    </div>
                </div>

                <div class="data-table">
                    <table>
                        <thead>
                            <tr>
                                <th class="sortable" onclick="sortBy('id')">ID <span class="sort-indicator" id="sort-id"></span></th>
                                <th class="sortable" onclick="sortBy('name')">Name <span class="sort-indicator" id="sort-name"></span></th>
                                <th class="sortable" onclick="sortBy('email')">Email <span class="sort-indicator" id="sort-email"></span></th>
                                <th class="sortable" onclick="sortBy('is_admin')">Admin <span class="sort-indicator" id="sort-is_admin"></span></th>
                                <th class="sortable" onclick="sortBy('email_verified')">Verified <span class="sort-indicator" id="sort-email_verified"></span></th>
                                <th class="sortable" onclick="sortBy('created_at')">Created <span class="sort-indicator" id="sort-created_at"></span></th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="users-table-body">
                            <tr>
                                <td colspan="7" class="text-center">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="pagination" id="pagination"></div>
            </div>
        </main>
    </div>

    <!-- User Detail Modal -->
    <div id="detail-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>User Details</h2>
                <button class="modal-close" onclick="closeDetailModal()">&times;</button>
            </div>
            <div id="user-detail-content">
                <p>Loading...</p>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="edit-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit User</h2>
                <button class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <form id="edit-form" onsubmit="handleEditSubmit(event)">
                <input type="hidden" id="edit-user-id">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" id="edit-name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="edit-email" required>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="edit-admin"> Admin
                    </label>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="edit-verified"> Email Verified
                    </label>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Confirm Delete</h2>
                <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
            </div>
            <p>Are you sure you want to delete this user? This action cannot be undone.</p>
            <div style="margin-top: 20px; display: flex; gap: 10px;">
                <button class="btn btn-danger" onclick="confirmDelete()">Delete</button>
                <button class="btn" onclick="closeDeleteModal()">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Password Reset Modal -->
    <div id="password-reset-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Reset Password</h2>
                <button class="modal-close" onclick="closePasswordResetModal()">&times;</button>
            </div>
            <form id="password-reset-form" onsubmit="handlePasswordReset(event)">
                <input type="hidden" id="reset-user-id">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" id="new-password" required minlength="8">
                    <small>Minimum 8 characters</small>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" id="confirm-password" required minlength="8">
                </div>
                <button type="submit" class="btn btn-success">Reset Password</button>
            </form>
        </div>
    </div>

    <script>const BASE_PATH = '<?= BASE_PATH ?>';</script>
    <script src="js/admin-api.js"></script>
    <script src="js/admin-users.js"></script>
</body>
</html>
