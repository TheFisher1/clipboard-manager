<?php
// Simple dashboard - just show basic info for now
$db = getDB();
$clipboards = $db->query("SELECT * FROM clipboards ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - <?= APP_NAME ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .container { max-width: 800px; margin: 0 auto; }
        .clipboard { border: 1px solid #ddd; padding: 15px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Dashboard</h1>
        
        <h2>Clipboards</h2>
        <?php if (empty($clipboards)): ?>
            <p>No clipboards yet.</p>
        <?php else: ?>
            <?php foreach ($clipboards as $clipboard): ?>
                <div class="clipboard">
                    <h3><?= htmlspecialchars($clipboard['name']) ?></h3>
                    <p>Created: <?= $clipboard['created_at'] ?></p>
                    <p>Public: <?= $clipboard['is_public'] ? 'Yes' : 'No' ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>