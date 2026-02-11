<?php 
require_once __DIR__ . '/config.php';
require_once __DIR__ . "/auth_check.php"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Moderation - Admin Panel</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include __DIR__ . '/nav.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Content Moderation</h1>
                <div class="user-info">
                    <span id="admin-name">Admin</span>
                    <button onclick="logout()">Logout</button>
                </div>
            </header>

            <div class="admin-content">
                <div class="filters-section">
                    <select id="filter-content-type">
                        <option value="">All Types</option>
                        <option value="text/plain">Text</option>
                        <option value="text/html">HTML</option>
                        <option value="image/jpeg">JPEG</option>
                        <option value="image/png">PNG</option>
                    </select>
                    <select id="filter-clipboard">
                        <option value="">All Clipboards</option>
                    </select>
                    <input type="date" id="filter-date-from" placeholder="From Date">
                    <input type="date" id="filter-date-to" placeholder="To Date">
                    <button onclick="contentManager.loadContent()" class="btn-primary">Apply Filters</button>
                    <button onclick="contentManager.bulkDelete()" class="btn-danger">Delete Selected</button>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all" onclick="contentManager.toggleSelectAll()"></th>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Clipboard</th>
                                <th>Submitted By</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="content-table-body">
                            <tr><td colspan="7" class="loading">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div id="detail-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="contentManager.closeDetailModal()">&times;</span>
            <h2>Content Details</h2>
            <div id="detail-content"></div>
        </div>
    </div>

    <div id="delete-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="contentManager.closeDeleteModal()">&times;</span>
            <h2>Delete Content</h2>
            <form id="delete-form">
                <div class="form-group">
                    <label>Reason for deletion:</label>
                    <textarea id="delete-reason" required></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-danger">Delete</button>
                    <button type="button" onclick="contentManager.closeDeleteModal()" class="btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>const BASE_PATH = '<?= BASE_PATH ?>';</script>
    <script src="js/admin-api.js"></script>
    <script src="js/admin-content.js"></script>
</body>
</html>
