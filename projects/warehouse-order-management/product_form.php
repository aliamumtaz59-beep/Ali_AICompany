<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/models/Shop.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
$product = $id ? Product::find($id) : null;
if ($id && !$product) {
    flash('danger', 'Product not found.');
    redirect('products.php');
}

$activeShops = Shop::active();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $data = [
        'product_code' => trim($_POST['product_code'] ?? ''),
        'product_name' => trim($_POST['product_name'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'unit' => trim($_POST['unit'] ?? ''),
        'shop_id' => ((int)($_POST['shop_id'] ?? 0)) ?: null,
        'status' => $_POST['status'] ?? 'active',
    ];

    if ($data['product_code'] === '') $errors[] = 'Product code is required.';
    if ($data['product_name'] === '') $errors[] = 'Product name is required.';
    if ($data['unit'] === '') $errors[] = 'Unit is required.';
    if ($data['product_code'] && Product::codeExists($data['product_code'], $id ?: null)) {
        $errors[] = 'Product code already exists.';
    }

    if (!$errors) {
        if ($id) {
            Product::update($id, $data);
            flash('success', 'Product updated successfully.');
        } else {
            Product::create($data);
            flash('success', 'Product created successfully.');
        }
        redirect('products.php');
    }
    $product = array_merge($product ?? [], $data);
}

$pageTitle = $id ? 'Edit Product' : 'Add Product';
require __DIR__ . '/includes/header.php';
?>

<div class="stat-card" style="max-width:600px;">
  <?php foreach ($errors as $err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= (int)$id ?>">
    <div class="mb-3">
      <label class="form-label">Product Code / SKU</label>
      <input type="text" name="product_code" class="form-control" required value="<?= e($product['product_code'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Product Name</label>
      <input type="text" name="product_name" class="form-control" required value="<?= e($product['product_name'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-control" rows="3"><?= e($product['description'] ?? '') ?></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Unit</label>
      <input type="text" name="unit" class="form-control" required value="<?= e($product['unit'] ?? 'PCS') ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Product Owner (Shop)</label>
      <select name="shop_id" class="form-select">
        <option value="">None</option>
        <?php foreach ($activeShops as $s): ?>
          <option value="<?= (int)$s['id'] ?>" <?= ($product['shop_id'] ?? '')==$s['id']?'selected':'' ?>><?= e($s['shop_name']) ?><?= $s['owner_name'] ? ' - ' . e($s['owner_name']) : '' ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
        <option value="active" <?= ($product['status'] ?? 'active')==='active'?'selected':'' ?>>Active</option>
        <option value="inactive" <?= ($product['status'] ?? '')==='inactive'?'selected':'' ?>>Inactive</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Save Product</button>
    <a href="products.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
