<?php
// config/db.php — Database connection
// Change these values to match your XAMPP setup.

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // default XAMPP MySQL user
define('DB_PASS', '');           // default XAMPP MySQL password (empty)
define('DB_NAME', 'hashguard_db');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // In production, log the error and show a generic message.
            die('<p style="font-family:sans-serif;color:#c00;padding:2rem">
                 Database connection failed. Please check <code>config/db.php</code>.<br>
                 Error: ' . htmlspecialchars($e->getMessage()) . '</p>');
        }
    }
    return $pdo;
}
