<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/models/Product.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle') {
    require_admin();
    verify_csrf();
    Product::toggleStatus((int)$_POST['id']);
    flash('success', 'Product status updated.');
    redirect('products.php');
}

$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';
$products = Product::all($search ?: null, $status ?: null);

$pageTitle = 'Products';
require __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <form class="row g-2">
    <div class="col-auto"><input type="text" name="search" class="form-control" placeholder="Search code/name" value="<?= e($search) ?>"></div>
    <div class="col-auto">
      <select name="status" class="form-select">
        <option value="">All Status</option>
        <option value="active" <?= $status==='active'?'selected':'' ?>>Active</option>
        <option value="inactive" <?= $status==='inactive'?'selected':'' ?>>Inactive</option>
      </select>
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary">Filter</button></div>
  </form>
  <?php if (current_user()['role'] === 'admin'): ?>
  <a href="product_form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Product</a>
  <?php endif; ?>
</div>

<div class="stat-card">
  <table class="table table-hover align-middle">
    <thead>
      <tr><th>Code</th><th>Name</th><th>Unit</th><th>Status</th><th>Updated</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php foreach ($products as $p): ?>
      <tr>
        <td><?= e($p['product_code']) ?></td>
        <td><?= e($p['product_name']) ?></td>
        <td><?= e($p['unit']) ?></td>
        <td><span class="badge bg-<?= $p['status']==='active'?'success':'secondary' ?>"><?= e($p['status']) ?></span></td>
        <td><?= e(format_date($p['updated_at'])) ?></td>
        <td>
          <?php if (current_user()['role'] === 'admin'): ?>
          <a href="product_form.php?id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
          <form method="post" class="d-inline">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="toggle">
            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
            <button class="btn btn-sm btn-outline-warning" title="Toggle Active/Inactive"><i class="bi bi-power"></i></button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$products): ?><tr><td colspan="6" class="text-center text-muted">No products found</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
