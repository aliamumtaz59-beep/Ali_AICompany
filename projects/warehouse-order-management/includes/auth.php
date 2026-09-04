<?php
require_once __DIR__ . '/../config/database.php';

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): void
{
    if (!current_user()) {
        redirect('login.php');
    }
}

function require_admin(): void
{
    require_login();
    if (current_user()['role'] !== 'admin') {
        http_response_code(403);
        die('Access denied. Admin privileges required.');
    }
}

function attempt_login(string $username, string $password): bool
{
    $stmt = db()->prepare("SELECT * FROM users WHERE username = ? AND status = 'active' LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'username' => $user['username'],
            'role' => $user['role'],
        ];
        return true;
    }
    return false;
}

function logout(): void
{
    $_SESSION = [];
    session_destroy();
}
