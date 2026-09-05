<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/models/Product.php';
require_permission('products.view');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle') {
    require_permission('products.manage');
    verify_csrf();
    Product::toggleStatus((int)$_POST['id']);
    flash('success', 'Product status updated.');
    redirect('products.php');
}

$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';
$products = Product::all($search ?: null, $status ?: null);

ob_start();
?>
<div class="stat-card">
  <table class="table table-hover align-middle">
    <thead>
      <tr><th>Image</th><th>Code</th><th>Name</th><th>Unit</th><th class="text-end">Qty (PCS)</th><th class="text-end">Remaining (PCS)</th><th>Owner (Shop)</th><th>Status</th><th>Updated</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php foreach ($products as $p): ?>
      <tr>
        <td>
          <?php if (!empty($p['image_path'])): ?>
            <img src="<?= e($p['image_path']) ?>" alt="" style="width:40px;height:40px;object-fit:cover;" class="border rounded">
          <?php else: ?>
            <span class="text-muted"><i class="bi bi-image"></i></span>
          <?php endif; ?>
        </td>
        <td><?= e($p['product_code']) ?></td>
        <td><?= e($p['product_name']) ?></td>
        <td><?= e($p['unit']) ?></td>
        <td class="text-end"><?= number_format($p['quantity_pcs'], 2) ?></td>
        <td class="text-end <?= $p['remaining_qty'] <= 0 ? 'text-danger fw-bold' : '' ?>"><?= number_format($p['remaining_qty'], 2) ?></td>
        <td><?= e($p['shop_name']) ?></td>
        <td><span class="badge bg-<?= $p['status']==='active'?'success':'secondary' ?>"><?= e($p['status']) ?></span></td>
        <td><?= e(format_date($p['updated_at'])) ?></td>
        <td>
          <?php if (user_has_permission('products.manage')): ?>
          <a href="product_form.php?id=<?= e(id_encode($p['id'])) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
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
    <?php if (!$products): ?><tr><td colspan="10" class="text-center text-muted">No products found</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php
$resultsHtml = ob_get_clean();

if (is_ajax_request()) {
    echo $resultsHtml;
    exit;
}

$pageTitle = 'Products';
require __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <form class="row g-2 filter-form" data-live-filter="resultsContainer">
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
  <?php if (user_has_permission('products.manage')): ?>
  <a href="product_form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Product</a>
  <?php endif; ?>
</div>

<div id="resultsContainer"><?= $resultsHtml ?></div>

<?php require __DIR__ . '/includes/footer.php'; ?>
