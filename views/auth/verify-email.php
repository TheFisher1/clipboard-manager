<!DOCTYPE html>
<html>
<head>
    <title>Email Verification - <?= APP_NAME ?></title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; text-align: center; }
        .error { color: red; margin-bottom: 15px; }
        .success { color: green; margin-bottom: 15px; }
        .links { margin-top: 20px; }
        .links a { color: #007cba; text-decoration: none; margin: 0 10px; }
    </style>
</head>
<body>
    <h2>Email Verification</h2>
    
    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <div class="links">
        <a href="/login">Go to Login</a>
        <a href="/">Back to Home</a>
    </div>
</body>
</html>