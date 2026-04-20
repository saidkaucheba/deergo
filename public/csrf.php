<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function csrf_field(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $token = htmlspecialchars($_SESSION['csrf_token']);
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

function csrf_verify(): void {
    $token      = $_POST['csrf_token'] ?? '';
    $sessionTok = $_SESSION['csrf_token'] ?? '';
    if (!$token || !$sessionTok || !hash_equals($sessionTok, $token)) {
        http_response_code(403);
        die('Ошибка безопасности: неверный CSRF-токен. <a href="javascript:history.back()">Назад</a>');
    }
}
?>