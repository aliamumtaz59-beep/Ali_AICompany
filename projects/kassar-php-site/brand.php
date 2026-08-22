<?php
require __DIR__ . '/includes/bootstrap.php';

$slug = $_GET['slug'] ?? '';
$brand = get_brand_by_slug($brands, $slug);

if (!$brand) {
    http_response_code(404);
    $page_title = 'Brand not found | Armadio';
    require __DIR__ . '/includes/header.php';
    ?>
    <section class="section bg-ivory">
      <div class="container" style="text-align:center;">
        <h1 style="font-family:var(--font-display); font-size:2rem; color: var(--color-navy);">Brand not found</h1>
        <p style="margin-top:1rem; color: rgba(22,32,44,0.7);">We couldn't find the brand you were looking for.</p>
        <p style="margin-top:2rem;"><?= render_button('Back to all brands', '/brands.php', 'primary') ?></p>
      </div>
    </section>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$page_title = $brand['name'] . ' | Armadio';
$page_description = $brand['description'];
require __DIR__ . '/includes/header.php';
?>

<section class="brand-hero">
  <div class="brand-hero__photo"<?= photo_style($brand['photo']) ?>></div>
  <div class="brand-hero__inner">
    <a href="/brands.php" class="brand-hero__back">← Back to all brands</a>

    <div class="brand-hero__grid">
      <div>
        <span class="brand-hero__badge"><?= e($brand['category']) ?></span>
        <h1 class="brand-hero__title"><?= e($brand['name']) ?></h1>
        <p class="brand-hero__desc"><?= e($brand['description']) ?></p>
        <div class="brand-hero__facts">
          <div>
            <span class="brand-hero__fact-label">Origin</span>
            <span class="brand-hero__fact-value"><?= e($brand['origin']) ?></span>
          </div>
          <div>
            <span class="brand-hero__fact-label">MOQ</span>
            <span class="brand-hero__fact-value"><?= e($brand['moq']) ?></span>
          </div>
        </div>
        <div class="brand-hero__actions">
          <?= render_button('Request Bulk Quote', '/buy-in-bulk.php?brand=' . urlencode($brand['name']), 'primary') ?>
          <?= render_button('Contact Sales', '/contact.php', 'secondary') ?>
        </div>
      </div>

      <div class="brand-hero__media">
        <div class="brand-hero__frame photo <?= e(category_duotone($brand['category'])) ?>"<?= photo_style($brand['photo']) ?>>
          <span class="brand-hero__mark <?= e($brand['color']) ?>"><?= e($brand['initials']) ?></span>
        </div>
      </div>
    </div>
  </div>
</section>

<?= render_cta_banner('assets/images/photos/cta-banner.jpg') ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
