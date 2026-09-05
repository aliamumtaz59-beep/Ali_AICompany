<?php $user = current_user(); $currentPage = basename($_SERVER['SCRIPT_NAME']); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? 'Warehouse Order Management') ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@1,700&display=swap" rel="stylesheet">
<link href="public/css/style.css" rel="stylesheet">
</head>
<body>
<div class="wrapper">
  <nav class="sidebar">
    <div class="sidebar-brand">
      <i class="bi bi-bag-fill"></i> Armadio
    </div>
    <ul class="nav flex-column">
      <li class="nav-item"><a class="nav-link<?= $currentPage==='index.php'?' active':'' ?>" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
      <li class="nav-item"><a class="nav-link<?= in_array($currentPage,['orders.php','order_form.php','order_view.php'])?' active':'' ?>" href="orders.php"><i class="bi bi-receipt"></i> Orders</a></li>
      <li class="nav-item"><a class="nav-link<?= in_array($currentPage,['products.php','product_form.php'])?' active':'' ?>" href="products.php"><i class="bi bi-box-seam"></i> Products</a></li>
      <li class="nav-item"><a class="nav-link<?= in_array($currentPage,['shops.php','shop_form.php'])?' active':'' ?>" href="shops.php"><i class="bi bi-shop"></i> Shops</a></li>
      <li class="nav-item"><a class="nav-link<?= $currentPage==='reports.php'?' active':'' ?>" href="reports.php"><i class="bi bi-bar-chart"></i> Reports</a></li>
      <?php if ($user && $user['role'] === 'admin'): ?>
      <li class="nav-item"><a class="nav-link<?= in_array($currentPage,['users.php','user_form.php'])?' active':'' ?>" href="users.php"><i class="bi bi-people"></i> Users</a></li>
      <?php endif; ?>
      <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
    </ul>
  </nav>
  <div class="content">
    <header class="topbar d-flex justify-content-between align-items-center">
      <button class="btn btn-sm btn-outline-secondary d-md-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <h5 class="mb-0"><?= e($pageTitle ?? '') ?></h5>
      <span class="text-muted"><?= e($user['name'] ?? '') ?> (<?= e($user['role'] ?? '') ?>)</span>
    </header>
    <main class="p-3">
      <?php foreach (get_flashes() as $f): ?>
        <div class="alert alert-<?= e($f['type']) ?> alert-dismissible fade show" role="alert">
          <?= e($f['message']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endforeach; ?>
