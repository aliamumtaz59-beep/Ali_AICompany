<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/models/Order.php';
require_once __DIR__ . '/models/Attachment.php';
require_permission('orders.view');

$id = id_decode($_GET['id'] ?? '');
$order = $id ? Order::find($id) : null;
if (!$order) {
    flash('danger', 'Order not found.');
    redirect('orders.php');
}

// Handle shipment marking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && user_has_permission('orders.manage')) {
    verify_csrf();
    $shipmentNumber = trim($_POST['shipment_number'] ?? '');
    if (!$shipmentNumber) {
        flash('danger', 'Shipment number is required.');
    } else {
        // Mark order as shipped
        Order::markShipped($id, $shipmentNumber, current_user()['id']);

        // Upload shipment proof if provided
        foreach (normalize_files($_FILES['shipment_proof'] ?? []) as $file) {
            $uploadError = Attachment::upload($id, $file, 'shipment_proof');
            if ($uploadError) flash('warning', $uploadError);
        }

        flash('success', 'Order marked as shipped to customer.');
        $order = Order::find($id);
    }
}

$totalQty = array_sum(array_column($order['items'], 'quantity'));
$attachments = Attachment::forOrder($id);

$pageTitle = 'Order ' . $order['order_number'];
require __DIR__ . '/includes/header.php';
?>

