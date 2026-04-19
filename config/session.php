<?php
// config/session.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_PATH', '/hashguard');

function requireLogin(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . BASE_PATH . '/auth/login.php');
        exit;
    }
}

function requireAdmin(): void {
    requireLogin();
    if ($_SESSION['role'] !== 'admin') {
        header('Location: ' . BASE_PATH . '/user/dashboard.php');
        exit;
    }
}

function requireUser(): void {
    requireLogin();
    if ($_SESSION['role'] !== 'user') {
        header('Location: ' . BASE_PATH . '/admin/dashboard.php');
        exit;
    }
}

function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

function currentRole(): string {
    return $_SESSION['role'] ?? '';
}

function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
