<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

requireLogin();

$isAdmin = (currentRole() === 'admin');
$flash   = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Security Guide — HashGuard</title>
<link rel="stylesheet" href="/hashguard/assets/css/dashboard.css">
<link rel="stylesheet" href="/hashguard/assets/css/guide.css">
</head>
<body class="user-body">

<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <span class="brand-icon">🔐</span>
    <span>HashGuard</span>
  </div>
  <nav class="sidebar-nav">
    <?php if ($isAdmin): ?>
    <a href="/hashguard/admin/dashboard.php" class="nav-item">
      <span class="nav-icon">📊</span> Admin Dashboard
    </a>
    <?php endif; ?>
    <a href="/hashguard/user/dashboard.php" class="nav-item active">
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
      <div class="su-role"><?= ucfirst(currentRole()) ?></div>
    </div>
  </div>
</aside>

<main class="main-content">
  <header class="page-header">
    <div>
      <h1>Password Security Guide</h1>
      <p class="page-sub">Everything you need to know about hashing and secure storage.</p>
    </div>
    <button class="hamburger" id="hamburger">☰</button>
  </header>

  <?php if ($flash): ?>
    <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
      <?= htmlspecialchars($flash['message']) ?>
    </div>
  <?php endif; ?>

  <div class="guide-hero">
    <div class="guide-hero-text">
      <h2>Why Does Password Storage Matter?</h2>
      <p>Every day, databases are breached. How passwords are stored determines whether those breaches become catastrophic. Hashing is the first line of defence.</p>
    </div>
    <div class="guide-hero-icon">🛡️</div>
  </div>

  <section class="guide-section">
    <div class="section-label">Section 01</div>
    <h2 class="section-title">What Is Hashing?</h2>
    <div class="guide-cards">
      <div class="guide-card">
        <div class="gc-icon">🔄</div>
        <h3>One-Way Function</h3>
        <p>A hash function converts any input into a fixed-length string. You <strong>cannot reverse</strong> a hash back to the original text.</p>
      </div>
      <div class="guide-card">
        <div class="gc-icon">📏</div>
        <h3>Fixed-Length Output</h3>
        <p>Whether you hash one character or a thousand, the output is always the same length. bcrypt always produces 60 characters.</p>
      </div>
      <div class="guide-card">
        <div class="gc-icon">🧪</div>
        <h3>Deterministic</h3>
        <p>The same input always produces the same hash. This allows verification without storing the original password.</p>
      </div>
    </div>
    <div class="code-block">
      <div class="code-label">Concept (pseudo-code)</div>
      <pre>hash("hello")  → "2cf24dba5fb0a30e..." (always the same)
hash("Hello")  → "185f8db32921bd46..." (completely different!)

