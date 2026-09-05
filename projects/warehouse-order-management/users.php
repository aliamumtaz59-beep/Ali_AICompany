<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/models/User.php';
require_permission('users.manage');

$users = User::all();
$pageTitle = 'Users';
require __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h6 class="mb-0">System Users</h6>
  <a href="user_form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add User</a>
</div>

<div class="stat-card">
  <table class="table table-hover">
    <thead><tr><th>Name</th><th>Username</th><th>Role</th><th>Permissions</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><?= e($u['name']) ?></td>
        <td><?= e($u['username']) ?></td>
        <td><span class="badge bg-info text-dark"><?= e($u['role']) ?></span></td>
        <td>
          <?php if ($u['role'] === 'admin'): ?>
            <span class="text-muted">All (Admin)</span>
          <?php elseif ($u['permissions']): ?>
            <?php foreach ($u['permissions'] as $code): ?>
              <span class="badge bg-secondary"><?= e(PERMISSIONS[$code] ?? $code) ?></span>
            <?php endforeach; ?>
          <?php else: ?>
            <span class="text-muted">None</span>
          <?php endif; ?>
        </td>
        <td><span class="badge bg-<?= $u['status']==='active'?'success':'secondary' ?>"><?= e($u['status']) ?></span></td>
        <td><a href="user_form.php?id=<?= (int)$u['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
