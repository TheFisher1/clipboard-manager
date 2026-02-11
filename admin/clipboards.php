<?php 
require_once __DIR__ . '/config.php';
require_once __DIR__ . "/auth_check.php"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clipboard Management - Admin Panel</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include __DIR__ . '/nav.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Clipboard Management</h1>
                <div class="user-info">
                    <span id="admin-name">Admin</span>
                    <button onclick="logout()">Logout</button>
                </div>
            </header>

            <div class="admin-content">
                <!-- Filters and Search -->
                <div class="filters-section">
                    <input type="text" id="search" placeholder="Search clipboards..." class="search-input">
                    <select id="filter-public" class="filter-select">
                        <option value="">All Types</option>
                        <option value="1">Public</option>
                        <option value="0">Private</option>
                    </select>
                    <select id="filter-owner" class="filter-select">
                        <option value="">All Owners</option>
                    </select>
                    <button onclick="clipboardManager.loadClipboards()" class="btn-primary">Apply Filters</button>
                </div>

                <!-- Clipboards Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th onclick="clipboardManager.sortBy('id')">ID</th>
                                <th onclick="clipboardManager.sortBy('name')">Name</th>
                                <th onclick="clipboardManager.sortBy('owner_name')">Owner</th>
                                <th>Type</th>
                                <th>Items</th>
                                <th>Subscribers</th>
                                <th onclick="clipboardManager.sortBy('created_at')">Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="clipboards-table-body">
                            <tr><td colspan="8" class="loading">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination" id="pagination"></div>
            </div>
        </main>
    </div>

    <!-- Clipboard Detail Modal -->
    <div id="detail-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="clipboardManager.closeDetailModal()">&times;</span>
            <h2>Clipboard Details</h2>
            <div id="detail-content"></div>
        </div>
    </div>

    <!-- Edit Clipboard Modal -->
    <div id="edit-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="clipboardManager.closeEditModal()">&times;</span>
            <h2>Edit Clipboard</h2>
            <form id="edit-form">
                <input type="hidden" id="edit-id">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" id="edit-name" required>
                </div>
                <div class="form-group">
                    <label>Description:</label>
                    <textarea id="edit-description"></textarea>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="edit-public">
                        Public Clipboard
                    </label>
                </div>
                <div class="form-group">
                    <label>Max Subscribers (0 = unlimited):</label>
                    <input type="number" id="edit-max-subscribers" min="0">
                </div>
                <div class="form-group">
                    <label>Max Items (0 = unlimited):</label>
                    <input type="number" id="edit-max-items" min="0">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Save Changes</button>
                    <button type="button" onclick="clipboardManager.closeEditModal()" class="btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Transfer Ownership Modal -->
    <div id="transfer-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="clipboardManager.closeTransferModal()">&times;</span>
            <h2>Transfer Ownership</h2>
            <form id="transfer-form">
                <input type="hidden" id="transfer-id">
                <div class="form-group">
                    <label>New Owner:</label>
                    <select id="transfer-owner" required></select>
                </div>
                <p class="warning">This action cannot be undone. The new owner will have full control over this clipboard.</p>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Transfer</button>
                    <button type="button" onclick="clipboardManager.closeTransferModal()" class="btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="clipboardManager.closeDeleteModal()">&times;</span>
            <h2>Confirm Delete</h2>
            <p>Are you sure you want to delete this clipboard? This will also delete all associated items and subscriptions.</p>
            <p class="warning">This action cannot be undone!</p>
            <div class="form-actions">
                <button onclick="clipboardManager.confirmDelete()" class="btn-danger">Delete</button>
                <button onclick="clipboardManager.closeDeleteModal()" class="btn-secondary">Cancel</button>
            </div>
        </div>
    </div>

    <script>const BASE_PATH = '<?= BASE_PATH ?>';</script>
    <script src="js/admin-api.js"></script>
    <script src="js/admin-clipboards.js"></script>
</body>
</html>
