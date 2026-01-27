<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = $_POST['db_host'] ?? 'localhost';
    $db_name = $_POST['db_name'] ?? 'clipboard_system';
    $db_user = $_POST['db_user'] ?? 'root';
    $db_pass = $_POST['db_pass'] ?? '';

    try {
        $pdo = new PDO("mysql:host={$db_host}", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}`");
        $pdo->exec("USE `{$db_name}`");

        $sql = file_get_contents('config/database.sql');
        $sql = preg_replace('/CREATE DATABASE.*?;/i', '', $sql);
        $sql = preg_replace('/USE.*?;/i', '', $sql);
        
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }

        $config = file_get_contents('config/config.php');
        $config = str_replace("'localhost'", "'{$db_host}'", $config);
        $config = str_replace("'clipboard_system'", "'{$db_name}'", $config);
        $config = str_replace("'root'", "'{$db_user}'", $config);
        $config = str_replace("''", "'{$db_pass}'", $config);
        file_put_contents('config/config.php', $config);

        echo "<h2>Installation Complete!</h2>";
        echo "<p>Database created successfully.</p>";
        echo "<p><a href='/'>Go to Application</a></p>";
        exit;

    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Install Clipboard System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .container { max-width: 500px; margin: 0 auto; }
        input, button { padding: 8px; margin: 5px 0; width: 100%; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Install Clipboard System</h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <label>Database Host:</label>
            <input type="text" name="db_host" value="localhost" required>
            
            <label>Database Name:</label>
            <input type="text" name="db_name" value="clipboard_system" required>
            
            <label>Database User:</label>
            <input type="text" name="db_user" value="root" required>
            
            <label>Database Password:</label>
            <input type="password" name="db_pass">
            
            <button type="submit">Install</button>
        </form>
    </div>
</body>
</html>
