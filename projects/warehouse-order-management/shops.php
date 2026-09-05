<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/models/Shop.php';
require_permission('shops.view');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle') {
    require_permission('shops.manage');
    verify_csrf();
    Shop::toggleStatus((int)$_POST['id']);
    flash('success', 'Shop status updated.');
    redirect('shops.php');
}

$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';
$shops = Shop::all($search ?: null, $status ?: null);

ob_start();
?>
<div class="stat-card">
  <table class="table table-hover align-middle">
    <thead>
      <tr><th>Shop Name</th><th>Owner</th><th>Contact Number</th><th>Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php foreach ($shops as $s): ?>
      <tr>
        <td><?= e($s['shop_name']) ?></td>
        <td><?= e($s['owner_name']) ?></td>
        <td><?= e($s['contact_number']) ?></td>
        <td><span class="badge bg-<?= $s['status']==='active'?'success':'secondary' ?>"><?= e($s['status']) ?></span></td>
        <td>
          <?php if (user_has_permission('shops.manage')): ?>
          <a href="shop_form.php?id=<?= e(id_encode($s['id'])) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
          <form method="post" class="d-inline">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="toggle">
            <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
            <button class="btn btn-sm btn-outline-warning" title="Toggle Active/Inactive"><i class="bi bi-power"></i></button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$shops): ?><tr><td colspan="5" class="text-center text-muted">No shops found</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php
$resultsHtml = ob_get_clean();

if (is_ajax_request()) {
    echo $resultsHtml;
    exit;
}

$pageTitle = 'Shops';
require __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <form class="row g-2 filter-form" data-live-filter="resultsContainer">
    <div class="col-auto"><input type="text" name="search" class="form-control" placeholder="Search shop/owner" value="<?= e($search) ?>"></div>
    <div class="col-auto">
      <select name="status" class="form-select">
        <option value="">All Status</option>
        <option value="active" <?= $status==='active'?'selected':'' ?>>Active</option>
        <option value="inactive" <?= $status==='inactive'?'selected':'' ?>>Inactive</option>
      </select>
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary">Filter</button></div>
  </form>
  <?php if (user_has_permission('shops.manage')): ?>
  <a href="shop_form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Shop</a>
  <?php endif; ?>
</div>

<div id="resultsContainer"><?= $resultsHtml ?></div>

<?php require __DIR__ . '/includes/footer.php'; ?>