<div class="stat-card" id="printArea">
  <div class="d-flex justify-content-between mb-3">
    <div>
      <h5>Order: <?= e($order['order_number']) ?></h5>
      <div class="text-muted">Order Date: <?= e(format_date($order['order_date'])) ?></div>
      <?php if ($order['shop_name']): ?>
      <div class="text-muted">Shop: <?= e($order['shop_name']) ?><?= $order['shop_owner_name'] ? ' - ' . e($order['shop_owner_name']) : '' ?><?= $order['shop_contact_number'] ? ' (' . e($order['shop_contact_number']) . ')' : '' ?></div>
      <?php endif; ?>
      <div class="text-muted">Created: <?= e(format_date($order['created_at'])) ?></div>
      <?php if ($order['remarks']): ?><div class="text-muted">Remarks: <?= e($order['remarks']) ?></div><?php endif; ?>
      <?php if ($order['barcode_no']): ?><div class="text-muted">Barcode No: <?= e($order['barcode_no']) ?></div><?php endif; ?>
      <?php if ($order['tiktok_order_number']): ?><div class="text-muted">TikTok Order #: <?= e($order['tiktok_order_number']) ?></div><?php endif; ?>
      <div class="mt-2">
        <span class="badge bg-<?= $order['status'] === 'pending_dispatch' ? 'warning' : 'success' ?>">
          <?= $order['status'] === 'pending_dispatch' ? 'Pending for Dispatch' : 'Shipped to Customer' ?>
        </span>
      </div>
      <?php if ($order['shipment_number']): ?><div class="text-muted">Shipment #: <?= e($order['shipment_number']) ?></div><?php endif; ?>
    </div>
    <div class="d-print-none">
      <?php if (user_has_permission('orders.manage')): ?>
      <a href="order_form.php?id=<?= e(id_encode($order['id'])) ?>" class="btn btn-outline-primary"><i class="bi bi-pencil"></i> Edit</a>
      <?php endif; ?>
      <button onclick="window.print()" class="btn btn-outline-secondary"><i class="bi bi-printer"></i> Print</button>
      <a href="orders.php" class="btn btn-secondary">Back</a>
    </div>
  </div>
  <table class="table table-bordered">
    <thead><tr><th>Image</th><th>Product</th><th class="text-end">Quantity</th><th>Unit</th><th>Remarks</th></tr></thead>
    <tbody>
    <?php foreach ($order['items'] as $item): ?>
      <tr>
        <td>
          <?php if (!empty($item['image_path'])): ?>
            <img src="<?= e($item['image_path']) ?>" alt="" style="width:40px;height:40px;object-fit:cover;" class="border rounded">
          <?php else: ?>
            <span class="text-muted"><i class="bi bi-image"></i></span>
          <?php endif; ?>
        </td>
        <td><?= e($item['product_code']) ?> - <?= e($item['product_name']) ?></td>
        <td class="text-end"><?= number_format($item['quantity'], 2) ?></td>
        <td><?= e($item['unit']) ?></td>
        <td><?= e($item['remarks']) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr><th colspan="2">Total</th><th class="text-end"><?= number_format($totalQty, 2) ?></th><th colspan="2"></th></tr>
    </tfoot>
  </table>

  <?php
    $orderDocs = array_filter($attachments, fn($a) => ($a['type'] ?? 'order_document') === 'order_document');
    $shipmentDocs = array_filter($attachments, fn($a) => ($a['type'] ?? 'order_document') === 'shipment_proof');
  ?>

  <?php if ($orderDocs): ?>
  <hr>
  <h6>📦 Order Files (Admin Uploaded)</h6>
  <div class="d-flex flex-wrap gap-3">
    <?php foreach ($orderDocs as $a): ?>
      <div class="text-center">
        <?php if (str_starts_with($a['mime_type'] ?? '', 'image/')): ?>
          <a href="api/attachment_download.php?id=<?= (int)$a['id'] ?>" target="_blank">
            <img src="api/attachment_download.php?id=<?= (int)$a['id'] ?>" alt="<?= e($a['original_name']) ?>" style="max-width:120px;max-height:120px;object-fit:cover;" class="border rounded">
          </a>
        <?php else: ?>
          <a href="api/attachment_download.php?id=<?= (int)$a['id'] ?>" target="_blank" class="btn btn-outline-secondary btn-sm d-block"><i class="bi bi-file-earmark"></i> File</a>
        <?php endif; ?>
        <div class="small text-muted mt-1" style="max-width:120px;overflow-wrap:break-word;"><?= e($a['original_name']) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if ($shipmentDocs): ?>
  <hr>
  <h6>🚚 Shipment Proof (Warehouse Uploaded)</h6>
  <div class="d-flex flex-wrap gap-3">
    <?php foreach ($shipmentDocs as $a): ?>
      <div class="text-center">
        <?php if (str_starts_with($a['mime_type'] ?? '', 'image/')): ?>
          <a href="api/attachment_download.php?id=<?= (int)$a['id'] ?>" target="_blank">
            <img src="api/attachment_download.php?id=<?= (int)$a['id'] ?>" alt="<?= e($a['original_name']) ?>" style="max-width:120px;max-height:120px;object-fit:cover;" class="border rounded">
          </a>
        <?php else: ?>
          <a href="api/attachment_download.php?id=<?= (int)$a['id'] ?>" target="_blank" class="btn btn-outline-secondary btn-sm d-block"><i class="bi bi-file-earmark"></i> File</a>
        <?php endif; ?>
        <div class="small text-muted mt-1" style="max-width:120px;overflow-wrap:break-word;"><?= e($a['original_name']) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if (user_has_permission('orders.manage') && $order['status'] === 'pending_dispatch'): ?>
  <hr>
  <div class="alert alert-info mb-3">
    <strong>🚚 Warehouse User Action Required</strong> - This order is pending dispatch. Complete the form below to mark as shipped.
  </div>
  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label"><strong>Shipment Number / Tracking ID</strong></label>
        <input type="text" name="shipment_number" class="form-control" placeholder="Enter shipment/tracking number" required>
        <div class="form-text">e.g. FDX123456789, TCS-PACK-001, JNTC-ABC-XYZ</div>
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-12">
        <label class="form-label"><strong>📸 Warehouse Upload: Shipment Proof (Barcode/Label Photo)</strong></label>
        <input type="file" name="shipment_proof" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
        <div class="form-text">Upload shipping label barcode, courier barcode, or proof of shipment photo. This proof will be attached to the order record.</div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12">
        <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-check-lg"></i> Mark as Shipped to Customer</button>
      </div>
    </div>
  </form>
  <?php endif; ?>

  <?php if ($order['status'] === 'shipped_to_customer'): ?>
  <hr>
  <div class="alert alert-success">
    <strong>Shipped to Customer</strong> on <?= e(format_date($order['updated_at'])) ?><br>
    Shipment Number: <strong><?= e($order['shipment_number']) ?></strong>
  </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
