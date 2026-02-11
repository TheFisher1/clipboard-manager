<nav class="admin-sidebar">
    <div class="sidebar-header">
        <h2>Admin Panel</h2>
    </div>
    <ul class="sidebar-menu">
        <li><a href="dashboard.php" <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class="active"' : '' ?>>Dashboard</a></li>
        <li><a href="users.php" <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'class="active"' : '' ?>>Users</a></li>
        <li><a href="clipboards.php" <?= basename($_SERVER['PHP_SELF']) == 'clipboards.php' ? 'class="active"' : '' ?>>Clipboards</a></li>
        <li><a href="content.php" <?= basename($_SERVER['PHP_SELF']) == 'content.php' ? 'class="active"' : '' ?>>Content</a></li>
        <li><a href="activity.php" <?= basename($_SERVER['PHP_SELF']) == 'activity.php' ? 'class="active"' : '' ?>>Activity Logs</a></li>
        <li><a href="settings.php" <?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'class="active"' : '' ?>>Settings</a></li>
        <li><a href="../public/dashboard.html">Back to App</a></li>
    </ul>
</nav>
