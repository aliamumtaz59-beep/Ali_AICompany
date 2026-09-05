<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/models/Order.php';
require_login();

$type = $_GET['type'] ?? 'daily';
$dateFrom = $_GET['date_from'] ?? date('Y-m-01');
$dateTo = $_GET['date_to'] ?? date('Y-m-d');

if ($type === 'daily') {
    $rows = Order::dailyReport($dateFrom, $dateTo);
} elseif ($type === 'monthly') {
    $rows = Order::monthlyReport($dateFrom, $dateTo);
} elseif ($type === 'product') {
    $rows = Order::productReport($dateFrom, $dateTo);
} else {
    $custom = Order::customReport($dateFrom, $dateTo);
    $rows = $custom['daily'];
}

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="report_' . $type . '_' . date('Ymd_His') . '.csv"');
    $out = fopen('php://output', 'w');
    if ($rows) {
        fputcsv($out, array_keys($rows[0]));
        foreach ($rows as $row) fputcsv($out, $row);
    }
    fclose($out);
    exit;
}

$exportUrl = '?' . http_build_query(array_merge($_GET, ['export' => 'csv']));

ob_start();
?>
<div class="d-flex justify-content-end mb-2">
  <a class="btn btn-outline-success btn-sm" href="<?= e($exportUrl) ?>"><i class="bi bi-download"></i> Export CSV</a>
</div>
<div class="stat-card">
  <?php if ($type === 'daily' || $type === 'custom'): ?>
    <table class="table table-hover">
      <thead><tr><th>Date</th><th class="text-end">Orders</th><th class="text-end">Quantity</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr><td><?= e(format_date($r['order_date'])) ?></td><td class="text-end"><?= (int)$r['orders'] ?></td><td class="text-end"><?= number_format($r['quantity'],2) ?></td></tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="3" class="text-center text-muted">No data</td></tr><?php endif; ?>
      </tbody>
    </table>
  <?php elseif ($type === 'monthly'): ?>
    <table class="table table-hover">
      <thead><tr><th>Month</th><th class="text-end">Orders</th><th class="text-end">Quantity</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr><td><?= e(date('M Y', strtotime($r['month'].'-01'))) ?></td><td class="text-end"><?= (int)$r['orders'] ?></td><td class="text-end"><?= number_format($r['quantity'],2) ?></td></tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="3" class="text-center text-muted">No data</td></tr><?php endif; ?>
      </tbody>
    </table>
  <?php elseif ($type === 'product'): ?>
    <table class="table table-hover">
      <thead><tr><th>Product</th><th class="text-end">Orders</th><th class="text-end">Quantity</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr><td><a href="orders.php?product_id=<?= (int)$r['id'] ?>"><?= e($r['product_code']) ?> - <?= e($r['product_name']) ?></a></td><td class="text-end"><?= (int)$r['order_count'] ?></td><td class="text-end"><?= number_format($r['total_qty'],2) ?></td></tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="3" class="text-center text-muted">No data</td></tr><?php endif; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <?php if ($type === 'custom'): ?>
    <hr>
    <h6>Product-wise Summary</h6>
    <table class="table table-sm">
      <thead><tr><th>Product</th><th class="text-end">Orders</th><th class="text-end">Quantity</th></tr></thead>
      <tbody>
      <?php foreach ($custom['products'] as $r): ?>
        <tr><td><?= e($r['product_code']) ?> - <?= e($r['product_name']) ?></td><td class="text-end"><?= (int)$r['order_count'] ?></td><td class="text-end"><?= number_format($r['total_qty'],2) ?></td></tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <div class="alert alert-info">
      Total Orders: <strong><?= (int)$custom['totals']['orders'] ?></strong> &nbsp;|&nbsp;
      Total Quantity: <strong><?= number_format($custom['totals']['quantity'],2) ?></strong>
    </div>
  <?php endif; ?>
</div>
<?php
$resultsHtml = ob_get_clean();

if (is_ajax_request()) {
    echo $resultsHtml;
    exit;
}

$pageTitle = 'Reports';
require __DIR__ . '/includes/header.php';
?>

<form class="row g-2 mb-3 filter-form" data-live-filter="resultsContainer">
  <div class="col-auto">
    <select name="type" class="form-select">
      <option value="daily" <?= $type==='daily'?'selected':'' ?>>Daily Report</option>
      <option value="monthly" <?= $type==='monthly'?'selected':'' ?>>Monthly Report</option>
      <option value="product" <?= $type==='product'?'selected':'' ?>>Product Report</option>
      <option value="custom" <?= $type==='custom'?'selected':'' ?>>Custom Date Report</option>
    </select>
  </div>
  <div class="col-auto"><input type="date" name="date_from" class="form-control" value="<?= e($dateFrom) ?>"></div>
  <div class="col-auto"><input type="date" name="date_to" class="form-control" value="<?= e($dateTo) ?>"></div>
  <div class="col-auto"><button class="btn btn-primary">Apply</button></div>
</form>

<div id="resultsContainer"><?= $resultsHtml ?></div>

<?php require __DIR__ . '/includes/footer.php'; ?>
