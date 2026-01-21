<?php
require_once 'src/Services/SessionManager.php';

$currentUser = SessionManager::getCurrentUser();
if (!$currentUser) {
    header('Location: /login');
    exit;
}

$db = getDB();
$stmt = $db->prepare("SELECT * FROM clipboards WHERE owner_id = ? ORDER BY created_at DESC");
$stmt->execute([$currentUser['id']]);
$clipboards = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - <?= APP_NAME ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .header { background: #007cba; color: white; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { margin: 0; }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .clipboard { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .clipboard h3 { margin-top: 0; color: #007cba; }
        .btn { background: #007cba; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; display: inline-block; }
        .btn:hover { background: #005a87; }
        .logout-btn { background: #dc3545; }
        .logout-btn:hover { background: #c82333; }
        .stats { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= APP_NAME ?> - Dashboard</h1>
        <div class="user-info">
            <span>Welcome, <?= htmlspecialchars($currentUser['name']) ?></span>
            <?php if ($currentUser['is_admin']): ?>
                <span style="background: #28a745; padding: 4px 8px; border-radius: 3px; font-size: 12px;">ADMIN</span>
            <?php endif; ?>
            <a href="/logout" class="btn logout-btn">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="stats">
            <h3>Account Information</h3>
            <p><strong>Email:</strong> <?= htmlspecialchars($currentUser['email']) ?></p>
            <p><strong>Account Type:</strong> <?= $currentUser['is_admin'] ? 'Administrator' : 'User' ?></p>
            <p><strong>Total Clipboards:</strong> <?= count($clipboards) ?></p>
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>My Clipboards</h2>
            <a href="/create-clipboard" class="btn">Create New Clipboard</a>
        </div>
        
        <?php if (empty($clipboards)): ?>
            <div class="clipboard">
                <p>No clipboards yet. <a href="/create-clipboard">Create your first clipboard</a> to get started!</p>
            </div>
        <?php else: ?>
            <?php foreach ($clipboards as $clipboard): ?>
                <div class="clipboard">
                    <h3><?= htmlspecialchars($clipboard['name']) ?></h3>
                    <p><strong>Created:</strong> <?= date('M j, Y g:i A', strtotime($clipboard['created_at'])) ?></p>
                    <p><strong>Visibility:</strong> <?= $clipboard['is_public'] ? 'Public' : 'Private' ?></p>
                    <div style="margin-top: 10px;">
                        <a href="/clipboard/<?= $clipboard['id'] ?>" class="btn">View</a>
                        <a href="/clipboard/<?= $clipboard['id'] ?>/edit" class="btn">Edit</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>