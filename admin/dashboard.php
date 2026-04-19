<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

requireAdmin();

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $del_id = (int) $_POST['delete_id'];
    if ($del_id !== (int)$_SESSION['user_id']) {
        $db->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'")->execute([$del_id]);
        setFlash('success', 'User removed successfully.');
    } else {
        setFlash('error', 'You cannot delete your own account.');
    }
    header('Location: ' . BASE_PATH . '/admin/dashboard.php');
    exit;
}

$total       = (int) $db->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalUsers  = (int) $db->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$totalAdmins = (int) $db->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();
$users       = $db->query('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC')->fetchAll();
$flash       = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard — HashGuard</title>
<link rel="stylesheet" href="/hashguard/assets/css/dashboard.css">
</head>
<body class="admin-body">

<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <span class="brand-icon">🔐</span>
    <span>HashGuard</span>
  </div>
  <nav class="sidebar-nav">
    <a href="/hashguard/admin/dashboard.php" class="nav-item active">
      <span class="nav-icon">📊</span> Dashboard
    </a>
    <a href="/hashguard/user/dashboard.php" class="nav-item">
      <span class="nav-icon">📖</span> Security Guide
    </a>
    <a href="/hashguard/auth/logout.php" class="nav-item nav-logout">
      <span class="nav-icon">🚪</span> Logout
    </a>
  </nav>
  <div class="sidebar-user">
    <div class="su-avatar"><?= strtoupper(substr($_SESSION['name'], 0, 1)) ?></div>
    <div>
      <div class="su-name"><?= htmlspecialchars($_SESSION['name']) ?></div>
      <div class="su-role">Administrator</div>
    </div>
  </div>
</aside>

<main class="main-content">
  <header class="page-header">
    <div>
      <h1>Admin Dashboard</h1>
      <p class="page-sub">Manage users and monitor the system.</p>
    </div>
    <button class="hamburger" id="hamburger">☰</button>
  </header>

  <?php if ($flash): ?>
    <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
      <?= htmlspecialchars($flash['message']) ?>
    </div>
  <?php endif; ?>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon">👥</div>
      <div class="stat-info">
        <span class="stat-num"><?= $total ?></span>
        <span class="stat-label">Total Accounts</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">🙋</div>
      <div class="stat-info">
        <span class="stat-num"><?= $totalUsers ?></span>
        <span class="stat-label">Registered Users</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">🛡️</div>
      <div class="stat-info">
        <span class="stat-num"><?= $totalAdmins ?></span>
        <span class="stat-label">Administrators</span>
      </div>
    </div>
  </div>

  <section class="card table-card">
    <div class="card-header">
      <h2>All Users</h2>
      <span class="badge"><?= $total ?> accounts</span>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Registered</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
          <tr>
            <td><?= $u['id'] ?></td>
            <td>
              <div class="user-cell">
                <span class="mini-avatar"><?= strtoupper(substr($u['name'],0,1)) ?></span>
                <?= htmlspecialchars($u['name']) ?>
              </div>
            </td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><span class="role-badge role-<?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span></td>
            <td><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
            <td>
              <?php if ($u['id'] != $_SESSION['user_id'] && $u['role'] !== 'admin'): ?>
              <form method="POST" style="display:inline"
                    onsubmit="return confirm('Delete this user?')">
                <input type="hidden" name="delete_id" value="<?= $u['id'] ?>">
                <button type="submit" class="btn-delete">Delete</button>
              </form>
              <?php else: ?>
              <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>

<script src="/hashguard/assets/js/dashboard.js"></script>
</body>
</html>
