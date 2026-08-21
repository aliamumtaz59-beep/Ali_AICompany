<?php
require __DIR__ . '/includes/bootstrap.php';

$slug = $_GET['slug'] ?? '';
$brand = get_brand_by_slug($brands, $slug);

if (!$brand) {
    http_response_code(404);
    $page_title = 'Brand not found | Kassar';
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

$page_title = $brand['name'] . ' | Kassar';
$page_description = $brand['description'];
require __DIR__ . '/includes/header.php';
?>

<section class="brand-hero">
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

      <?php if (!empty($brand['image'])): ?>
        <div class="brand-hero__media">
          <div class="brand-hero__glow <?= e($brand['color']) ?>"></div>
          <div class="brand-hero__frame">
            <img src="/<?= e($brand['image']) ?>" alt="<?= e($brand['name']) ?>">
          </div>
        </div>
      <?php else: ?>
        <div class="brand-hero__fallback">
          <div class="brand-hero__fallback-ping <?= e($brand['color']) ?>"></div>
          <div class="brand-hero__fallback-dashed"></div>
          <span class="brand-hero__fallback-initials <?= e($brand['color']) ?>"><?= e($brand['initials']) ?></span>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="cta-banner">
  <div class="cta-banner__inner reveal">
    <h2 class="cta-banner__title">Ready to <span class="italic text-amber-light">trade</span> with Kassar?</h2>
    <p class="cta-banner__desc">Tell us whether you're buying in bulk, shopping retail, or looking to supply your own brand — we'll route you to the right team within one business day.</p>
    <div class="cta-banner__actions">
      <?= render_button('Contact Us', '/contact.php', 'primary') ?>
      <?= render_button('Supply to Kassar', '/supply-to-us.php', 'secondary') ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
