<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/models/Order.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$order = Order::find($id);
if (!$order) {
    flash('danger', 'Order not found.');
    redirect('orders.php');
}

$totalQty = array_sum(array_column($order['items'], 'quantity'));

$pageTitle = 'Order ' . $order['order_number'];
require __DIR__ . '/includes/header.php';
?>

<div class="stat-card" id="printArea">
  <div class="d-flex justify-content-between mb-3">
    <div>
      <h5>Order: <?= e($order['order_number']) ?></h5>
      <div class="text-muted">Order Date: <?= e(format_date($order['order_date'])) ?></div>
      <div class="text-muted">Created: <?= e(format_date($order['created_at'])) ?></div>
      <?php if ($order['remarks']): ?><div class="text-muted">Remarks: <?= e($order['remarks']) ?></div><?php endif; ?>
    </div>
    <div class="d-print-none">
      <a href="order_form.php?id=<?= (int)$order['id'] ?>" class="btn btn-outline-primary"><i class="bi bi-pencil"></i> Edit</a>
      <button onclick="window.print()" class="btn btn-outline-secondary"><i class="bi bi-printer"></i> Print</button>
      <a href="orders.php" class="btn btn-secondary">Back</a>
    </div>
  </div>
  <table class="table table-bordered">
    <thead><tr><th>Product</th><th class="text-end">Quantity</th><th>Unit</th><th>Remarks</th></tr></thead>
    <tbody>
    <?php foreach ($order['items'] as $item): ?>
      <tr>
        <td><?= e($item['product_code']) ?> - <?= e($item['product_name']) ?></td>
        <td class="text-end"><?= number_format($item['quantity'], 2) ?></td>
        <td><?= e($item['unit']) ?></td>
        <td><?= e($item['remarks']) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr><th>Total</th><th class="text-end"><?= number_format($totalQty, 2) ?></th><th colspan="2"></th></tr>
    </tfoot>
  </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
