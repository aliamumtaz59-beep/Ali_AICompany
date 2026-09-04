<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/models/Order.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/models/Attachment.php';
require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
$order = $id ? Order::find($id) : null;
if ($id && !$order) {
    flash('danger', 'Order not found.');
    redirect('orders.php');
}

$activeProducts = Product::active();
$activeProductIds = array_column($activeProducts, 'id');

$errors = [];
$formData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $orderNumber = trim($_POST['order_number'] ?? '');
    $orderDate = $_POST['order_date'] ?? '';
    $barcodeNo = trim($_POST['barcode_no'] ?? '');
    $remarks = trim($_POST['remarks'] ?? '');
    $itemsRaw = $_POST['items'] ?? [];

    if ($orderNumber === '') $errors[] = 'Order number is required.';
    if ($orderDate === '') $errors[] = 'Order date is required.';
    if (Order::orderNumberExists($orderNumber, $id ?: null)) $errors[] = 'Order number already exists.';

    $items = [];
    $seenProducts = [];
    foreach ($itemsRaw as $line) {
        $productId = (int)($line['product_id'] ?? 0);
        $quantity = (float)($line['quantity'] ?? 0);
        $unit = trim($line['unit'] ?? '');
        $lineRemarks = trim($line['remarks'] ?? '');

        if (!$productId) continue;
        if (!in_array($productId, $activeProductIds)) {
            $errors[] = 'One of the selected products is not active.';
            continue;
        }
        if ($quantity <= 0) {
            $errors[] = 'Quantity must be greater than zero for all lines.';
            continue;
        }
        if (isset($seenProducts[$productId])) {
            // Combine duplicate product lines automatically
            $items[$seenProducts[$productId]]['quantity'] += $quantity;
            continue;
        }
        $seenProducts[$productId] = count($items);
        $items[] = ['product_id' => $productId, 'quantity' => $quantity, 'unit' => $unit ?: 'PCS', 'remarks' => $lineRemarks];
    }

    if (!$items) $errors[] = 'At least one product is required.';

    if (!$errors) {
        $header = ['order_number' => $orderNumber, 'order_date' => $orderDate, 'barcode_no' => $barcodeNo ?: null, 'remarks' => $remarks];
        if ($id) {
            Order::update($id, $header, $items);
            $orderId = $id;
            flash('success', 'Order updated successfully.');
        } else {
            $orderId = Order::create($header, $items, current_user()['id']);
            flash('success', 'Order created successfully.');
        }

        foreach (normalize_files($_FILES['attachments'] ?? []) as $file) {
            $uploadError = Attachment::upload($orderId, $file);
            if ($uploadError) flash('warning', $uploadError);
        }

        redirect('orders.php');
    }

    $formData = ['order_number' => $orderNumber, 'order_date' => $orderDate, 'barcode_no' => $barcodeNo, 'remarks' => $remarks, 'items' => $itemsRaw];
}

$displayOrder = $formData ?? ($order ? [
    'order_number' => $order['order_number'],
    'order_date' => $order['order_date'],
    'barcode_no' => $order['barcode_no'],
    'remarks' => $order['remarks'],
    'items' => $order['items'],
] : [
    'order_number' => generate_order_number(db()),
    'order_date' => date('Y-m-d'),
    'barcode_no' => '',
    'remarks' => '',
    'items' => [['product_id' => '', 'quantity' => '', 'unit' => '', 'remarks' => '']],
]);

$pageTitle = $id ? 'Edit Order' : 'New Order';
require __DIR__ . '/includes/header.php';
?>

