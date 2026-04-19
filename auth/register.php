<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_PATH . '/' . currentRole() . '/dashboard.php');
    exit;
}

$errors = [];
$old    = ['name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']    ?? '');
    $email    = trim($_POST['email']   ?? '');
    $password = $_POST['password']     ?? '';
    $confirm  = $_POST['confirm']      ?? '';

    $old = ['name' => $name, 'email' => $email];

    if ($name === '') {
        $errors[] = 'Full name is required.';
    } elseif (strlen($name) > 100) {
        $errors[] = 'Name must be 100 characters or fewer.';
    }

    if ($email === '') {
        $errors[] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $db = getDB();
        $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'That email address is already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $ins  = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            $ins->execute([$name, $email, $hash]);

            setFlash('success', 'Account created! You can now log in.');
            header('Location: ' . BASE_PATH . '/auth/login.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register — HashGuard</title>
<link rel="stylesheet" href="/hashguard/assets/css/auth.css">
</head>
<body>
<div class="auth-wrapper">
  <div class="auth-brand">
    <span class="brand-icon">🔐</span>
    <span class="brand-name">HashGuard</span>
  </div>

  <div class="auth-card">
    <h1>Create Account</h1>
    <p class="auth-subtitle">Join HashGuard to explore password security.</p>

    <?php if ($errors): ?>
      <div class="alert alert-error">
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST" action="" novalidate>
      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name"
               value="<?= htmlspecialchars($old['name']) ?>"
               placeholder="Juan Dela Cruz" required>
      </div>

      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email"
               value="<?= htmlspecialchars($old['email']) ?>"
               placeholder="juan@example.com" required>
      </div>

      <div class="form-group">
        <label for="password">Password <span class="hint">(min. 8 characters)</span></label>
        <div class="input-wrap">
          <input type="password" id="password" name="password" placeholder="••••••••" required>
          <button type="button" class="toggle-pw" data-target="password">👁</button>
        </div>
      </div>

      <div class="form-group">
        <label for="confirm">Confirm Password</label>
        <div class="input-wrap">
          <input type="password" id="confirm" name="confirm" placeholder="••••••••" required>
          <button type="button" class="toggle-pw" data-target="confirm">👁</button>
        </div>
      </div>

      <button type="submit" class="btn-primary">Create Account</button>
    </form>

    <p class="auth-switch">Already have an account? <a href="/hashguard/auth/login.php">Sign in</a></p>
  </div>
</div>
<script src="/hashguard/assets/js/auth.js"></script>
</body>
</html>
