<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/models/Shop.php';
require_permission('shops.manage');

$id = isset($_GET['id']) ? id_decode($_GET['id']) : (isset($_POST['id']) ? id_decode($_POST['id']) : 0);
$shop = $id ? Shop::find($id) : null;
if ($id && !$shop) {
    flash('danger', 'Shop not found.');
    redirect('shops.php');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $data = [
        'shop_name' => trim($_POST['shop_name'] ?? ''),
        'owner_name' => trim($_POST['owner_name'] ?? ''),
        'contact_number' => trim($_POST['contact_number'] ?? ''),
        'status' => $_POST['status'] ?? 'active',
    ];

    if ($data['shop_name'] === '') $errors[] = 'Shop name is required.';

    if (!$errors) {
        if ($id) {
            Shop::update($id, $data);
            flash('success', 'Shop updated successfully.');
        } else {
            Shop::create($data);
            flash('success', 'Shop created successfully.');
        }
        redirect('shops.php');
    }
    $shop = array_merge($shop ?? [], $data);
}

$pageTitle = $id ? 'Edit Shop' : 'Add Shop';
require __DIR__ . '/includes/header.php';
?>

<div class="stat-card" style="max-width:600px;">
  <?php foreach ($errors as $err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= e($id ? id_encode($id) : '') ?>">
    <div class="mb-3">
      <label class="form-label">Shop Name</label>
      <input type="text" name="shop_name" class="form-control" required value="<?= e($shop['shop_name'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Owner Name</label>
      <input type="text" name="owner_name" class="form-control" value="<?= e($shop['owner_name'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Contact Number</label>
      <input type="text" name="contact_number" class="form-control" value="<?= e($shop['contact_number'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
        <option value="active" <?= ($shop['status'] ?? 'active')==='active'?'selected':'' ?>>Active</option>
        <option value="inactive" <?= ($shop['status'] ?? '')==='inactive'?'selected':'' ?>>Inactive</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Save Shop</button>
    <a href="shops.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
