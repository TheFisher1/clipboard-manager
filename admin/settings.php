<?php require_once __DIR__ . "/auth_check.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
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
                <li><a href="/admin/activity.php">Activity Logs</a></li>
                <li><a href="/admin/settings.php" class="active">Settings</a></li>
                <li><a href="/public/dashboard.html">Back to App</a></li>
            </ul>
        </nav>

        <main class="admin-main">
            <header class="admin-header">
                <h1>System Settings</h1>
                <div class="user-info">
                    <span id="admin-name">Admin</span>
                    <button onclick="logout()">Logout</button>
                </div>
            </header>

            <div class="admin-content">
                <div id="settings-container" class="settings-container">
                    <p class="loading">Loading settings...</p>
                </div>
            </div>
        </main>
    </div>

    <script src="/admin/js/admin-api.js"></script>
    <script src="/admin/js/admin-settings.js"></script>
</body>
</html>
