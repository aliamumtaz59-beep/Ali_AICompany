<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/models/Shop.php';
require_permission('products.manage');

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
        'quantity_pcs' => (float)($_POST['quantity_pcs'] ?? 0),
        'status' => $_POST['status'] ?? 'active',
    ];

    if ($data['product_code'] === '') $errors[] = 'Product code is required.';
    if ($data['product_name'] === '') $errors[] = 'Product name is required.';
    if ($data['unit'] === '') $errors[] = 'Unit is required.';
    if ($data['quantity_pcs'] < 0) $errors[] = 'Quantity (PCS) cannot be negative.';
    if ($data['product_code'] && Product::codeExists($data['product_code'], $id ?: null)) {
        $errors[] = 'Product code already exists.';
    }

    if (!$errors) {
        $imagePath = $product['image_path'] ?? null;

        if (isset($_POST['remove_image'])) {
            Product::deleteImageFile($imagePath);
            $imagePath = null;
        }

        if (!empty($_FILES['image']['name'])) {
            $result = Product::uploadImage($_FILES['image']);
            if ($result['error']) {
                $errors[] = $result['error'];
            } else {
                Product::deleteImageFile($imagePath);
                $imagePath = $result['path'];
            }
        }

        $data['image_path'] = $imagePath;
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
  <form method="post" enctype="multipart/form-data">
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
      <label class="form-label">Quantity (PCS)</label>
      <input type="number" step="0.01" min="0" name="quantity_pcs" class="form-control" required value="<?= e((string)($product['quantity_pcs'] ?? '0')) ?>">
      <div class="form-text">Total stock quantity for this product, in PCS.</div>
    </div>
    <?php if ($id): ?>
    <div class="mb-3">
      <label class="form-label">Remaining Quantity (PCS)</label>
      <input type="text" class="form-control" value="<?= e(number_format((float)($product['remaining_qty'] ?? 0), 2)) ?>" disabled>
      <div class="form-text">Calculated automatically: Quantity (PCS) minus everything ordered so far.</div>
    </div>
    <?php endif; ?>
    <div class="mb-3">
      <label class="form-label">Product Image</label>
      <?php if (!empty($product['image_path'])): ?>
        <div class="mb-2">
          <img src="<?= e($product['image_path']) ?>" alt="" style="width:100px;height:100px;object-fit:cover;" class="border rounded d-block mb-1">
          <div class="form-check">
            <input type="checkbox" name="remove_image" value="1" class="form-check-input" id="removeImage">
            <label class="form-check-label" for="removeImage">Remove current image</label>
          </div>
        </div>
      <?php endif; ?>
      <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
      <div class="form-text">Max 5MB. JPG, PNG, GIF, or WEBP.</div>
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
