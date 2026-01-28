<?php require_once __DIR__ . "/auth_check.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs - Admin Panel</title>
    <link rel="stylesheet" href="/admin/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <nav class="admin-sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="/admin/dashboard.php">Dashboard</a></li>
                <li><a href="/admin/users.php">Users</a></li>
                <li><a href="/admin/clipboards.php">Clipboards</a></li>
                <li><a href="/admin/content.php">Content</a></li>
                <li><a href="/admin/activity.php" class="active">Activity Logs</a></li>
                <li><a href="/public/dashboard.html">Back to App</a></li>
            </ul>
        </nav>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Activity Logs</h1>
                <div class="user-info">
                    <span id="admin-name">Admin</span>
                    <button onclick="logout()">Logout</button>
                </div>
            </header>

            <div class="admin-content">
                <div class="filters-section">
                    <input type="date" id="filter-date-from" placeholder="From Date">
                    <input type="date" id="filter-date-to" placeholder="To Date">
                    <select id="filter-action-type">
                        <option value="">All Actions</option>
                        <option value="create">Create</option>
                        <option value="view">View</option>
                        <option value="download">Download</option>
                        <option value="delete">Delete</option>
                        <option value="share">Share</option>
                    </select>
                    <button onclick="activityManager.loadActivity()" class="btn-primary">Apply Filters</button>
                    <button onclick="activityManager.exportActivity()" class="btn-secondary">Export</button>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Clipboard</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="activity-table-body">
                            <tr><td colspan="6" class="loading">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div id="detail-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="activityManager.closeDetailModal()">&times;</span>
            <h2>Activity Details</h2>
            <div id="detail-content"></div>
        </div>
    </div>

    <script src="/admin/js/admin-api.js"></script>
    <script src="/admin/js/admin-activity.js"></script>
</body>
</html>
