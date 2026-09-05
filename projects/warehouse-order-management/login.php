<?php
require_once __DIR__ . '/includes/functions.php';

if (current_user()) {
    redirect('index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username && $password && attempt_login($username, $password)) {
        redirect('index.php');
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Armadio - Warehouse Login</title>
<link rel="icon" type="image/svg+xml" href="public/favicon.svg">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@1,600;1,700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  * { font-family: 'Inter', sans-serif; }
  body {
    min-height: 100vh;
    background:
      radial-gradient(circle at 15% 20%, rgba(232,120,90,.18), transparent 40%),
      radial-gradient(circle at 85% 80%, rgba(232,120,90,.12), transparent 45%),
      linear-gradient(160deg, #1c1815 0%, #2a2420 50%, #1c1815 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
  }
  .brand-mark { display: flex; flex-direction: column; align-items: center; margin-bottom: 2rem; }
  .brand-icon {
    width: 64px; height: 64px; border-radius: 18px;
    background: linear-gradient(135deg, #f2a58a, #e8785a);
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 8px 24px rgba(232,120,90,.35);
    margin-bottom: .9rem;
  }
  .brand-name {
    font-family: 'Playfair Display', serif;
    font-style: italic;
    font-weight: 700;
    font-size: 2.1rem;
    color: #f5efe9;
    letter-spacing: .5px;
  }
  .brand-tagline { color: #b8ada3; font-size: .85rem; letter-spacing: 1.5px; text-transform: uppercase; margin-top: .2rem; }
  .login-card {
    background: rgba(255,255,255,.98);
    border-radius: 1rem;
    box-shadow: 0 20px 60px rgba(0,0,0,.35);
    padding: 2.2rem 2.2rem 2rem;
    width: 100%;
    max-width: 400px;
  }
  .login-card h5 { font-weight: 600; color: #2a2420; }
  .login-card .subtitle { color: #8a8078; font-size: .9rem; margin-bottom: 1.6rem; }
  .form-label { font-size: .82rem; font-weight: 600; color: #5a5049; text-transform: uppercase; letter-spacing: .5px; }
  .input-group-text { background: #f5efe9; border-right: none; color: #e8785a; }
  .form-control { border-left: none; padding: .65rem .8rem; }
  .form-control:focus { box-shadow: none; border-color: #e8785a; }
  .input-group:focus-within .input-group-text { border-color: #e8785a; }
  .btn-armadio {
    background: linear-gradient(135deg, #f2a58a, #e8785a);
    border: none; color: #fff; font-weight: 600; padding: .7rem; border-radius: .5rem;
    transition: transform .15s, box-shadow .15s;
  }
  .btn-armadio:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(232,120,90,.4); color: #fff; }
  .footer-note { text-align: center; color: #8a8078; font-size: .78rem; margin-top: 1.4rem; }
</style>
</head>
<body>
<div>
  <div class="brand-mark">
    <div class="brand-icon">
      <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M6 8h12l1 12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L6 8z"/>
        <path d="M9 8V6a3 3 0 0 1 6 0v2"/>
      </svg>
    </div>
    <div class="brand-name">Armadio</div>
    <div class="brand-tagline">Warehouse Order Management</div>
  </div>

  <div class="login-card">
    <h5>Welcome back</h5>
    <div class="subtitle">Sign in to manage your orders</div>
    <?php if ($error): ?>
      <div class="alert alert-danger py-2"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="post">
      <?= csrf_field() ?>
      <div class="mb-3">
        <label class="form-label">Username</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input type="text" name="username" class="form-control" required autofocus>
        </div>
      </div>
      <div class="mb-4">
        <label class="form-label">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" name="password" class="form-control" required>
        </div>
      </div>
      <button type="submit" class="btn btn-armadio w-100">Sign In</button>
    </form>
  </div>
  <div class="footer-note">&copy; <?= date('Y') ?> Armadio. All rights reserved.</div>
</div>
</body>
</html>
