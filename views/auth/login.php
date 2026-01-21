<!DOCTYPE html>
<html>
<head>
    <title>Login - <?= APP_NAME ?></title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="email"], input[type="password"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #005a87; }
        .error { color: red; margin-bottom: 15px; }
        .links { margin-top: 20px; text-align: center; }
        .links a { color: #007cba; text-decoration: none; margin: 0 10px; }
    </style>
</head>
<body>
    <h2>Login to <?= APP_NAME ?></h2>
    
    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit">Login</button>
    </form>
    
    <div class="links">
        <a href="/register">Create Account</a>
        <a href="/forgot-password">Forgot Password?</a>
        <a href="/">Back to Home</a>
    </div>
</body>
</html>