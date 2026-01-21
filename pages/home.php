<?php
require_once 'src/Services/SessionManager.php';

$currentUser = SessionManager::getCurrentUser();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= APP_NAME ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .header { background: #007cba; color: white; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { margin: 0; }
        .nav-links { display: flex; gap: 15px; }
        .nav-links a { color: white; text-decoration: none; padding: 8px 15px; border-radius: 4px; }
        .nav-links a:hover { background: rgba(255,255,255,0.2); }
        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .hero { text-align: center; padding: 60px 0; }
        .hero h2 { font-size: 2.5em; margin-bottom: 20px; color: #333; }
        .hero p { font-size: 1.2em; color: #666; margin-bottom: 30px; }
        .cta-buttons { display: flex; gap: 15px; justify-content: center; }
        .btn { background: #007cba; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; font-size: 16px; }
        .btn:hover { background: #005a87; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #545b62; }
        .features { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-top: 60px; }
        .feature { text-align: center; padding: 20px; }
        .feature h3 { color: #007cba; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= APP_NAME ?></h1>
        <div class="nav-links">
            <?php if ($currentUser): ?>
                <span>Welcome, <?= htmlspecialchars($currentUser['name']) ?></span>
                <a href="/dashboard">Dashboard</a>
                <a href="/logout">Logout</a>
            <?php else: ?>
                <a href="/login">Login</a>
                <a href="/register">Register</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="container">
        <div class="hero">
            <h2>Cross-Browser Clipboard System</h2>
            <p>Share links, code snippets, and files across different browsers and devices with real-time notifications and secure access control.</p>
            
            <div class="cta-buttons">
                <?php if ($currentUser): ?>
                    <a href="/dashboard" class="btn">Go to Dashboard</a>
                    <a href="/create-clipboard" class="btn btn-secondary">Create Clipboard</a>
                <?php else: ?>
                    <a href="/register" class="btn">Get Started</a>
                    <a href="/login" class="btn btn-secondary">Login</a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="features">
            <div class="feature">
                <h3>🔗 Share Any Content</h3>
                <p>Share links, code snippets, text, images, and files with customizable content type restrictions.</p>
            </div>
            
            <div class="feature">
                <h3>🔔 Real-time Notifications</h3>
                <p>Get instant email notifications and live updates when new content is shared to your clipboards.</p>
            </div>
            
            <div class="feature">
                <h3>🔒 Secure Access Control</h3>
                <p>Control who can access your clipboards with public/private settings and user-specific permissions.</p>
            </div>
            
            <div class="feature">
                <h3>⏰ Content Expiration</h3>
                <p>Set automatic expiration times for sensitive content with single-use access options.</p>
            </div>
            
            <div class="feature">
                <h3>📊 Usage Analytics</h3>
                <p>Track clipboard usage with detailed statistics and organize clipboards into hierarchical groups.</p>
            </div>
            
            <div class="feature">
                <h3>🔌 API Integration</h3>
                <p>Integrate with external systems using REST API endpoints and webhook notifications.</p>
            </div>
        </div>
    </div>
</body>
</html>