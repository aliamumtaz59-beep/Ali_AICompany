<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/models/Order.php';
require_once __DIR__ . '/models/Shop.php';
require_once __DIR__ . '/models/Product.php';
require_permission('reports.view');

$dateFrom = $_GET['date_from'] ?? date('Y-m-01');
$dateTo = $_GET['date_to'] ?? date('Y-m-d');
$shopId = (int)($_GET['shop_id'] ?? 0) ?: null;
$productId = (int)($_GET['product_id'] ?? 0) ?: null;

$rows = Order::detailedReport($dateFrom, $dateTo, $shopId, $productId);

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="detailed_report_' . date('Ymd_His') . '.csv"');
    $out = fopen('php://output', 'w');
    if ($rows) {
        fputcsv($out, array_keys($rows[0]));
        foreach ($rows as $row) fputcsv($out, $row);
    }
    fclose($out);
    exit;
}

$shops = Shop::active();
$products = Product::active();
$totalQty = array_sum(array_column($rows, 'quantity'));
$orderCount = count(array_unique(array_column($rows, 'order_number')));
$exportUrl = '?' . http_build_query(array_merge($_GET, ['export' => 'csv']));

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
  <div class="alert alert-info mb-0 py-2 flex-grow-1">
    Line Items: <strong><?= count($rows) ?></strong> &nbsp;|&nbsp;
    Orders: <strong><?= $orderCount ?></strong> &nbsp;|&nbsp;
    Total Quantity: <strong><?= number_format($totalQty, 2) ?></strong>
  </div>
  <a class="btn btn-outline-success btn-sm" href="<?= e($exportUrl) ?>"><i class="bi bi-download"></i> Export CSV</a>
</div>

<div class="stat-card">
  <div class="table-responsive">
  <table class="table table-hover table-sm">
    <thead><tr><th>Date</th><th>Order #</th><th>Shop Name</th><th class="text-end">Shop ID</th><th>Product Name</th><th>Barcode</th><th class="text-end">Quantity</th><th>Unit</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= e(format_date($r['order_date'])) ?></td>
        <td><a href="orders.php?order_number=<?= urlencode($r['order_number']) ?>"><?= e($r['order_number']) ?></a></td>
        <td><?php if ($r['shop_id']): ?><a href="orders.php?shop_id=<?= (int)$r['shop_id'] ?>"><?= e($r['shop_name']) ?></a><?php endif; ?></td>
        <td class="text-end"><?= (int)$r['shop_id'] ?></td>
        <td><a href="orders.php?product_id=<?= (int)$r['product_id'] ?>"><?= e($r['product_code']) ?> - <?= e($r['product_name']) ?></a></td>
        <td><?= e($r['barcode_no']) ?></td>
        <td class="text-end"><?= number_format($r['quantity'],2) ?></td>
        <td><?= e($r['unit']) ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="8" class="text-center text-muted">No data for the selected filters</td></tr><?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
<?php
$resultsHtml = ob_get_clean();

if (is_ajax_request()) {
    echo $resultsHtml;
    exit;
}

$pageTitle = 'Detailed Report';
require __DIR__ . '/includes/header.php';
?>

<form class="row g-2 mb-3 filter-form" data-live-filter="resultsContainer">
  <div class="col-auto"><label class="form-label mb-0">From</label><input type="date" name="date_from" class="form-control" value="<?= e($dateFrom) ?>"></div>
  <div class="col-auto"><label class="form-label mb-0">To</label><input type="date" name="date_to" class="form-control" value="<?= e($dateTo) ?>"></div>
  <div class="col-auto">
    <label class="form-label mb-0">Shop</label>
    <select name="shop_id" class="form-select">
      <option value="">All Shops</option>
      <?php foreach ($shops as $s): ?>
        <option value="<?= (int)$s['id'] ?>" <?= $shopId==$s['id']?'selected':'' ?>><?= e($s['shop_name']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-auto">
    <label class="form-label mb-0">Product</label>
    <select name="product_id" class="form-select">
      <option value="">All Products</option>
      <?php foreach ($products as $p): ?>
        <option value="<?= (int)$p['id'] ?>" <?= $productId==$p['id']?'selected':'' ?>><?= e($p['product_code']) ?> - <?= e($p['product_name']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-auto align-self-end"><button class="btn btn-primary">Apply</button></div>
</form>

<div id="resultsContainer"><?= $resultsHtml ?></div>

<?php require __DIR__ . '/includes/footer.php'; ?>
