<?php

$pdo = new PDO('mysql:host=db;dbname=db', 'db', 'db');
$hash = password_hash('admin', PASSWORD_ARGON2I, ['memory_cost' => 65536, 'time_cost' => 16, 'threads' => 1]);
$pdo->prepare('UPDATE be_users SET password = ? WHERE username = ?')->execute([$hash, 'admin']);
echo 'Admin password reset to "admin".' . PHP_EOL;
