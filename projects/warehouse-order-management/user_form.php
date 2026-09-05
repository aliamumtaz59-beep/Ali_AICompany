<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/models/User.php';
require_permission('users.manage');

$id = isset($_GET['id']) ? id_decode($_GET['id']) : (isset($_POST['id']) ? id_decode($_POST['id']) : 0);
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
        'permissions' => array_values(array_intersect((array)($_POST['permissions'] ?? []), array_keys(PERMISSIONS))),
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

$userPermissions = $user['permissions'] ?? [];

$pageTitle = $id ? 'Edit User' : 'Add User';
require __DIR__ . '/includes/header.php';
?>

<div class="stat-card" style="max-width:600px;">
  <?php foreach ($errors as $err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post" autocomplete="off">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= e($id ? id_encode($id) : '') ?>">
    <!-- Decoy fields: this form edits an arbitrary user account, not the logged-in browser's own
         login, but browsers pattern-match "username + password inputs" as a login form and will
         silently overwrite the real fields below with a saved credential the instant the page
         loads. These hidden, unfocusable fields catch that autofill instead. -->
    <input type="text" name="fake_username" autocomplete="username" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;" tabindex="-1" aria-hidden="true">
    <input type="password" name="fake_password" autocomplete="current-password" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;" tabindex="-1" aria-hidden="true">
    <div class="mb-3">
      <label class="form-label">Full Name</label>
      <input type="text" name="name" class="form-control" autocomplete="off" required value="<?= e($user['name'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" autocomplete="off" required value="<?= e($user['username'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Password <?= $id ? '(leave blank to keep current)' : '' ?></label>
      <input type="password" name="password" class="form-control" autocomplete="new-password" <?= $id ? '' : 'required' ?>>
    </div>
    <div class="mb-3">
      <label class="form-label">Role</label>
      <select name="role" id="roleSelect" class="form-select">
        <option value="user" <?= ($user['role'] ?? 'user')==='user'?'selected':'' ?>>User</option>
        <option value="admin" <?= ($user['role'] ?? '')==='admin'?'selected':'' ?>>Admin</option>
      </select>
      <div class="form-text">Admins always have full access to everything, regardless of the permissions below.</div>
    </div>
    <div class="mb-3" id="permissionsBlock">
      <label class="form-label d-block">Permissions</label>
      <div class="row">
        <?php foreach (PERMISSIONS as $code => $label): ?>
          <div class="col-6">
            <div class="form-check">
              <input type="checkbox" name="permissions[]" value="<?= e($code) ?>" class="form-check-input" id="perm_<?= e($code) ?>" <?= in_array($code, $userPermissions, true) ? 'checked' : '' ?>>
              <label class="form-check-label" for="perm_<?= e($code) ?>"><?= e($label) ?></label>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
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

<script>
(function () {
  var roleSelect = document.getElementById('roleSelect');
  var block = document.getElementById('permissionsBlock');
  function toggle() {
    block.style.opacity = roleSelect.value === 'admin' ? '.4' : '1';
  }
  roleSelect.addEventListener('change', toggle);
  toggle();
})();
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
