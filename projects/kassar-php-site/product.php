<?php
require __DIR__ . '/includes/bootstrap.php';

$slug = $_GET['slug'] ?? '';
$product = get_product_by_slug($products, $slug);

if (!$product) {
    http_response_code(404);
    $page_title = 'Product not found | Armadio';
    require __DIR__ . '/includes/header.php';
    ?>
    <section class="section bg-ivory">
      <div class="container" style="text-align:center;">
        <h1 style="font-family:var(--font-display); font-size:2rem; color: var(--color-navy);">Product not found</h1>
        <p style="margin-top:1rem; color: rgba(28,23,18,0.7);">We couldn't find the product you were looking for.</p>
        <p style="margin-top:2rem;"><?= render_button('Back to shop', '/shop.php', 'primary') ?></p>
      </div>
    </section>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$image = $product['image_full'] ?? $product['image'];
$page_title = $product['name'] . ' | Armadio';
$page_description = $product['description'];
require __DIR__ . '/includes/header.php';
?>

<section class="section bg-ivory" style="padding-top: 8rem;">
  <div class="container">
    <a href="/shop.php" class="product-detail__back">← Back to shop</a>

    <div class="product-detail">
      <div class="product-detail__media">
        <img src="/<?= e($image) ?>" alt="<?= e($product['name']) ?>">
      </div>

      <div class="product-detail__info">
        <span class="product-card__category"><?= e($product['category']) ?></span>
        <h1 class="product-detail__title"><?= e($product['name']) ?></h1>

        <div class="product-detail__price">
          <span class="product-card__price-now"><?= money($product['price']) ?></span>
          <?php if (!empty($product['compareAtPrice'])): ?>
            <span class="product-card__price-was"><?= money($product['compareAtPrice']) ?></span>
          <?php endif; ?>
        </div>

        <p class="product-detail__desc"><?= e($product['description']) ?></p>

        <button
          type="button"
          class="add-to-cart-btn product-detail__cta"
          data-add-to-cart
          data-slug="<?= e($product['slug']) ?>"
          data-name="<?= e($product['name']) ?>"
          data-price="<?= e((string) $product['price']) ?>"
          data-gradient="<?= e($product['gradient']) ?>"
        >Add to cart</button>

        <a href="/shop.php" class="cart-summary__continue" style="margin-top: 1rem;">Continue shopping</a>
      </div>
    </div>
  </div>
</section>

<?= render_cta_banner('assets/images/photos/cta-banner.jpg') ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