<div class="stat-card">
  <?php foreach ($errors as $err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post" id="orderForm" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= (int)$id ?>">
    <div class="row g-3 mb-3">
      <div class="col-md-4">
        <label class="form-label">Order Number</label>
        <input type="text" name="order_number" id="orderNumberInput" class="form-control" required value="<?= e($displayOrder['order_number']) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Order Date</label>
        <input type="date" name="order_date" class="form-control" required value="<?= e($displayOrder['order_date']) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Remarks</label>
        <input type="text" name="remarks" class="form-control" value="<?= e($displayOrder['remarks']) ?>">
      </div>
    </div>
    <div class="row g-3 mb-3">
      <div class="col-md-4">
        <label class="form-label">Barcode No</label>
        <input type="text" name="barcode_no" class="form-control" placeholder="Scan or type barcode" value="<?= e($displayOrder['barcode_no'] ?? '') ?>">
      </div>
      <div class="col-md-8">
        <label class="form-label">Attachments (support file / image of order)</label>
        <input type="file" name="attachments[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt">
        <div class="form-text">Max 10MB per file. Allowed: images, PDF, Word, Excel, text files.</div>
      </div>
    </div>

    <?php if ($id): $existingAttachments = Attachment::forOrder($id); if ($existingAttachments): ?>
    <div class="mb-3">
      <label class="form-label d-block">Existing Attachments</label>
      <ul class="list-group" style="max-width:500px;">
        <?php foreach ($existingAttachments as $a): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <a href="api/attachment_download.php?id=<?= (int)$a['id'] ?>" target="_blank"><?= e($a['original_name']) ?></a>
          <form method="post" action="attachment_delete.php" class="d-inline">
            <?= csrf_field() ?>
            <input type="hidden" name="attachment_id" value="<?= (int)$a['id'] ?>">
            <input type="hidden" name="order_id" value="<?= (int)$id ?>">
            <button type="submit" class="btn btn-sm btn-outline-danger confirm-delete"><i class="bi bi-trash"></i></button>
          </form>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; endif; ?>

    <table class="table" id="itemsTable">
      <thead><tr><th>Product</th><th>Quantity</th><th>Unit</th><th>Remarks</th><th></th></tr></thead>
      <tbody id="orderItemsBody">
        <?php foreach ($displayOrder['items'] as $idx => $item): ?>
        <tr>
          <td>
            <select class="form-select product-select" name="items[<?= $idx ?>][product_id]" required>
              <option value="">Select product</option>
              <?php foreach ($activeProducts as $p): ?>
                <option value="<?= (int)$p['id'] ?>" data-unit="<?= e($p['unit']) ?>" <?= ($item['product_id'] ?? '')==$p['id']?'selected':'' ?>><?= e($p['product_code']) ?> - <?= e($p['product_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </td>
          <td><input type="number" step="0.01" min="0.01" class="form-control" name="items[<?= $idx ?>][quantity]" required value="<?= e((string)($item['quantity'] ?? '')) ?>"></td>
          <td><input type="text" class="form-control unit-input" name="items[<?= $idx ?>][unit]" required value="<?= e($item['unit'] ?? '') ?>"></td>
          <td><input type="text" class="form-control" name="items[<?= $idx ?>][remarks]" value="<?= e($item['remarks'] ?? '') ?>"></td>
          <td><button type="button" class="btn btn-sm btn-danger remove-line"><i class="bi bi-trash"></i></button></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="addLineBtn"><i class="bi bi-plus"></i> Add Product</button>
    <br>
    <button type="submit" class="btn btn-primary">Save Order</button>
    <a href="orders.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<script>
var activeProducts = <?= json_encode(array_map(fn($p) => ['id'=>$p['id'],'product_code'=>$p['product_code'],'product_name'=>$p['product_name'],'unit'=>$p['unit']], $activeProducts)) ?>;
document.getElementById('addLineBtn').addEventListener('click', function () {
  addOrderLine(activeProducts);
});
<?php if (!$id): ?>
document.querySelector('input[name="order_date"]').addEventListener('change', function () {
  fetch('api/get_order_number.php?date=' + encodeURIComponent(this.value))
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (data.order_number) document.querySelector('input[name="order_number"]').value = data.order_number;
    });
});
<?php endif; ?>
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
