<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/pagination.php';
require_once __DIR__ . '/models/Order.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/models/Shop.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    require_admin();
    verify_csrf();
    Order::delete((int)$_POST['id']);
    flash('success', 'Order deleted successfully.');
    redirect('orders.php');
}

$filters = [
    'order_number' => trim($_GET['order_number'] ?? ''),
    'date_from' => $_GET['date_from'] ?? '',
    'date_to' => $_GET['date_to'] ?? '',
    'product_id' => $_GET['product_id'] ?? '',
    'shop_id' => $_GET['shop_id'] ?? '',
];
$page = max(1, (int)($_GET['page'] ?? 1));
$result = Order::paginated($filters, $page);
$products = Product::active();
$shops = Shop::active();

ob_start();
?>
<div class="stat-card">
  <table class="table table-hover align-middle">
    <thead>
      <tr><th>Order #</th><th>Date</th><th>Shop</th><th class="text-end">Products</th><th class="text-end">Total Qty</th><th>Created</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php foreach ($result['data'] as $o): ?>
      <tr>
        <td><a href="order_view.php?id=<?= (int)$o['id'] ?>"><?= e($o['order_number']) ?></a></td>
        <td><?= e(format_date($o['order_date'])) ?></td>
        <td><?= e($o['shop_name']) ?></td>
        <td class="text-end"><?= (int)$o['item_count'] ?></td>
        <td class="text-end"><?= number_format($o['total_quantity'], 2) ?></td>
        <td><?= e(format_date($o['created_at'])) ?></td>
        <td>
          <a href="order_view.php?id=<?= (int)$o['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
          <a href="order_form.php?id=<?= (int)$o['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
          <?php if (current_user()['role'] === 'admin'): ?>
          <form method="post" class="d-inline confirm-delete-form">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
            <button type="submit" class="btn btn-sm btn-outline-danger confirm-delete"><i class="bi bi-trash"></i></button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$result['data']): ?><tr><td colspan="7" class="text-center text-muted">No orders found</td></tr><?php endif; ?>
    </tbody>
  </table>
  <?php render_pagination($page, $result['pages'], array_filter($filters)); ?>
</div>
<?php
$resultsHtml = ob_get_clean();

if (is_ajax_request()) {
    echo $resultsHtml;
    exit;
}

$pageTitle = 'Orders';
require __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <form class="row g-2 filter-form" data-live-filter="resultsContainer">
    <div class="col-auto"><input type="text" name="order_number" class="form-control" placeholder="Order #" value="<?= e($filters['order_number']) ?>"></div>
    <div class="col-auto"><input type="date" name="date_from" class="form-control" value="<?= e($filters['date_from']) ?>"></div>
    <div class="col-auto"><input type="date" name="date_to" class="form-control" value="<?= e($filters['date_to']) ?>"></div>
    <div class="col-auto">
      <select name="product_id" class="form-select">
        <option value="">All Products</option>
        <?php foreach ($products as $p): ?>
          <option value="<?= (int)$p['id'] ?>" <?= ($filters['product_id']==$p['id'])?'selected':'' ?>><?= e($p['product_code']) ?> - <?= e($p['product_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-auto">
      <select name="shop_id" class="form-select">
        <option value="">All Shops</option>
        <?php foreach ($shops as $s): ?>
          <option value="<?= (int)$s['id'] ?>" <?= ($filters['shop_id']==$s['id'])?'selected':'' ?>><?= e($s['shop_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary">Filter</button></div>
  </form>
  <a href="order_form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Order</a>
</div>

<div id="resultsContainer"><?= $resultsHtml ?></div>

<?php require __DIR__ . '/includes/footer.php'; ?>
