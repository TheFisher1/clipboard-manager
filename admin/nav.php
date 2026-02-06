<?php
// This file should be included after config.php
if (!defined('BASE_PATH')) {
    require_once __DIR__ . '/config.php';
}
?>
<nav class="admin-sidebar">
    <div class="sidebar-header">
        <h2>Admin Panel</h2>
    </div>
    <ul class="sidebar-menu">
        <li><a href="<?= BASE_PATH ?>/admin/dashboard.php" <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class="active"' : '' ?>>Dashboard</a></li>
        <li><a href="<?= BASE_PATH ?>/admin/users.php" <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'class="active"' : '' ?>>Users</a></li>
        <li><a href="<?= BASE_PATH ?>/admin/clipboards.php" <?= basename($_SERVER['PHP_SELF']) == 'clipboards.php' ? 'class="active"' : '' ?>>Clipboards</a></li>
        <li><a href="<?= BASE_PATH ?>/admin/content.php" <?= basename($_SERVER['PHP_SELF']) == 'content.php' ? 'class="active"' : '' ?>>Content</a></li>
        <li><a href="<?= BASE_PATH ?>/admin/activity.php" <?= basename($_SERVER['PHP_SELF']) == 'activity.php' ? 'class="active"' : '' ?>>Activity Logs</a></li>
        <li><a href="<?= BASE_PATH ?>/public/dashboard.html">Back to App</a></li>
    </ul>
</nav>