// You CANNOT reverse this:
"2cf24dba5fb0a30e..." → ??? (impossible)</pre>
    </div>
  </section>

  <section class="guide-section">
    <div class="section-label">Section 02</div>
    <h2 class="section-title">Password Hashing in PHP</h2>
    <p class="section-intro">PHP provides two built-in functions that make password hashing safe and simple.</p>
    <div class="guide-cards two-col">
      <div class="guide-card highlight-card">
        <div class="gc-icon">🔒</div>
        <h3>password_hash()</h3>
        <p>Generates a secure bcrypt hash with an automatic random salt and configurable work factor.</p>
        <div class="mini-code"><pre>$hash = password_hash(
    $password,
    PASSWORD_BCRYPT,
    ['cost' => 12]
);</pre></div>
      </div>
      <div class="guide-card highlight-card">
        <div class="gc-icon">✅</div>
        <h3>password_verify()</h3>
        <p>Safely checks a plain password against a stored hash. Resistant to timing attacks.</p>
        <div class="mini-code"><pre>if (password_verify($input, $hash)) {
    // Correct password
} else {
    // Wrong password
}</pre></div>
      </div>
    </div>
    <div class="info-box">
      <span class="ib-icon">💡</span>
      <div><strong>Never use MD5 or SHA1 for passwords.</strong> These are fast hashes that can be cracked in seconds. Always use <code>password_hash()</code> which uses bcrypt.</div>
    </div>
  </section>

  <section class="guide-section">
    <div class="section-label">Section 03</div>
    <h2 class="section-title">Salting: Defeating Rainbow Tables</h2>
    <p class="section-intro">A salt is a random value added to the password before hashing so that two users with the same password get completely different hashes.</p>
    <div class="comparison-table">
      <div class="ct-header"><div>Without Salt</div><div>With Salt (bcrypt)</div></div>
      <div class="ct-row">
        <div><code>hash("password123")</code> → same for everyone</div>
        <div><code>hash("password123" + salt)</code> → unique per user</div>
      </div>
      <div class="ct-row">
        <div>⚠️ Rainbow table attacks work</div>
        <div>✅ Rainbow tables are useless</div>
      </div>
      <div class="ct-row">
        <div>⚠️ Shared passwords are visible</div>
        <div>✅ No information leakage</div>
      </div>
    </div>
    <div class="info-box info-green">
      <span class="ib-icon">✅</span>
      <div><strong>Good news:</strong> <code>password_hash()</code> handles salting automatically. You never need to generate or store a salt yourself.</div>
    </div>
  </section>

  <section class="guide-section">
    <div class="section-label">Section 04</div>
    <h2 class="section-title">Comparing Hash Algorithms</h2>
    <div class="algo-grid">
      <div class="algo-card algo-bad">
        <div class="algo-badge danger">⚠️ Avoid for Passwords</div>
        <h3>MD5</h3>
        <ul>
          <li>128-bit output</li>
          <li>Designed for checksums</li>
          <li>Cracks in milliseconds</li>
          <li>No built-in salt</li>
        </ul>
      </div>
      <div class="algo-card algo-bad">
        <div class="algo-badge danger">⚠️ Avoid for Passwords</div>
        <h3>SHA-1 / SHA-256</h3>
        <ul>
          <li>Fast — too fast for passwords</li>
          <li>No work factor</li>
          <li>No built-in salt</li>
          <li>GPU-crackable</li>
        </ul>
      </div>
      <div class="algo-card algo-good">
        <div class="algo-badge success">✅ Recommended</div>
        <h3>bcrypt</h3>
        <ul>
          <li>60-char output</li>
          <li>Intentionally slow</li>
          <li>Built-in salt</li>
          <li>Configurable cost factor</li>
        </ul>
      </div>
      <div class="algo-card algo-good">
        <div class="algo-badge success">✅ Recommended</div>
        <h3>Argon2id</h3>
        <ul>
          <li>Memory-hard algorithm</li>
          <li>PHP 7.2+ supported</li>
          <li>Resistant to GPU attacks</li>
          <li>Password Competition winner</li>
        </ul>
      </div>
    </div>
  </section>

  <section class="guide-section">
    <div class="section-label">Section 05</div>
    <h2 class="section-title">Security Best Practices</h2>
    <div class="tips-list">
      <div class="tip-item">
        <span class="tip-num">01</span>
        <div><h4>Never store plain-text passwords</h4><p>Not in the database, not in logs, not in emails. Once hashed, discard the original.</p></div>
      </div>
      <div class="tip-item">
        <span class="tip-num">02</span>
        <div><h4>Use HTTPS everywhere</h4><p>Even with perfect hashing, passwords sent over plain HTTP can be intercepted. TLS is non-negotiable.</p></div>
      </div>
      <div class="tip-item">
        <span class="tip-num">03</span>
        <div><h4>Use prepared statements</h4><p>Always parameterise database queries with PDO to prevent SQL injection attacks.</p></div>
      </div>
      <div class="tip-item">
        <span class="tip-num">04</span>
        <div><h4>Regenerate sessions on login</h4><p>Call <code>session_regenerate_id(true)</code> after authentication to prevent session fixation.</p></div>
      </div>
      <div class="tip-item">
        <span class="tip-num">05</span>
        <div><h4>Enforce strong passwords</h4><p>Minimum 8–12 characters, mix of types, reject known breached passwords.</p></div>
      </div>
      <div class="tip-item">
        <span class="tip-num">06</span>
        <div><h4>Update bcrypt cost over time</h4><p>As hardware gets faster, increase the cost factor and re-hash on next login.</p></div>
      </div>
    </div>
  </section>

</main>

<script src="/hashguard/assets/js/dashboard.js"></script>
</body>
</html>
