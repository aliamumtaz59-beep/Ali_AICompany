<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/models/Order.php';
require_login();

$range = $_GET['range'] ?? 'this_month';
$today = date('Y-m-d');

switch ($range) {
    case 'today':
        $dateFrom = $dateTo = $today;
        break;
    case 'yesterday':
        $dateFrom = $dateTo = date('Y-m-d', strtotime('-1 day'));
        break;
    case 'this_week':
        $dateFrom = date('Y-m-d', strtotime('monday this week'));
        $dateTo = $today;
        break;
    case 'last_month':
        $dateFrom = date('Y-m-01', strtotime('first day of last month'));
        $dateTo = date('Y-m-t', strtotime('last day of last month'));
        break;
    case 'custom':
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo = $_GET['date_to'] ?? $today;
        break;
    case 'this_month':
    default:
        $dateFrom = date('Y-m-01');
        $dateTo = $today;
        $range = 'this_month';
        break;
}

$stats = Order::dashboardStats();
$trend = Order::trend($dateFrom, $dateTo);
$topProducts = Order::topProducts($dateFrom, $dateTo);
$shopSales = Order::salesByShop($dateFrom, $dateTo);

$pageTitle = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>

<form method="get" class="row g-2 mb-4 align-items-end">
  <div class="col-auto">
    <label class="form-label mb-0">Range</label>
    <select name="range" class="form-select" onchange="this.form.submit()">
      <?php foreach (['today'=>'Today','yesterday'=>'Yesterday','this_week'=>'This Week','this_month'=>'This Month','last_month'=>'Last Month','custom'=>'Custom Range'] as $k=>$label): ?>
        <option value="<?= $k ?>" <?= $range===$k?'selected':'' ?>><?= $label ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-auto"><label class="form-label mb-0">From</label><input type="date" name="date_from" class="form-control" value="<?= e($dateFrom) ?>"></div>
  <div class="col-auto"><label class="form-label mb-0">To</label><input type="date" name="date_to" class="form-control" value="<?= e($dateTo) ?>"></div>
  <div class="col-auto"><button class="btn btn-primary" type="submit">Apply</button></div>
</form>

