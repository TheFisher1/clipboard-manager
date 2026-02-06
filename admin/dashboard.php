<?php 
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_check.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/admin/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include __DIR__ . '/nav.php'; ?>

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

    <script>const BASE_PATH = '<?= BASE_PATH ?>';</script>
    <script src="<?= BASE_PATH ?>/admin/js/admin-api.js"></script>
    <script src="<?= BASE_PATH ?>/admin/js/admin-dashboard.js"></script>
</body>
</html>
