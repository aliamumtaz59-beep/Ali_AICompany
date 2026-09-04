<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/models/User.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
$user = $id ? User::find($id) : null;
if ($id && !$user) {
    flash('danger', 'User not found.');
    redirect('users.php');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'username' => trim($_POST['username'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'role' => $_POST['role'] ?? 'user',
        'status' => $_POST['status'] ?? 'active',
    ];

    if ($data['name'] === '') $errors[] = 'Name is required.';
    if ($data['username'] === '') $errors[] = 'Username is required.';
    if (!$id && $data['password'] === '') $errors[] = 'Password is required for new users.';
    if ($data['username'] && User::usernameExists($data['username'], $id ?: null)) $errors[] = 'Username already exists.';

    if (!$errors) {
        if ($id) {
            User::update($id, $data);
            flash('success', 'User updated successfully.');
        } else {
            User::create($data);
            flash('success', 'User created successfully.');
        }
        redirect('users.php');
    }
    $user = array_merge($user ?? [], $data);
}

$pageTitle = $id ? 'Edit User' : 'Add User';
require __DIR__ . '/includes/header.php';
?>

<div class="stat-card" style="max-width:600px;">
  <?php foreach ($errors as $err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= (int)$id ?>">
    <div class="mb-3">
      <label class="form-label">Full Name</label>
      <input type="text" name="name" class="form-control" required value="<?= e($user['name'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" required value="<?= e($user['username'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Password <?= $id ? '(leave blank to keep current)' : '' ?></label>
      <input type="password" name="password" class="form-control" <?= $id ? '' : 'required' ?>>
    </div>
    <div class="mb-3">
      <label class="form-label">Role</label>
      <select name="role" class="form-select">
        <option value="user" <?= ($user['role'] ?? 'user')==='user'?'selected':'' ?>>User</option>
        <option value="admin" <?= ($user['role'] ?? '')==='admin'?'selected':'' ?>>Admin</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
        <option value="active" <?= ($user['status'] ?? 'active')==='active'?'selected':'' ?>>Active</option>
        <option value="inactive" <?= ($user['status'] ?? '')==='inactive'?'selected':'' ?>>Inactive</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Save User</button>
    <a href="users.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