<div class="row g-3 mb-4">
  <div class="col-6 col-md-4 col-xl-2">
    <div class="kpi-card kpi-coral">
      <i class="bi bi-cart-check kpi-bg-icon"></i>
      <div class="d-flex align-items-center gap-3">
        <div class="kpi-icon"><i class="bi bi-cart-check"></i></div>
        <div>
          <div class="kpi-value"><?= (int)$stats['today_orders'] ?></div>
          <div class="kpi-label">Today's Orders</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="kpi-card kpi-green">
      <i class="bi bi-boxes kpi-bg-icon"></i>
      <div class="d-flex align-items-center gap-3">
        <div class="kpi-icon"><i class="bi bi-boxes"></i></div>
        <div>
          <div class="kpi-value"><?= number_format($stats['today_qty'], 0) ?></div>
          <div class="kpi-label">Today's Quantity</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="kpi-card kpi-purple">
      <i class="bi bi-calendar-month kpi-bg-icon"></i>
      <div class="d-flex align-items-center gap-3">
        <div class="kpi-icon"><i class="bi bi-calendar-month"></i></div>
        <div>
          <div class="kpi-value"><?= (int)$stats['month_orders'] ?></div>
          <div class="kpi-label">This Month Orders</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="kpi-card kpi-orange">
      <i class="bi bi-graph-up-arrow kpi-bg-icon"></i>
      <div class="d-flex align-items-center gap-3">
        <div class="kpi-icon"><i class="bi bi-graph-up-arrow"></i></div>
        <div>
          <div class="kpi-value"><?= number_format($stats['month_qty'], 0) ?></div>
          <div class="kpi-label">This Month Quantity</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="kpi-card kpi-teal">
      <i class="bi bi-box-seam kpi-bg-icon"></i>
      <div class="d-flex align-items-center gap-3">
        <div class="kpi-icon"><i class="bi bi-box-seam"></i></div>
        <div>
          <div class="kpi-value"><?= (int)$stats['active_products'] ?></div>
          <div class="kpi-label">Active Products</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="kpi-card kpi-indigo">
      <i class="bi bi-shop kpi-bg-icon"></i>
      <div class="d-flex align-items-center gap-3">
        <div class="kpi-icon"><i class="bi bi-shop"></i></div>
        <div>
          <div class="kpi-value"><?= (int)$stats['active_shops'] ?></div>
          <div class="kpi-label">Active Shops</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-5">
    <div class="chart-card accent-blue">
      <h6><i class="bi bi-graph-up text-primary"></i> Order Trend (<?= e(format_date($dateFrom)) ?> - <?= e(format_date($dateTo)) ?>)</h6>
      <canvas id="trendChart" height="140"></canvas>
    </div>
  </div>
  <div class="col-md-4">
    <div class="chart-card accent-green">
      <h6><i class="bi bi-bar-chart-fill text-success"></i> Top Products (Quantity)</h6>
      <canvas id="productChart" height="140"></canvas>
    </div>
  </div>
  <div class="col-md-3">
    <div class="chart-card accent-coral">
      <h6><i class="bi bi-shop text-purple"></i> Sales by Shop</h6>
      <canvas id="shopChart" height="140"></canvas>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-md-6">
    <div class="stat-card">
      <h6>Top Products</h6>
      <table class="table table-sm">
        <thead><tr><th>Product</th><th class="text-end">Orders</th><th class="text-end">Qty</th></tr></thead>
        <tbody>
        <?php foreach ($topProducts as $p): ?>
          <tr><td><?= e($p['product_code']) ?> - <?= e($p['product_name']) ?></td><td class="text-end"><?= (int)$p['order_count'] ?></td><td class="text-end"><?= number_format($p['total_qty'],2) ?></td></tr>
        <?php endforeach; ?>
        <?php if (!$topProducts): ?><tr><td colspan="3" class="text-center text-muted">No data</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-md-6">
    <div class="stat-card">
      <h6>Recent Orders</h6>
      <table class="table table-sm">
        <thead><tr><th>Order #</th><th>Date</th><th>Shop</th><th class="text-end">Qty</th></tr></thead>
        <tbody>
        <?php foreach ($stats['recent_orders'] as $o): ?>
          <tr>
            <td><a href="order_view.php?id=<?= (int)$o['id'] ?>"><?= e($o['order_number']) ?></a></td>
            <td><?= e(format_date($o['order_date'])) ?></td>
            <td><?= e($o['shop_name']) ?></td>
            <td class="text-end"><?= number_format($o['total_quantity'],2) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$stats['recent_orders']): ?><tr><td colspan="4" class="text-center text-muted">No orders yet</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('trendChart'), {
  type: 'line',
  data: {
    labels: <?= json_encode(array_map(fn($r) => date('d-M', strtotime($r['order_date'])), $trend)) ?>,
    datasets: [{
      label: 'Orders',
      data: <?= json_encode(array_map(fn($r) => (int)$r['orders'], $trend)) ?>,
      borderColor: '#4facfe', backgroundColor: 'rgba(79,172,254,.15)', tension: .35, fill: true, pointBackgroundColor: '#00c6ff'
    }]
  },
  options: { responsive: true, plugins: { legend: { display: false } } }
});
new Chart(document.getElementById('productChart'), {
  type: 'bar',
  data: {
    labels: <?= json_encode(array_map(fn($p) => $p['product_code'], $topProducts)) ?>,
    datasets: [{
      label: 'Quantity',
      data: <?= json_encode(array_map(fn($p) => (float)$p['total_qty'], $topProducts)) ?>,
      backgroundColor: ['#4facfe','#43e97b','#fa709a','#a18cd1','#fee140','#30cfd0','#667eea','#f77062','#38f9d7','#feb47b']
    }]
  },
  options: { responsive: true, plugins: { legend: { display: false } } }
});
new Chart(document.getElementById('shopChart'), {
  type: 'doughnut',
  data: {
    labels: <?= json_encode(array_map(fn($s) => $s['shop_name'], $shopSales)) ?>,
    datasets: [{
      data: <?= json_encode(array_map(fn($s) => (float)$s['total_qty'], $shopSales)) ?>,
      backgroundColor: ['#4facfe','#43e97b','#fa709a','#a18cd1','#fee140','#30cfd0','#667eea','#f77062','#38f9d7','#feb47b']
    }]
  },
  options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } } }
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
