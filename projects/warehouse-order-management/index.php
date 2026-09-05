<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/models/Order.php';
require_login();

$range = $_GET['range'] ?? 'this_month';
$today = date('Y-m-d');
[$dateFrom, $dateTo] = resolve_date_range($range, $_GET['date_from'] ?? null, $_GET['date_to'] ?? null);

$stats = Order::dashboardStats();
$trend = Order::trend($dateFrom, $dateTo);
$topProducts = Order::topProducts($dateFrom, $dateTo);
$shopSales = Order::salesByShop($dateFrom, $dateTo);

$pageTitle = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>

<form method="get" id="dashboardFilterForm" class="row g-2 mb-4 align-items-end">
  <div class="col-auto">
    <label class="form-label mb-0">Range</label>
    <select name="range" id="rangeSelect" class="form-select">
      <?php foreach (['today'=>'Today','yesterday'=>'Yesterday','this_week'=>'This Week','this_month'=>'This Month','last_month'=>'Last Month','custom'=>'Custom Range'] as $k=>$label): ?>
        <option value="<?= $k ?>" <?= $range===$k?'selected':'' ?>><?= $label ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-auto"><label class="form-label mb-0">From</label><input type="date" name="date_from" id="dateFromInput" class="form-control" value="<?= e($dateFrom) ?>"></div>
  <div class="col-auto"><label class="form-label mb-0">To</label><input type="date" name="date_to" id="dateToInput" class="form-control" value="<?= e($dateTo) ?>"></div>
  <div class="col-auto"><button class="btn btn-primary" type="submit">Apply</button></div>
</form>

<div class="row g-3 mb-4">
  <div class="col-6 col-md-4 col-xl-2">
    <a class="text-decoration-none" href="orders.php?date_from=<?= e($today) ?>&date_to=<?= e($today) ?>">
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
    </a>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <a class="text-decoration-none" href="orders.php?date_from=<?= e($today) ?>&date_to=<?= e($today) ?>">
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
    </a>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <a class="text-decoration-none" href="orders.php?date_from=<?= e(date('Y-m-01')) ?>&date_to=<?= e($today) ?>">
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
    </a>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <a class="text-decoration-none" href="orders.php?date_from=<?= e(date('Y-m-01')) ?>&date_to=<?= e($today) ?>">
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
    </a>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <a class="text-decoration-none" href="products.php?status=active">
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
    </a>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <a class="text-decoration-none" href="shops.php?status=active">
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
    </a>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-5">
    <div class="chart-card accent-blue">
      <h6><i class="bi bi-graph-up text-primary"></i> Order Trend (<span id="trendRangeLabel"><?= e(format_date($dateFrom)) ?> - <?= e(format_date($dateTo)) ?></span>)</h6>
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
        <tbody id="topProductsBody">
        <?php foreach ($topProducts as $p): ?>
          <tr><td><a href="orders.php?product_id=<?= (int)$p['id'] ?>"><?= e($p['product_code']) ?> - <?= e($p['product_name']) ?></a></td><td class="text-end"><?= (int)$p['order_count'] ?></td><td class="text-end"><?= number_format($p['total_qty'],2) ?></td></tr>
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
            <td><a href="order_view.php?id=<?= e(id_encode($o['id'])) ?>"><?= e($o['order_number']) ?></a></td>
            <td><?= e(format_date($o['order_date'])) ?></td>
            <td><?php if ($o['shop_id']): ?><a href="orders.php?shop_id=<?= (int)$o['shop_id'] ?>"><?= e($o['shop_name']) ?></a><?php endif; ?></td>
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
var trendDates = <?= json_encode(array_map(fn($r) => $r['order_date'], $trend)) ?>;
var productIds = <?= json_encode(array_map(fn($p) => (int)$p['id'], $topProducts)) ?>;
var shopIds = <?= json_encode(array_map(fn($s) => (int)$s['id'], $shopSales)) ?>;
var CHART_COLORS = ['#4facfe','#43e97b','#fa709a','#a18cd1','#fee140','#30cfd0','#667eea','#f77062','#38f9d7','#feb47b'];

