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
<title>Login - Warehouse Order Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="public/css/style.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center" style="min-height:100vh; background:#1e2a38;">
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body p-4">
          <h4 class="text-center mb-3"><i class="bi bi-boxes"></i> Warehouse Login</h4>
          <?php if ($error): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
          <?php endif; ?>
          <form method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
