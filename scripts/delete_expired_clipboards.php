<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/Core/Repository/ClipboardRepository.php';

$repo = new ClipboardRepository();
$deleted = $repo->deleteExpired();

echo "[OK] Deleted expired clipboards: {$deleted}" . PHP_EOL;
