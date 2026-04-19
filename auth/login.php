<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_PATH . '/' . currentRole() . '/dashboard.php');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please fill in both fields.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role'];

            setFlash('success', 'Welcome back, ' . $user['name'] . '!');

            if ($user['role'] === 'admin') {
                header('Location: ' . BASE_PATH . '/admin/dashboard.php');
            } else {
                header('Location: ' . BASE_PATH . '/user/dashboard.php');
            }
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — HashGuard</title>
<link rel="stylesheet" href="/hashguard/assets/css/auth.css">
</head>
<body>
<div class="auth-wrapper">
  <div class="auth-brand">
    <span class="brand-icon">🔐</span>
    <span class="brand-name">HashGuard</span>
  </div>

  <div class="auth-card">
    <h1>Sign In</h1>
    <p class="auth-subtitle">Access your HashGuard account.</p>

    <?php if ($flash): ?>
      <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
        <?= htmlspecialchars($flash['message']) ?>
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="" novalidate>
      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email"
               value="<?= htmlspecialchars($email) ?>"
               placeholder="juan@example.com" required autofocus>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-wrap">
          <input type="password" id="password" name="password" placeholder="••••••••" required>
          <button type="button" class="toggle-pw" data-target="password">👁</button>
        </div>
      </div>

      <button type="submit" class="btn-primary">Sign In</button>
    </form>

    <p class="auth-switch">No account yet? <a href="/hashguard/auth/register.php">Create one</a></p>

    <div class="demo-box">
      <p class="demo-title">Demo credentials</p>
      <p><strong>Admin:</strong> admin@hashguard.com / <em>Admin@1234</em></p>
      <p><strong>User:</strong> register a new account</p>
    </div>
  </div>
</div>
<script src="/hashguard/assets/js/auth.js"></script>
</body>
</html>
