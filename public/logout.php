<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/auth.php';

if (is_logged_in()) {
    audit('LOGOUT', 'usuarios', (int) current_user()['id']);
}

logout_user();
header('Location: ' . app_url('index.php'));
exit;
