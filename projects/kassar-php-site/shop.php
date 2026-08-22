<?php
require __DIR__ . '/includes/bootstrap.php';
$page_title = 'Shop | Armadio';
$page_description = "Shop retail quantities of Armadio's vetted brands — power tools, beauty, grocery and lifestyle goods, delivered to your door.";
require __DIR__ . '/includes/header.php';
?>

<?= render_page_header('B2C Retail', 'Shop the Armadio collection', 'The same vetted brands sold wholesale, now available in everyday retail quantities with straightforward checkout.', 'assets/images/photos/hero-shop.jpg') ?>

<section class="section bg-ivory">
  <div class="container">
    <div class="grid grid--4">
      <?php foreach ($products as $product): ?>
        <div class="reveal">
          <div class="product-card">
            <div class="product-card__media">
              <img src="/<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
            </div>
            <div class="product-card__body">
              <span class="product-card__category"><?= e($product['category']) ?></span>
              <h3 class="product-card__title"><?= e($product['name']) ?></h3>
              <p class="product-card__desc"><?= e($product['description']) ?></p>
              <div class="product-card__price">
                <span class="product-card__price-now"><?= money($product['price']) ?></span>
                <?php if (!empty($product['compareAtPrice'])): ?>
                  <span class="product-card__price-was"><?= money($product['compareAtPrice']) ?></span>
                <?php endif; ?>
              </div>
              <div class="product-card__action">
                <button
                  type="button"
                  class="add-to-cart-btn"
                  data-add-to-cart
                  data-slug="<?= e($product['slug']) ?>"
                  data-name="<?= e($product['name']) ?>"
                  data-price="<?= e((string) $product['price']) ?>"
                  data-gradient="<?= e($product['gradient']) ?>"
                >Add to cart</button>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