var trendChart = new Chart(document.getElementById('trendChart'), {
  type: 'line',
  data: {
    labels: <?= json_encode(array_map(fn($r) => date('d-M', strtotime($r['order_date'])), $trend)) ?>,
    datasets: [{
      label: 'Orders',
      data: <?= json_encode(array_map(fn($r) => (int)$r['orders'], $trend)) ?>,
      borderColor: '#4facfe', backgroundColor: 'rgba(79,172,254,.15)', tension: .35, fill: true, pointBackgroundColor: '#00c6ff'
    }]
  },
  options: {
    responsive: true,
    onClick: function (evt, elements) {
      if (!elements.length) return;
      var date = trendDates[elements[0].index];
      window.location.href = 'orders.php?date_from=' + date + '&date_to=' + date;
    },
    onHover: function (evt, elements) { evt.native.target.style.cursor = elements.length ? 'pointer' : 'default'; },
    plugins: { legend: { display: false } }
  }
});
var productChart = new Chart(document.getElementById('productChart'), {
  type: 'bar',
  data: {
    labels: <?= json_encode(array_map(fn($p) => $p['product_code'] . ' - ' . $p['product_name'], $topProducts)) ?>,
    datasets: [{
      label: 'Quantity',
      data: <?= json_encode(array_map(fn($p) => (float)$p['total_qty'], $topProducts)) ?>,
      backgroundColor: CHART_COLORS
    }]
  },
  options: {
    responsive: true,
    onClick: function (evt, elements) {
      if (!elements.length) return;
      window.location.href = 'orders.php?product_id=' + productIds[elements[0].index];
    },
    onHover: function (evt, elements) { evt.native.target.style.cursor = elements.length ? 'pointer' : 'default'; },
    plugins: { legend: { display: false } }
  }
});
var shopChart = new Chart(document.getElementById('shopChart'), {
  type: 'doughnut',
  data: {
    labels: <?= json_encode(array_map(fn($s) => $s['shop_name'], $shopSales)) ?>,
    datasets: [{
      data: <?= json_encode(array_map(fn($s) => (float)$s['total_qty'], $shopSales)) ?>,
      backgroundColor: CHART_COLORS
    }]
  },
  options: {
    responsive: true,
    onClick: function (evt, elements) {
      if (!elements.length) return;
      window.location.href = 'orders.php?shop_id=' + shopIds[elements[0].index];
    },
    onHover: function (evt, elements) { evt.native.target.style.cursor = elements.length ? 'pointer' : 'default'; },
    plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } }
  }
});

function renderTopProductsTable(items) {
  var tbody = document.getElementById('topProductsBody');
  if (!items.length) {
    tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No data</td></tr>';
    return;
  }
  tbody.innerHTML = items.map(function (p) {
    return '<tr><td><a href="orders.php?product_id=' + p.id + '">' + p.label + '</a></td>' +
      '<td class="text-end">' + p.order_count + '</td>' +
      '<td class="text-end">' + p.qty.toFixed(2) + '</td></tr>';
  }).join('');
}

function refreshDashboard(pushHistory) {
  var form = document.getElementById('dashboardFilterForm');
  var url = window.location.pathname + '?' + new URLSearchParams(new FormData(form)).toString();
  var apiUrl = 'api/dashboard_data.php?' + new URLSearchParams(new FormData(form)).toString();

  fetch(apiUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      document.getElementById('trendRangeLabel').textContent = data.date_from_label + ' - ' + data.date_to_label;

      trendDates = data.trend.map(function (r) { return r.date; });
      trendChart.data.labels = data.trend.map(function (r) { return r.label; });
      trendChart.data.datasets[0].data = data.trend.map(function (r) { return r.orders; });
      trendChart.update();

      productIds = data.top_products.map(function (p) { return p.id; });
      productChart.data.labels = data.top_products.map(function (p) { return p.label; });
      productChart.data.datasets[0].data = data.top_products.map(function (p) { return p.qty; });
      productChart.update();
      renderTopProductsTable(data.top_products);

      shopIds = data.shop_sales.map(function (s) { return s.id; });
      shopChart.data.labels = data.shop_sales.map(function (s) { return s.label; });
      shopChart.data.datasets[0].data = data.shop_sales.map(function (s) { return s.qty; });
      shopChart.update();

      if (pushHistory !== false) history.pushState({ dashboardFilter: true }, '', url);
    })
    .catch(function () {
      form.submit();
    });
}

(function () {
  var form = document.getElementById('dashboardFilterForm');
  var rangeSelect = document.getElementById('rangeSelect');
  var dateFromInput = document.getElementById('dateFromInput');
  var dateToInput = document.getElementById('dateToInput');

  function setRangeCustom() {
    if (rangeSelect.tomselect) {
      rangeSelect.tomselect.setValue('custom', true);
    } else {
      rangeSelect.value = 'custom';
    }
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    refreshDashboard(true);
  });
  rangeSelect.addEventListener('change', function () { refreshDashboard(true); });
  dateFromInput.addEventListener('change', function () { setRangeCustom(); refreshDashboard(true); });
  dateToInput.addEventListener('change', function () { setRangeCustom(); refreshDashboard(true); });

  window.addEventListener('popstate', function () {
    var params = new URLSearchParams(window.location.search);
    rangeSelect.value = params.get('range') || 'this_month';
    dateFromInput.value = params.get('date_from') || '';
    dateToInput.value = params.get('date_to') || '';
    refreshDashboard(false);
  });
})();
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
