<?php
require_once __DIR__ . '/config/session.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_PATH . '/' . currentRole() . '/dashboard.php');
} else {
    header('Location: ' . BASE_PATH . '/auth/login.php');
}
exit;
