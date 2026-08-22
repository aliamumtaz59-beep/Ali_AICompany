<?php
require __DIR__ . '/includes/bootstrap.php';
$page_title = 'Your Cart | Armadio';
$page_description = 'Review items in your Armadio retail cart before checkout.';
require __DIR__ . '/includes/header.php';
?>

<?= render_page_header('B2C Retail', 'Your Cart', 'Review your items below. Checkout is a placeholder for now — real payment will be added via Stripe.') ?>

<section class="section bg-ivory">
  <div class="container" style="max-width: 72rem;" data-cart-root>

    <div class="cart-empty" data-cart-empty hidden>
      <p class="cart-empty__title">Your cart is empty</p>
      <p class="cart-empty__desc">Browse the shop and add a few items to get started.</p>
      <p style="margin-top:1.5rem;"><?= render_button('Go to Shop', '/shop.php', 'primary') ?></p>
    </div>

    <div class="cart-success" data-cart-success hidden>
      <p class="cart-success__title">Order placeholder confirmed</p>
      <p class="cart-success__desc">This is a UI-only checkout — no payment has been taken. Real checkout will be wired up via Stripe.</p>
      <p style="margin-top:1.5rem;"><?= render_button('Continue Shopping', '/shop.php', 'primary') ?></p>
    </div>

    <div class="cart-layout" data-cart-filled hidden>
      <div>
        <div class="cart-items" data-cart-items></div>
        <button type="button" class="cart-clear" data-cart-clear>Clear cart</button>
      </div>

      <div class="cart-summary">
        <h2 class="cart-summary__title">Order Summary</h2>
        <div class="cart-summary__row">
          <span>Subtotal</span>
          <strong data-cart-subtotal>£0.00</strong>
        </div>
        <p class="cart-summary__note">Shipping and taxes calculated at checkout.</p>
        <button type="button" class="cart-summary__checkout" data-cart-checkout>Proceed to Checkout</button>
        <a href="/shop.php" class="cart-summary__continue">Continue shopping</a>
      </div>
    </div>

  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
