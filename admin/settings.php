<?php 
require_once __DIR__ . '/config.php';
require_once __DIR__ . "/auth_check.php"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include __DIR__ . '/nav.php'; ?>

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

    <script>const BASE_PATH = '<?= BASE_PATH ?>';</script>
    <script src="js/admin-api.js"></script>
    <script src="js/admin-settings.js"></script>
</body>
</html>
