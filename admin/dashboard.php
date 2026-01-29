<?php 
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/base_path.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    <link rel="stylesheet" href="<?php echo url('/admin/css/admin.css'); ?>">
</head>
<body>
    <div class="admin-container">
        <nav class="admin-sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="<?php echo url('/admin/dashboard.php'); ?>" class="active">Dashboard</a></li>
                <li><a href="<?php echo url('/admin/users.php'); ?>">Users</a></li>
                <li><a href="<?php echo url('/admin/clipboards.php'); ?>">Clipboards</a></li>
                <li><a href="<?php echo url('/admin/content.php'); ?>">Content</a></li>
                <li><a href="<?php echo url('/admin/activity.php'); ?>">Activity Logs</a></li>
                <li><a href="<?php echo url('/public/dashboard.html'); ?>">Back to App</a></li>
            </ul>
        </nav>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Dashboard</h1>
                <div class="user-info">
                    <span id="admin-name">Admin</span>
                    <button onclick="logout()">Logout</button>
                </div>
            </header>

            <div class="admin-content">
                <!-- Statistics Cards -->
                <div id="stats-container" class="stats-grid">
                    <div class="stat-card">
                        <h3>Loading...</h3>
                        <div class="stat-value">-</div>
                    </div>
                </div>

                <!-- Recent Activity Feed -->
                <div class="recent-activity">
                    <h2>Recent Activity</h2>
                    <div id="activity-feed" class="activity-list">
                        <p>Loading activity...</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Set base path for JavaScript
        window.BASE_PATH = '<?php echo BASE_PATH; ?>';
    </script>
    <script src="<?php echo url('/admin/js/admin-api.js'); ?>"></script>
    <script src="<?php echo url('/admin/js/admin-dashboard.js'); ?>"></script>
</body>
</html>
