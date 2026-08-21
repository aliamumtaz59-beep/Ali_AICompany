<?php
require __DIR__ . '/includes/bootstrap.php';
$page_title = 'Categories | Kassar';
$page_description = 'Discover the trading categories Kassar specialises in, from power tools to beauty, grocery and lifestyle goods.';
require __DIR__ . '/includes/header.php';
?>

<?= render_page_header('What we trade', 'Categories built for bulk and retail', 'Each category is backed by vetted manufacturers and brands, with flexible order sizes for wholesale accounts and retail shoppers alike.') ?>

<section class="section bg-ivory">
  <div class="container" style="max-width: 72rem;">
    <?php foreach ($categories as $i => $category): ?>
      <div class="reveal">
        <div id="<?= e($category['slug']) ?>" class="category-row<?= $i % 2 === 1 ? ' category-row--reverse' : '' ?>">
          <div class="category-row__media <?= e($category['gradient']) ?>">
            <span class="category-row__stat"><?= e($category['stat']['value']) ?> <?= e($category['stat']['label']) ?></span>
          </div>
          <div class="category-row__content">
            <span class="category-row__eyebrow">Category <?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
            <h2 class="category-row__title"><?= e($category['name']) ?></h2>
            <p class="category-row__tagline"><?= e($category['tagline']) ?></p>
            <p class="category-row__desc"><?= e($category['description']) ?></p>
            <div class="category-row__actions">
              <?= render_button('Buy in Bulk', '/buy-in-bulk.php', 'ghost') ?>
              <?= render_button('Shop Retail', '/shop.php', 'navy-outline') ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
